import os
import re
import json
from bs4 import BeautifulSoup

EXCLUDE_DIRS = {"tests", "assets"}
FILE_EXTENSIONS = {".php", ".html"}

EDITABLE_RE = re.compile(r"editableText\(\s*['\"]([^'\"]+)['\"],\s*\$pdo,\s*['\"]([^'\"]*)['\"]")
PHP_BLOCK_RE = re.compile(r"<\?php.*?\?>", re.DOTALL)


def gather_files(base_dir: str) -> list[str]:
    collected = []
    for root, dirs, files in os.walk(base_dir):
        dirs[:] = [d for d in dirs if d not in EXCLUDE_DIRS and not d.startswith('.')]
        for file in files:
            if os.path.splitext(file)[1].lower() in FILE_EXTENSIONS:
                collected.append(os.path.join(root, file))
    return collected


def extract_strings(path: str) -> set[str]:
    strings: set[str] = set()
    try:
        with open(path, 'r', encoding='utf-8') as f:
            content = f.read()
    except Exception:
        return strings

    for _key, value in EDITABLE_RE.findall(content):
        cleaned = value.strip()
        if cleaned:
            strings.add(cleaned)

    html = PHP_BLOCK_RE.sub('', content)
    soup = BeautifulSoup(html, 'html.parser')
    for element in soup.find_all(string=True):
        if element.parent.name in {'script', 'style'}:
            continue
        text = element.strip()
        if not text:
            continue
        # Skip fragments that look like code or markup
        if re.search(r'[{}<>$]|\\?php|;|=', text):
            continue
        if not re.search(r'[A-Za-zÁÉÍÓÚáéíóúñÑ]', text):
            continue
        strings.add(text)
    return strings


def main() -> None:
    all_strings: set[str] = set()
    for file in gather_files('.'):
        all_strings.update(extract_strings(file))

    os.makedirs('translations', exist_ok=True)
    ordered = sorted(all_strings)

    es = {s: s for s in ordered}
    en = {s: "" for s in ordered}
    gl = {s: "" for s in ordered}

    with open('translations/es.json', 'w', encoding='utf-8') as f:
        json.dump(es, f, ensure_ascii=False, indent=2)
    with open('translations/en.json', 'w', encoding='utf-8') as f:
        json.dump(en, f, ensure_ascii=False, indent=2)
    with open('translations/gl.json', 'w', encoding='utf-8') as f:
        json.dump(gl, f, ensure_ascii=False, indent=2)

    print(f"Extracted {len(all_strings)} unique strings.")


if __name__ == '__main__':
    main()
