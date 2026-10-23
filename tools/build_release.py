"""Build a private cPanel new-install archive without copying runtime data."""
from pathlib import Path, PurePosixPath
from zipfile import ZipFile, ZIP_DEFLATED
import argparse, secrets, html

def build(output):
    repo=Path(__file__).resolve().parents[1];source=repo/'src'
    required=['index.php','router.php','.htaccess','config/.htaccess','config/example.php']
    if not all((source/x).is_file() for x in required):
        raise ValueError('The source tree is incomplete.')
    key=secrets.token_hex(32)
    output=Path(output).resolve();output.parent.mkdir(parents=True,exist_ok=True)
    if output.exists():raise FileExistsError('Choose a new output name; the existing archive was preserved.')
    content={}
    for path in sorted(source.rglob('*')):
        if not path.is_file():continue
        if path.is_symlink():raise ValueError('Source symlinks are not included in a release.')
        name=path.relative_to(source).as_posix();parts=PurePosixPath(name).parts
        if '__pycache__' in parts or path.suffix in {'.pyc','.log','.sqlite','.sqlite3','.db'}:continue
        if parts[0]=='config' and name not in {'config/.htaccess','config/example.php'}:continue
        if parts[0]=='storage' and path.name!='.htaccess':continue
        if name in {'START_HERE.html','START_HERE_PRIVATE.html'}:continue
        content[name]=path.read_bytes()
    content['config/setup.key']=(key+'\n').encode()
    for folder in ['cache','logs','sessions','uploads']:
        content.setdefault('storage/'+folder+'/.htaccess',b'Require all denied\n')
    start='<!doctype html><meta charset="utf-8"><title>CivicFlow installation</title><main style="max-width:800px;margin:60px auto;font:18px/1.6 Arial"><h1>CivicFlow 1.0.4 installation</h1><p>Read this file locally. Upload and extract this application archive into a new cPanel document root, retain all .htaccess files and open /install.</p><p>Private setup key</p><pre style="overflow-wrap:anywhere;white-space:pre-wrap">'+html.escape(key)+'</pre><p>Create your own administrator account and use a fresh empty database. Keep config/local.php, config/installed.lock and private uploads when updating an existing installation. The application does not require reinstallation for this documentation revision.</p><p>Remove the uploaded archive and this start file from the hosting directory after setup.</p></main>'
    content['START_HERE.html']=start.encode()
    with ZipFile(output,'x',compression=ZIP_DEFLATED,compresslevel=9) as z:
        for name,data in content.items():z.writestr(name,data)
    with ZipFile(output) as z:
        if z.testzip() is not None:raise ValueError('Archive CRC validation failed.')
        if not all(x in z.namelist() for x in required):raise ValueError('Required application file missing.')
        if any('..' in PurePosixPath(x).parts or PurePosixPath(x).is_absolute() for x in z.namelist()):raise ValueError('Unsafe archive path.')
    print('Built '+str(output))
    print('The unique setup key is inside START_HERE.html and config/setup.key in that private archive.')
    return output

def main():
    p=argparse.ArgumentParser();p.add_argument('--output',default=str(Path(__file__).resolve().parents[1]/'dist/CivicFlow_cPanel_Complete.zip'))
    build(p.parse_args().output)
if __name__=='__main__':main()
