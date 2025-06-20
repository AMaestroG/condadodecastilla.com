from graph_db_interface import GraphDBInterface # Assuming graph_db_interface.py is in the same directory
from datetime import datetime
import uuid

class LinkDiscoverer:
    def __init__(self, db_interface: GraphDBInterface):
        """
        Initializes the discoverer with a GraphDBInterface instance.
        """
        self.db = db_interface
        print("LinkDiscoverer initialized.")

    def find_uncrawled_links(self, limit: int = 10) -> list[str]:
        """
        Finds URLs from links that haven't been crawled or have missing content.
        A resource is considered "uncrawled" if it's not in the DB,
        or if it is, but its 'content' field (used as proxy for processed_content)
        is empty, 'N/A (placeholder)', or missing.
        """
        all_links = self.db.get_all_links()
        uncrawled_urls = set() # Use a set to store unique URLs

        print(f"\nFinding uncrawled links (limit: {limit})...")
        for link_data in all_links:
            target_url = link_data.get("target_url")
            if not target_url:
                continue

            resource = self.db.get_resource(target_url)

            is_uncrawled = False
            if not resource:
                is_uncrawled = True
                print(f"  Target URL {target_url} not found in DB (considered uncrawled).")
            else:
                # Using 'content' as a proxy for 'processed_content' as per problem description
                content = resource.get("content")
                if not content or content.strip() == "" or content == "N/A (placeholder)":
                    is_uncrawled = True
                    print(f"  Target URL {target_url} found but content is missing/placeholder (considered uncrawled).")
                else:
                    print(f"  Target URL {target_url} found and has content (considered crawled).")

            if is_uncrawled:
                uncrawled_urls.add(target_url)
                if len(uncrawled_urls) >= limit:
                    break

        result_list = list(uncrawled_urls)
        print(f"Found {len(result_list)} unique uncrawled URLs.")
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

        print(f"\nSuggesting search queries from top {top_n_resources} resources...")
        for resource_data in resources_to_consider:
            metadata = resource_data.get("metadata", {})
            title = metadata.get("title")
            if title and title.strip() != "":
                suggested_queries.append(f"More about {title}")
                print(f"  Generated query from title: '{title}'")

        if not suggested_queries:
            default_query = "latest technology trends"
            suggested_queries.append(default_query)
            print(f"  No suitable titles found, using default query: '{default_query}'")

        print(f"Suggested {len(suggested_queries)} queries.")
        return suggested_queries

# For a usage demonstration, see examples/link_discoverer_demo.py
