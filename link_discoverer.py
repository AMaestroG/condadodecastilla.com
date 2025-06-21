from graph_db_interface import GraphDBInterface  # Assuming graph_db_interface.py is in the same directory
from datetime import datetime
import uuid
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

class LinkDiscoverer:
    def __init__(self, db_interface: GraphDBInterface):
        """
        Initializes the discoverer with a GraphDBInterface instance.
        """
        self.db = db_interface
        logger.info("LinkDiscoverer initialized.")

    def find_uncrawled_links(self, limit: int = 10) -> list[str]:
        """
        Finds URLs from links that haven't been crawled or have missing content.
        A resource is considered "uncrawled" if it's not in the DB,
        or if it is, but its 'content' field (used as proxy for processed_content)
        is empty, 'N/A (placeholder)', or missing.
        """
        all_links = self.db.get_all_links()
        uncrawled_urls = set() # Use a set to store unique URLs

        logger.info("\nFinding uncrawled links (limit: %d)...", limit)
        for link_data in all_links:
            target_url = link_data.get("target_url")
            if not target_url:
                continue

            resource = self.db.get_resource(target_url)

            is_uncrawled = False
            if not resource:
                is_uncrawled = True
                logger.info(
                    "  Target URL %s not found in DB (considered uncrawled).",
                    target_url,
                )
            else:
                # Using 'content' as a proxy for 'processed_content' as per problem description
                content = resource.get("content")
                if not content or content.strip() == "" or content == "N/A (placeholder)":
                    is_uncrawled = True
                    logger.info(
                        "  Target URL %s found but content is missing/placeholder (considered uncrawled).",
                        target_url,
                    )
                else:
                    logger.info(
                        "  Target URL %s found and has content (considered crawled).",
                        target_url,
                    )

            if is_uncrawled:
                uncrawled_urls.add(target_url)
                if len(uncrawled_urls) >= limit:
                    break

        result_list = list(uncrawled_urls)
        logger.info("Found %d unique uncrawled URLs.", len(result_list))
        return result_list

    def suggest_search_queries_from_topics(self, top_n_resources: int = 3) -> list[str]:
        """
        Suggests search queries based on titles of existing resources.
        Placeholder logic: uses resource titles.
        """
        # Future: Enhance with actual topic modeling (e.g., LDA, NMF on resource content)
        # and generate queries based on identified topics.
        suggested_queries = []

        # Retrieve all resources and sort them, e.g., by last_crawled_at or a future 'importance' score
        # For now, just take the first N resources as they come from get_all_resources()
        all_resources = self.db.get_all_resources()

        resources_to_consider = all_resources[:top_n_resources]

        logger.info(
            "\nSuggesting search queries from top %d resources...",
            top_n_resources,
        )
        for resource_data in resources_to_consider:
            metadata = resource_data.get("metadata", {})
            title = metadata.get("title")
            if title and title.strip() != "":
                suggested_queries.append(f"More about {title}")
                logger.info("  Generated query from title: '%s'", title)

        if not suggested_queries:
            default_query = "latest technology trends"
            suggested_queries.append(default_query)
            logger.info(
                "  No suitable titles found, using default query: '%s'",
                default_query,
            )

        logger.info("Suggested %d queries.", len(suggested_queries))
        return suggested_queries

# For a usage demonstration, see examples/link_discoverer_demo.py
