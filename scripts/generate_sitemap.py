import os
import xml.etree.ElementTree as ET
from xml.dom import minidom
from datetime import datetime
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

# Base URL used in each entry of the sitemap. Can be overridden with an
# environment variable for local testing or different deployments.
BASE_URL = os.getenv('BASE_URL', 'https://condadodecastilla.com')

EXCLUDE_DIRS = {
    'scripts', 'dashboard', '__pycache__', 'assets', 'uploads',
    'database_setup', '.git', 'fragments'
}

FILE_EXTENSIONS = {'.html', '.php'}


def is_eligible(filename: str) -> bool:
    return os.path.splitext(filename)[1].lower() in FILE_EXTENSIONS


def collect_files(base_dir: str) -> list[str]:
    collected = []
    for root, dirs, files in os.walk(base_dir):
        # Skip unwanted directories and files
        # Exclude directories listed in EXCLUDE_DIRS or starting with a dot.
        # Ignore files starting with '_' and anything under the 'fragments' folder.
        dirs[:] = [d for d in dirs if d not in EXCLUDE_DIRS and not d.startswith('.')]
        for file in files:
            if is_eligible(file) and not file.startswith('_'):
                full_path = os.path.join(root, file)
                rel_path = os.path.relpath(full_path, base_dir)
                collected.append(rel_path.replace(os.sep, '/'))
    return sorted(collected)


def build_sitemap(paths: list[str]) -> ET.ElementTree:
    urlset = ET.Element('urlset', xmlns="http://www.sitemaps.org/schemas/sitemap/0.9")
    today = datetime.utcnow().date().isoformat()
    for path in paths:
        url = ET.SubElement(urlset, 'url')
        loc = ET.SubElement(url, 'loc')
        loc.text = f"{BASE_URL}/{path}"
        lastmod = ET.SubElement(url, 'lastmod')
        lastmod.text = today
    return ET.ElementTree(urlset)


def write_pretty_xml(tree: ET.ElementTree, filepath: str) -> None:
    rough = ET.tostring(tree.getroot(), 'utf-8')
    reparsed = minidom.parseString(rough)
    with open(filepath, 'w', encoding='utf-8') as f:
        f.write(reparsed.toprettyxml(indent="  "))


def main() -> None:
    paths = collect_files('.')
    tree = build_sitemap(paths)
    write_pretty_xml(tree, 'sitemap.xml')
    logger.info("Generated sitemap with %d entries.", len(paths))


if __name__ == '__main__':
    main()
