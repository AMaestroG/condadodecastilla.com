import uuid
from datetime import datetime
import json
import logging

logger = logging.getLogger(__name__)

class GraphDBInterface:
    def __init__(self, config=None):
        """
        Initializes in-memory storage for nodes (resources) and edges (links).
        _nodes stores resources keyed by their URL for quick lookup.
        _edges is a list of link dictionaries.
        """
        self._nodes = {}  # Key: URL, Value: resource_data dictionary
        self._edges = []  # List of link_data dictionaries
        self.db_filepath = "knowledge_graph_db.json"
        # Config is not used for now, but can be for future DB connections
        logger.info("GraphDBInterface initializing...")
        self._load_from_file() # Load data on initialization

    def _save_to_file(self):
        """Saves the current state of nodes and edges to a JSON file."""
        try:
            with open(self.db_filepath, 'w') as f:
                json.dump({"nodes": self._nodes, "edges": self._edges}, f, indent=2)
            logger.info(f"Database state saved to {self.db_filepath}")
        except IOError as e:
            logger.error(f"Error saving database state to {self.db_filepath}: {e}")

    def _load_from_file(self):
        """Loads the state of nodes and edges from a JSON file if it exists."""
        try:
            with open(self.db_filepath, 'r') as f:
                data = json.load(f)
                self._nodes = data.get("nodes", {})
                self._edges = data.get("edges", [])
            logger.info(f"Database state loaded from {self.db_filepath}")
        except FileNotFoundError:
            logger.info(f"Database file {self.db_filepath} not found. Starting with an empty database.")
        except json.JSONDecodeError as e:
            logger.error(f"Error decoding JSON from {self.db_filepath}: {e}. Starting with an empty database.")
        except IOError as e:
            logger.error(f"Error loading database state from {self.db_filepath}: {e}. Starting with an empty database.")


    def _create_placeholder_resource(self, url: str) -> dict:
        """Helper to create a basic resource if it doesn't exist."""
        logger.info(f"Creating placeholder resource for URL: {url}")
        return {
            "id": str(uuid.uuid4()),
            "url": url,
            "content": "N/A (placeholder)", # Default content for placeholder
            "last_crawled_at": datetime.utcnow().isoformat(), # Timestamp of placeholder creation
            "metadata": {"status": "placeholder"} # Metadata indicating it's a placeholder
        }

    def add_or_update_resource(self, resource_data: dict) -> None:
        """
        Adds or updates a resource in self._nodes.
        Requires 'url' in resource_data.
        """
        url = resource_data.get("url")
        if not url:
            logger.error("Error: 'url' is required to add or update a resource.")
            return

        if url in self._nodes:
            self._nodes[url].update(resource_data)
            logger.info(f"Updated resource: {url}")
        else:
            # Ensure essential fields like 'id' are present if new
            if "id" not in resource_data:
                resource_data["id"] = str(uuid.uuid4())
            if "last_crawled_at" not in resource_data: # Ensure timestamp, could be creation or last update
                 resource_data["last_crawled_at"] = datetime.utcnow().isoformat()
            self._nodes[url] = resource_data
            logger.info(f"Added new resource: {url}")
        self._save_to_file() # Save after adding/updating

    def get_resource(self, url: str) -> dict | None:
        """Retrieves a resource from self._nodes by URL."""
        resource = self._nodes.get(url)
        if resource:
            logger.info(f"Retrieved resource: {url}")
        else:
            logger.info(f"Resource not found: {url}")
        return resource

    def resource_exists(self, url: str) -> bool:
        """Checks if a resource URL exists in self._nodes."""
        exists = url in self._nodes
        logger.debug(f"Resource exists check for {url}: {exists}") # Changed to debug as it's very frequent
        return exists

    def add_link(self, link_data: dict) -> None:
        """
        Adds a link to self._edges.
        Requires 'source_url' and 'target_url' in link_data.
        Creates placeholder resources if source_url or target_url don't exist.
        Prevents duplicate links based on (source_url, target_url) pair.
        """
        source_url = link_data.get("source_url")
        target_url = link_data.get("target_url")

        if not source_url or not target_url:
            logger.error("Error: 'source_url' and 'target_url' are required to add a link.")
            return

        # Check for duplicate links
        for edge in self._edges:
            if edge.get("source_url") == source_url and edge.get("target_url") == target_url:
                logger.info(f"Link from {source_url} to {target_url} already exists. Skipping.")
                return

        # Ensure source resource exists or create a placeholder
        if not self.resource_exists(source_url): # resource_exists uses logger.debug
            logger.info(f"Source resource {source_url} not found. Creating placeholder.")
            placeholder_source = self._create_placeholder_resource(source_url)
            self.add_or_update_resource(placeholder_source) # add_or_update_resource uses logger.info/error
        # Update link_data with the actual source resource ID
        link_data["source_resource_id"] = self._nodes[source_url]["id"]


        # Ensure target resource exists or create a placeholder
        if not self.resource_exists(target_url): # resource_exists uses logger.debug
            logger.info(f"Target resource {target_url} not found. Creating placeholder.")
            placeholder_target = self._create_placeholder_resource(target_url)
            self.add_or_update_resource(placeholder_target) # add_or_update_resource uses logger.info/error
        # Update link_data with the actual target resource ID (if it was a placeholder)
        link_data["target_resource_id"] = self._nodes[target_url]["id"]


        # Ensure essential fields for the link itself
        if "id" not in link_data:
            link_data["id"] = str(uuid.uuid4())
        if "created_at" not in link_data:
            link_data["created_at"] = datetime.utcnow().isoformat()

        self._edges.append(link_data)
        logger.info(f"Added link: {source_url} -> {target_url}")
        self._save_to_file() # Save after adding a link

    def get_links_from_resource(self, url: str) -> list[dict]:
        """Returns a list of links where source_url matches the given URL."""
        links = [edge for edge in self._edges if edge.get("source_url") == url]
        logger.debug(f"Found {len(links)} links from resource: {url}") # Changed to debug
        return links

    def get_links_to_resource(self, url: str) -> list[dict]:
        """Returns a list of links where target_url matches the given URL."""
        links = [edge for edge in self._edges if edge.get("target_url") == url]
        logger.debug(f"Found {len(links)} links to resource: {url}") # Changed to debug
        return links

    def get_all_resources(self) -> list[dict]:
        """Returns a list of all resource dictionaries."""
        all_nodes = list(self._nodes.values())
        logger.info(f"Retrieved {len(all_nodes)} resources.")
        return all_nodes

    def get_all_links(self) -> list[dict]:
        """Returns a list of all link dictionaries."""
        logger.info(f"Retrieved {len(self._edges)} links.")
        return list(self._edges)

