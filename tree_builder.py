import requests
from bs4 import BeautifulSoup
from urllib.parse import urlparse, urljoin
import json
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

def build_tree(url, visited_urls=None, max_depth=2, current_depth=0):
    """
    Recursively fetches HTML content, parses title and internal links,
    and builds a tree structure.

    Args:
        url (str): The URL to fetch and parse.
        visited_urls (set, optional): Set of already visited URLs. Defaults to None.
        max_depth (int, optional): Maximum depth for recursion. Defaults to 2.
        current_depth (int, optional): Current depth in recursion. Defaults to 0.

    Returns:
        dict: A dictionary with 'title', 'url', and 'children' keys, or None.
    """
    if visited_urls is None: # Should be initialized outside for the first call
        visited_urls = set()

    if url in visited_urls or current_depth > max_depth:
        # Return a minimal node if already visited but within depth, to signify it exists
        # Or None if depth is exceeded, to stop further processing down this branch
        if url in visited_urls and current_depth <= max_depth:
             # print(f"Skipping already visited URL: {url} at depth {current_depth}")
             return {'title': 'Visited Link', 'url': url, 'children': []} # Indicate it's a known link
        # print(f"Skipping URL {url}: already visited or max depth ({max_depth}) exceeded at {current_depth}.")
        return None

    logger.info("Processing: %s at depth %d", url, current_depth)
    visited_urls.add(url)

    try:
        response = requests.get(url, timeout=10)
        response.raise_for_status()
    except requests.exceptions.RequestException as e:
        logger.error("Error fetching URL %s: %s", url, e)
        return None

    try:
        soup = BeautifulSoup(response.content, 'html.parser')
    except Exception as e:
        logger.error("Error parsing HTML from %s: %s", url, e)
        return None

    title_tag = soup.find('title')
    title = title_tag.string.strip() if title_tag else 'No Title Found'

    node_data = {
        'title': title,
        'url': url,
        'children': []
    }

    # Extract internal links for recursion (only if within depth)
    if current_depth < max_depth:
        parsed_url = urlparse(url)
        base_url = f"{parsed_url.scheme}://{parsed_url.netloc}"
        internal_links_for_recursion = set()

        for link_tag in soup.find_all('a', href=True):
            href = link_tag['href']
            joined_url = urljoin(base_url, href)
            parsed_joined_url = urlparse(joined_url)

            if parsed_joined_url.netloc == parsed_url.netloc:
                clean_link = parsed_joined_url.scheme + "://" + parsed_joined_url.netloc + parsed_joined_url.path
                # Avoid adding self-references that lead to immediate re-visit in next step if not handled by visited_urls
                if clean_link != url:
                     internal_links_for_recursion.add(clean_link)

        for link in sorted(list(internal_links_for_recursion)):
            child_node = build_tree(link, visited_urls, max_depth, current_depth + 1)
            if child_node:
                node_data['children'].append(child_node)

    # internal_links key is removed as per requirement, children array represents them

    return node_data

def condense_tree(node):
    """
    Recursively condenses the tree by removing empty leaf nodes (e.g., 'Visited Link' with no children)
    and merging single-child 'Visited Link' parent nodes.
    Modifies the tree in-place.
    """
    if not node or not isinstance(node, dict) or 'children' not in node:
        return node

    # Helper for URL comparison
    def urls_are_pseudo_identical(url1, url2):
        return url1.rstrip('/') == url2.rstrip('/')

    # Rule 3: Detect and remove redundant index pattern (current node vs its first child)
    # This rule is applied before recursing to children of the current node,
    # as it might restructure the direct children list of the current node.
    if len(node.get('children', [])) > 0:
        first_child = node['children'][0]
        if (node.get('title') == first_child.get('title') and
                urls_are_pseudo_identical(node.get('url', ''), first_child.get('url', ''))):
            # print(f"Condensing: Applying redundant index pattern fix for node: {node.get('url')}")
            # The children of the first_child become the new children of the current node.
            # Any other children of the current node (beyond the first) are discarded under this rule.
            # This assumes the pattern implies the first child is the *true* representation
            # and other direct children of 'node' at this point are less relevant or duplicates.
            # If other children should be preserved, this logic would need adjustment.
            # For now, strictly interpreting "replace the node['children'] with node['children'][0]['children']"
            node['children'] = first_child.get('children', [])
            # After this, the 'node' itself is preserved, but its children list is updated.
            # The old first_child is gone.

    # Recursively condense children first (post-order traversal for modification for other rules)
    # This ensures that deeper nodes are processed before applying rules 1 and 2 to the current node's children list.
    for child in node.get('children', []): # Iterate over potentially modified children list
        condense_tree(child)

    # Rules 1 & 2 are applied to the children of the current 'node'
    new_children = []
    for child in node.get('children', []): # Iterate again, as children list could have been changed by recursive calls
        # Check Rule 1: Is it an empty 'Visited Link' leaf?
        if child.get('title') == 'Visited Link' and not child.get('children'):
            # print(f"Condensing: Removing empty 'Visited Link' leaf: {child.get('url')}")
            continue

        # Check Rule 2: Is it a 'Visited Link' node with exactly one child?
        if child.get('title') == 'Visited Link' and len(child.get('children', [])) == 1:
            # print(f"Condensing: Merging single-child 'Visited Link' node: {child.get('url')} with its child {child['children'][0].get('url')}")
            # The child of the 'Visited Link' node has already been condensed because of the recursion above.
            new_children.append(child['children'][0])
            continue

        new_children.append(child)

    node['children'] = new_children
    return node

