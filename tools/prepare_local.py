"""Create a private setup key for an uninstalled local source copy."""
from pathlib import Path
import secrets, os

def main():
    root=Path(__file__).resolve().parents[1]/'src'
    if (root/'config/local.php').exists() or (root/'config/installed.lock').exists():
        raise SystemExit('An installation already exists. Its configuration and lock were preserved.')
    keyfile=root/'config/setup.key'
    if keyfile.exists():
        raise SystemExit('A setup key already exists. Read src/config/setup.key locally; it was preserved.')
    keyfile.parent.mkdir(parents=True,exist_ok=True)
    with keyfile.open('x') as stream:
        stream.write(secrets.token_hex(32)+'\n')
    os.chmod(keyfile,0o600)
    print('Created src/config/setup.key. Read it locally and keep it outside Git.')
    print('Next: cd src, then php -S 127.0.0.1:8080 router.php')

if __name__=='__main__':main()
