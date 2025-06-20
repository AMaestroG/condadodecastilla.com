import os
import re
import json
import unicodedata
from bs4 import BeautifulSoup


def normalize(name: str) -> str:
    norm = unicodedata.normalize('NFKD', name)
    norm = norm.encode('ascii', 'ignore').decode('ascii')
    return re.sub(r'[^a-z0-9]+', '', norm.lower())

# Load mapping from names to web paths and file paths
with open('assets/data/characters_enriched.json', encoding='utf-8') as f:
    characters = json.load(f)

name_to_web = {}
file_to_name = {}
for ch in characters:
    key = normalize(ch['name'])
    name_to_web[key] = ch['web_path']
    file_path = ch['file_path'].lstrip('/')
    if file_path.startswith('app/'):
        file_path = file_path[len('app/'):]  # remove 'app/' prefix
    file_to_name[file_path] = ch['name']

# Patterns for parent-child relationships
name_part = r"[A-ZÁÉÍÓÚÜÑa-záéíóúüñ'() ]+"
patterns = {
    'parent': re.compile(rf'(?:Padre|Madre|El padre|La madre) de\s+({name_part})', re.IGNORECASE),
    'child': re.compile(rf'Hij[oa] de\s+({name_part})', re.IGNORECASE),
}

pairs = []

for file_path, name in file_to_name.items():
    if not os.path.exists(file_path):
        continue
    with open(file_path, encoding='utf-8') as f:
        html = f.read()
    soup = BeautifulSoup(html, 'html.parser')

    for li in soup.find_all('li'):
        text = li.get_text(' ', strip=True)
        for m in patterns['parent'].findall(text):
            child = re.split(r',|\s+segun\b|\s+seg\xC3\xBAn\b', m, 1, flags=re.IGNORECASE)[0].strip().rstrip('.').rstrip(')')
            if 0 < len(child.split()) <= 6 and not child.lower().startswith('sus '):
                pairs.append((name, child))
        for m in patterns['child'].findall(text):
            parent = re.split(r',|\s+segun\b|\s+seg\xC3\xBAn\b', m, 1, flags=re.IGNORECASE)[0].strip().rstrip('.').rstrip(')')
            if 0 < len(parent.split()) <= 6 and not parent.lower().startswith('sus '):
                pairs.append((parent, name))

# Deduplicate pairs
seen = set()
unique_pairs = []
for parent, child in pairs:
    key = (normalize(parent), normalize(child))
    if key not in seen and key[0] != key[1]:
        seen.add(key)
        unique_pairs.append((parent, child))

# Prepare Markdown table
lines = ["| Progenitor | Descendiente |", "|-----------|--------------|"]
for parent, child in sorted(unique_pairs):
    p_path = name_to_web.get(normalize(parent))
    c_path = name_to_web.get(normalize(child))
    parent_md = f"[{parent}]({p_path})" if p_path else parent
    child_md = f"[{child}]({c_path})" if c_path else child
    lines.append(f"| {parent_md} | {child_md} |")

output_path = os.path.join('docs', 'parent_child_pairs.md')
with open(output_path, 'w', encoding='utf-8') as f:
    f.write("# Relaciones de Parentesco\n\n")
    f.write("Lista de pares padre/madre e hijo/a extraídos de las biografías.\n\n")
    f.write("\n".join(lines))
    f.write("\n")

print(f"Generated {output_path} with {len(unique_pairs)} pairs.")