if __name__ == '__main__':
    import argparse
    import sys

    parser = argparse.ArgumentParser(description="Build and condense a website tree.")
    parser.add_argument(
        "--test-condensation",
        metavar="JSON_FILE",
        type=str,
        help="Load a JSON tree from a file, condense it, and print to console. Bypasses crawling."
    )
    args = parser.parse_args()

    if args.test_condensation:
        logger.info("--- Test Condensation Mode ---")
        filepath = args.test_condensation
        try:
            with open(filepath, 'r', encoding='utf-8') as f:
                loaded_data = json.load(f)
            logger.info("Successfully loaded tree data from %s", filepath)

            logger.info("\n--- Condensing loaded tree ---")
            condensed_from_file = condense_tree(loaded_data) # Assumes condense_tree modifies in place or returns the modified tree

            logger.info("\n--- Result of Condensation (from file) ---")
            logger.info(json.dumps(condensed_from_file, indent=2, ensure_ascii=False))

        except FileNotFoundError:
            logger.error("Error: File not found at %s", filepath)
            sys.exit(1)
        except json.JSONDecodeError as e:
            logger.error("Error: Invalid JSON in %s - %s", filepath, e)
            sys.exit(1)
        except Exception as e:
            logger.error("An unexpected error occurred during test condensation: %s", e)
            sys.exit(1)
        sys.exit(0) # Successfully exit after test mode

    # --- Normal Crawling Mode ---
    main_url = "https://www.condadodecastilla.es/"
    visited_urls_global = set()
    crawl_max_depth = 2 # Set depth to 2 for this run

    logger.info("--- Processing Main URL (max_depth=%d): %s ---", crawl_max_depth, main_url)

    homepage_tree = build_tree(main_url, visited_urls=visited_urls_global, max_depth=crawl_max_depth, current_depth=0)

    if homepage_tree:
        logger.info("\n--- Condensing Tree ---")
        condensed_tree = condense_tree(homepage_tree)

        output_filename = "condensed_website_tree.json"
        try:
            with open(output_filename, 'w', encoding='utf-8') as f:
                json.dump(condensed_tree, f, indent=2, ensure_ascii=False)
            logger.info("\n--- Condensed tree successfully written to %s ---", output_filename)
        except IOError as e:
            logger.error("\n--- Error writing condensed tree to file: %s ---", e)
    else:
        logger.error("Failed to retrieve data for %s", main_url)
    # print("\n--- Processing Main Sections (individually, not recursively from homepage) ---")
    # section_urls = [
    #     "https://www.condadodecastilla.es/historia/",
    #     "https://www.condadodecastilla.es/personajes/",
    #     "https://www.condadodecastilla.es/lugares/",
    #     "https://www.condadodecastilla.es/cultura-sociedad/"
    # ]

    # for section_url in section_urls:
    #     # Each section can be a new tree or link into the main visited set
    #     # For simplicity, let's treat them as separate small trees for now,
    #     # re-initializing visited_urls for each or using the global one.
    #     # Using global one means if homepage linked to sections, they won't be re-crawled.
    #     print(f"\n--- Processing Section URL (max_depth=0): {section_url} ---")
    #     # Resetting current_depth for each new root, and using a shallow depth for sections for this example
    #     section_visited_urls = set() # Potentially use visited_urls_global
    #     section_data = build_tree(section_url, visited_urls=section_visited_urls, max_depth=0, current_depth=0)
    #     if section_data:
    #         print(f"Title: {section_data['title']}")
    #         print(f"URL: {section_data['url']}")
    #         print(f"Found {len(section_data['children'])} child links (at depth 0).")
    #         # print(json.dumps(section_data, indent=2, ensure_ascii=False)) # Optionally print full section tree
    #     else:
    #         print(f"Failed to retrieve data for {section_url}")