if __name__ == "__main__":
    # Basic logging setup for standalone script execution
    logging.basicConfig(level=logging.DEBUG, format='%(asctime)s - %(name)s - %(levelname)s - %(message)s')
    db = GraphDBInterface() # Logger inside will pick up this basicConfig

    # The print statements in __main__ are kept for direct console output during individual script testing.
    # They are not part of the library's operational logging.
    print("\n--- Testing Resource Operations ---")
    # Add resources
    res1_data = {"url": "http://example.com/page1", "content": "Content of page 1", "metadata": {"title": "Page 1"}}
    db.add_or_update_resource(res1_data)

    res2_data = {"url": "http://example.com/page2", "content": "Content of page 2", "metadata": {"title": "Page 2"}}
    db.add_or_update_resource(res2_data)

    # Get a resource
    retrieved_res = db.get_resource("http://example.com/page1")
    if retrieved_res:
        print(f"Retrieved content for page1: {retrieved_res.get('content')}")

    # Check existence
    db.resource_exists("http://example.com/page1")
    db.resource_exists("http://example.com/nonexistent")

    # Update a resource
    res1_updated_data = {"url": "http://example.com/page1", "content": "Updated content of page 1", "metadata": {"status": "updated"}}
    db.add_or_update_resource(res1_updated_data)
    retrieved_res_updated = db.get_resource("http://example.com/page1")
    if retrieved_res_updated:
        print(f"Updated content for page1: {retrieved_res_updated.get('content')}, Status: {retrieved_res_updated.get('metadata', {}).get('status')}")

    print("\n--- Testing Link Operations ---")
    # Add links
    link1_data = {"source_url": "http://example.com/page1", "target_url": "http://example.com/page2", "anchor_text": "Link to Page 2"}
    db.add_link(link1_data)

    link2_data = {"source_url": "http://example.com/page1", "target_url": "http://example.com/page3", "anchor_text": "Link to Page 3 (non-existent yet)"}
    db.add_link(link2_data) # This should create a placeholder for page3

    # Try adding a duplicate link
    db.add_link(link1_data)


    # Get links from a resource
    links_from_page1 = db.get_links_from_resource("http://example.com/page1")
    print(f"Links from page1: {[(l['source_url'], l['target_url']) for l in links_from_page1]}")

    # Get links to a resource
    links_to_page2 = db.get_links_to_resource("http://example.com/page2")
    print(f"Links to page2: {[(l['source_url'], l['target_url']) for l in links_to_page2]}")

    links_to_page3 = db.get_links_to_resource("http://example.com/page3") # Page 3 was a placeholder
    print(f"Links to page3: {[(l['source_url'], l['target_url']) for l in links_to_page3]}")


    print("\n--- Testing Retrieval of All Data ---")
    all_res = db.get_all_resources()
    print(f"Total resources: {len(all_res)}")
    # for r in all_res:
    #     print(f"  Resource URL: {r['url']}, Content: {r.get('content', 'N/A')[:30]}...")

    all_links = db.get_all_links()
    print(f"Total links: {len(all_links)}")
    # for l_info in all_links:
    #     print(f"  Link: {l_info['source_url']} -> {l_info['target_url']}")

    # Demonstrate saving explicitly (though it happens on add/update)
    # db._save_to_file() # This line is for explicit testing if needed, remove for production if auto-save is enough

    print("\n--- Verifying Placeholder Creation ---")
    page3_resource = db.get_resource("http://example.com/page3")
    if page3_resource:
        print(f"Resource page3 data: {page3_resource}")
        assert page3_resource.get("metadata", {}).get("status") == "placeholder"

    # Test adding a link where neither source nor target exist
    link3_data = {"source_url": "http://example.com/page4", "target_url": "http://example.com/page5", "anchor_text": "Link from new to new"}
    db.add_link(link3_data)
    db.resource_exists("http://example.com/page4")
    db.resource_exists("http://example.com/page5")
    page4_res = db.get_resource("http://example.com/page4")
    page5_res = db.get_resource("http://example.com/page5")
    assert page4_res and page4_res.get("metadata", {}).get("status") == "placeholder"
    assert page5_res and page5_res.get("metadata", {}).get("status") == "placeholder"

    print("\n--- Final State ---")
    print("All Resources:")
    for r_url, r_data in db._nodes.items():
        print(f"  URL: {r_url}, ID: {r_data['id']}, Content: {r_data.get('content', 'N/A')[:30]}..., Crawled: {r_data.get('last_crawled_at')}")
    print("All Links:")
    for l_info in db._edges:
        print(f"  ID: {l_info['id']}, From: {l_info['source_url']} (ID: {l_info['source_resource_id']}) -> To: {l_info['target_url']} (ID: {l_info.get('target_resource_id', 'N/A')}), Anchor: {l_info.get('anchor_text', 'N/A')}")

    print("\nDemo complete.")

    # Example of loading from a potentially existing file (if you run the test multiple times)
    print("\n--- Testing loading from file (if exists from previous run) ---")
    db_loaded = GraphDBInterface() # This will attempt to load from knowledge_graph_db.json
    print(f"Loaded {len(db_loaded.get_all_resources())} resources and {len(db_loaded.get_all_links())} links on second init.")
