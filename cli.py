import argparse
import json
from datetime import datetime, timezone
import uuid  # For generating IDs if not provided by crawler/processor
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

# Assuming all module files are in the same directory
from graph_db_interface import GraphDBInterface
from crawler import WebCrawler
from content_processor import ContentProcessor
from consistency_analyzer import ConsistencyAnalyzer
from link_discoverer import LinkDiscoverer

# --- Global Instances ---
db_interface = GraphDBInterface()
content_processor = ContentProcessor() # Independent
crawler = WebCrawler() # User agent can be default
consistency_analyzer = ConsistencyAnalyzer(db_interface)
link_discoverer = LinkDiscoverer(db_interface)

# --- Handler Functions ---

def handle_add_url(url_to_crawl: str):
    """
    Handles crawling a URL, processing its content, and adding it to the graph.
    """
    logger.info("Attempting to crawl and process URL: %s", url_to_crawl)

    # 1. Fetch Page (using WebCrawler's fetch_page for raw HTML)
    # fetch_page returns: html_content, error_message
    raw_html_content, fetch_error = crawler.fetch_page(url_to_crawl)

    if fetch_error:
        logger.error("Error fetching URL: %s", fetch_error)
        return

    if not raw_html_content:
        logger.error("No HTML content fetched. Cannot process.")
        return

    logger.info("Successfully fetched content for %s", url_to_crawl)

    # 2. Process Content (using ContentProcessor for text extraction)
    # process_content returns: {"processed_text": text}
    processed_data = content_processor.process_content(raw_html_content)
    main_text_content = processed_data.get("processed_text", "")
    logger.info("Processed text content (length: %d).", len(main_text_content))

    # 3. Parse HTML (using WebCrawler's parse_html for title and links)
    # parse_html returns: title, list_of_link_dicts
    # Each link_dict from parse_html is like: {"anchor_text": "...", "target_url": "..."}
    page_title, extracted_links_info = crawler.parse_html(raw_html_content, url_to_crawl)
    logger.info(
        "Parsed HTML. Title: '%s'. Found %d links.",
        page_title,
        len(extracted_links_info),
    )

    # 4. Construct WebResource Data
    # Based on data_structures.md and what crawler/processor provide
    resource_id = str(uuid.uuid4()) # Generate a new ID for this resource
    web_resource_data = {
        "id": resource_id,
        "url": url_to_crawl,
        "content": main_text_content, # Using main_text_content from ContentProcessor
        "last_crawled_at": datetime.now(timezone.utc).isoformat(),
        "metadata": {
            "title": page_title if page_title else "N/A",
            # Potentially other metadata if ContentProcessor provided more
        }
    }

    # 5. Add/Update Resource in DB
    db_interface.add_or_update_resource(web_resource_data)
    logger.info(
        "Resource for %s added/updated in the database with ID: %s",
        url_to_crawl,
        resource_id,
    )

    # 6. Construct and Add Links Data
    if extracted_links_info:
        logger.info("Adding extracted links to the database...")
        for link_info in extracted_links_info:
            # Link data structure needs source_url and target_url for GraphDBInterface
            # source_resource_id will be set by add_link based on source_url
            db_link_data = {
                "id": str(uuid.uuid4()), # Unique ID for the link itself
                "source_url": url_to_crawl, # The page we just crawled
                "target_url": link_info["target_url"],
                "anchor_text": link_info.get("anchor_text", ""),
                "created_at": datetime.now(timezone.utc).isoformat()
            }
            db_interface.add_link(db_link_data)
        logger.info(
            "Added %d links originating from %s.",
            len(extracted_links_info),
            url_to_crawl,
        )
    else:
        logger.info("No links found on %s to add.", url_to_crawl)

    logger.info("Processing complete for %s.", url_to_crawl)


def handle_show_resource(url: str):
    """Shows a specific resource from the database."""
    logger.info("Looking up resource: %s...", url)
    resource = db_interface.get_resource(url)
    if resource:
        logger.info(json.dumps(resource, indent=2, sort_keys=True))
    else:
        logger.info("Resource not found.")

def handle_list_resources():
    """Lists all resources in the database."""
    logger.info("Fetching all resources...")
    resources = db_interface.get_all_resources()
    if resources:
        logger.info(json.dumps(resources, indent=2, sort_keys=True))
    else:
        logger.info("No resources found in the database.")

def handle_find_uncrawled(limit: int):
    """Finds uncrawled links."""
    logger.info("Finding up to %d uncrawled links...", limit)
    uncrawled_urls = link_discoverer.find_uncrawled_links(limit=limit)
    if uncrawled_urls:
        logger.info("Uncrawled URLs:")
        for url in uncrawled_urls:
            logger.info("  - %s", url)
    else:
        logger.info("No uncrawled links found (or all known links are crawled).")

def handle_run_consistency_check():
    """Runs consistency checks on the graph."""
    logger.info("Running consistency analysis on the graph...")
    issues = consistency_analyzer.analyze_graph_consistency()
    if issues:
        logger.info("Consistency issues found:")
        logger.info(json.dumps(issues, indent=2, sort_keys=True))
    else:
        logger.info("No consistency issues found.")

# --- Main CLI Logic ---
def main():
    parser = argparse.ArgumentParser(description="Knowledge Graph CLI")
    subparsers = parser.add_subparsers(dest="command", help="Available commands")
    subparsers.required = True # Make sure a command is provided

    # Command: add_url
    add_parser = subparsers.add_parser("add_url", help="Crawl a URL and add it to the graph.")
    add_parser.add_argument("url", type=str, help="The URL to crawl (e.g., http://example.com).")
    add_parser.set_defaults(func=lambda args: handle_add_url(args.url))

    # Command: show_resource
    show_parser = subparsers.add_parser("show_resource", help="Show a specific resource by URL.")
    show_parser.add_argument("url", type=str, help="The URL of the resource to show.")
    show_parser.set_defaults(func=lambda args: handle_show_resource(args.url))

    # Command: list_resources
    list_parser = subparsers.add_parser("list_resources", help="List all resources in the graph.")
    list_parser.set_defaults(func=lambda args: handle_list_resources())

    # Command: find_uncrawled
    uncrawled_parser = subparsers.add_parser("find_uncrawled", help="Find links pointing to uncrawled resources.")
    uncrawled_parser.add_argument("--limit", type=int, default=10, help="Maximum number of uncrawled URLs to return.")
    uncrawled_parser.set_defaults(func=lambda args: handle_find_uncrawled(args.limit))

    # Command: run_consistency
    consistency_parser = subparsers.add_parser("run_consistency", help="Run consistency checks on the graph.")
    consistency_parser.set_defaults(func=lambda args: handle_run_consistency_check())

    args = parser.parse_args()
    args.func(args) # Call the appropriate handler

if __name__ == "__main__":
    main()
