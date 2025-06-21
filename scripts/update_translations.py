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
        if re.search(r'[{}<>$]|\\?php|;|=', text):
            continue
        if not re.search(r'[A-Za-zÁÉÍÓÚáéíóúñÑ]', text):
            continue
        strings.add(text)
    return strings


def load_catalog(path: str) -> dict:
    try:
        with open(path, 'r', encoding='utf-8') as f:
            return json.load(f)
    except FileNotFoundError:
        return {}


def save_catalog(path: str, data: dict) -> None:
    with open(path, 'w', encoding='utf-8') as f:
        json.dump(data, f, ensure_ascii=False, indent=2)


def main() -> None:
    es = load_catalog('translations/es.json')
    en = load_catalog('translations/en.json')
    gl = load_catalog('translations/gl.json')

    all_strings: set[str] = set()
    for file in gather_files('.'):
        all_strings.update(extract_strings(file))

    added = 0
    for s in sorted(all_strings):
        if s not in es:
            es[s] = s
            en[s] = ""
            gl[s] = ""
            added += 1

    os.makedirs('translations', exist_ok=True)
    save_catalog('translations/es.json', dict(sorted(es.items())))
    save_catalog('translations/en.json', dict(sorted(en.items())))
    save_catalog('translations/gl.json', dict(sorted(gl.items())))

    print(f"Appended {added} new strings.")


if __name__ == '__main__':
    main()
