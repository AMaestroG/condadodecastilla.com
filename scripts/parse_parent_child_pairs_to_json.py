import re, json
from pathlib import Path
import logging


def configure_logger() -> logging.Logger:
    """Return a logger configured once for this module."""
    logger = logging.getLogger(__name__)
    if not logger.handlers:
        logging.basicConfig(
            level=logging.INFO,
            format="%(asctime)s - %(name)s - %(levelname)s - %(message)s",
        )
    return logger


logger = configure_logger()

table_path = Path('docs/parent_child_pairs.md')
output_path = Path('personajes/parent_child_pairs.json')

pattern = re.compile(r"\|\s*(.*?)\s*\|\s*(.*?)\s*\|")
link_re = re.compile(r"\[(.*?)\]\((.*?)\)")

pairs = []
for line in table_path.read_text(encoding='utf-8').splitlines():
    m = pattern.match(line)
    if m:
        parent_cell, child_cell = m.groups()
        # Skip header and separator rows
        if parent_cell.lower().startswith('progenitor') or set(parent_cell) <= {'-', ' '}:
            continue
        def parse(cell):
            link_match = link_re.match(cell)
            if link_match:
                return link_match.group(1), link_match.group(2)
            return cell, None
        parent, parent_link = parse(parent_cell)
        child, child_link = parse(child_cell)
        pairs.append({
            'parent': parent,
            'parent_link': parent_link,
            'child': child,
            'child_link': child_link
        })

output_path.write_text(json.dumps(pairs, ensure_ascii=False, indent=2), encoding='utf-8')
logger.info("Wrote %s with %d records", output_path, len(pairs))
