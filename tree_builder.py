import requests
from bs4 import BeautifulSoup
from urllib.parse import urlparse, urljoin
import json

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

    print(f"Processing: {url} at depth {current_depth}")
    visited_urls.add(url)

    try:
        response = requests.get(url, timeout=10)
        response.raise_for_status()
    except requests.exceptions.RequestException as e:
        print(f"Error fetching URL {url}: {e}")
        return None

    try:
        soup = BeautifulSoup(response.content, 'html.parser')
    except Exception as e:
        print(f"Error parsing HTML from {url}: {e}")
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

    # Recursively condense children first (post-order traversal for modification)
    for child in node['children']:
        condense_tree(child)

    # Rule 1: Remove empty leaf nodes titled 'Visited Link'
    # Rule 2: Merge single-child 'Visited Link' parent nodes

    new_children = []
    for child in node['children']:
        # Check Rule 1: Is it an empty 'Visited Link' leaf?
        if child.get('title') == 'Visited Link' and not child.get('children'):
            # Skip adding this child to new_children, effectively removing it
            # print(f"Condensing: Removing empty 'Visited Link' leaf: {child.get('url')}")
            continue

        # Check Rule 2: Is it a 'Visited Link' node with exactly one child?
        if child.get('title') == 'Visited Link' and len(child.get('children', [])) == 1:
            # Replace this child with its own child
            # print(f"Condensing: Merging single-child 'Visited Link' node: {child.get('url')} with its child {child['children'][0].get('url')}")
            new_children.append(child['children'][0])
            # Important: We've already called condense_tree on child['children'][0]
            # so it's already as condensed as it can be from its perspective.
            continue

        new_children.append(child)

    node['children'] = new_children
    return node


if __name__ == '__main__':
    main_url = "https://www.condadodecastilla.es/"
    # Initialize visited_urls for the entire crawl session
    visited_urls_global = set()
    # Define max_depth for the crawl
    crawl_max_depth = 0 # Minimum depth to ensure script completion

    print(f"--- Processing Main URL (max_depth={crawl_max_depth}): {main_url} ---")

    # Pass visited_urls_global to the main call
    homepage_tree = build_tree(main_url, visited_urls=visited_urls_global, max_depth=crawl_max_depth, current_depth=0)

    if homepage_tree:
        print("\n--- Condensing Tree ---")
        condensed_tree = condense_tree(homepage_tree) # Modifies in place

        output_filename = "condensed_website_tree.json"
        try:
            with open(output_filename, 'w', encoding='utf-8') as f:
                json.dump(condensed_tree, f, indent=2, ensure_ascii=False)
            print(f"\n--- Condensed tree successfully written to {output_filename} ---")
        except IOError as e:
            print(f"\n--- Error writing condensed tree to file: {e} ---")
            # Optionally, print to console if writing to file fails
            # print("\n--- Condensed Homepage Tree Structure (fallback to console) ---")
            # print(json.dumps(condensed_tree, indent=2, ensure_ascii=False))
    else:
        print(f"Failed to retrieve data for {main_url}")

    # Commenting out section processing for brevity in recursive test
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
