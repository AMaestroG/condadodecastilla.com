import os
import sys

# Ensure script and project root are in the Python path
SCRIPT_DIR = os.path.dirname(os.path.abspath(__file__))
ROOT_DIR = os.path.dirname(SCRIPT_DIR)
if SCRIPT_DIR not in sys.path:
    sys.path.append(SCRIPT_DIR)
if ROOT_DIR not in sys.path:
    sys.path.append(ROOT_DIR)

from process_characters import main as process_characters_main
from generate_sitemap import main as generate_sitemap_main

try:
    from consistency_analyzer import ConsistencyAnalyzer
    from graph_db_interface import GraphDBInterface
except ImportError as e:
    raise ImportError(f"Could not import dependencies: {e}")


def main() -> None:
    """Runs maintenance tasks for the site."""
    process_characters_main()
    generate_sitemap_main()

    db = GraphDBInterface()
    analyzer = ConsistencyAnalyzer(db)
    analyzer.analyze_graph_consistency()


if __name__ == "__main__":
    main()
