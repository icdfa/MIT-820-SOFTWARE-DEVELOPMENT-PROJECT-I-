"""Check local documentation links, media and the source packaging boundary."""
from pathlib import Path
import re,json,sys
from urllib.parse import unquote,urlsplit
from html.parser import HTMLParser

ROOT=Path(__file__).resolve().parents[1]
errors=[];links=0;images=0
def check(path,target,image=False):
    global links,images
    target=target.strip().split(' "',1)[0].strip('<>')
    if not target or target.startswith(('#','data:')):return
    if urlsplit(target).scheme:return
    target=unquote(target.split('#',1)[0].split('?',1)[0])
    if not target:return
    resolved=(path.parent/target).resolve()
    if not resolved.is_relative_to(ROOT):errors.append(str(path.relative_to(ROOT))+': path escapes repository: '+target)
    elif not resolved.exists():errors.append(str(path.relative_to(ROOT))+': missing '+target)
    links+=1;images+=int(image)
class Parser(HTMLParser):
    def __init__(self,path):super().__init__();self.path=path
    def handle_starttag(self,tag,attrs):
        for key,value in attrs:
            if key in {'href','src'} and value:check(self.path,value,tag=='img')
for path in sorted(ROOT.rglob('*')):
    if not path.is_file() or any(x in path.relative_to(ROOT).parts for x in ['.git','dist','node_modules']):continue
    if path.suffix=='.md':
        text=path.read_text();text=re.sub(r'```.*?```','',text,flags=re.S)
        for match in re.finditer(r'(!?)\[[^\]]*\]\(([^)]+)\)',text):check(path,match.group(2),bool(match.group(1)))
        Parser(path).feed(text)
    elif path.suffix=='.html' and not str(path.relative_to(ROOT)).startswith('src/'):
        Parser(path).feed(path.read_text())
for name in ['src/config/local.php','src/config/installed.lock','src/config/setup.key']:
    if (ROOT/name).exists():errors.append('Private installation state must remain outside the source upload: '+name)
for name in ['src/index.php','src/.htaccess','src/router.php','.github/workflows/ci.yml','README.md','docs/COURSE_ALIGNMENT.md','docs/project-management/sprint-backlog.csv']:
    if not (ROOT/name).is_file():errors.append('Required file missing: '+name)
result={'local_links_checked':links,'image_links_checked':images,'errors':errors}
print(json.dumps(result,indent=2))
if errors:sys.exit(1)
