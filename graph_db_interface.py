import uuid
from datetime import datetime
import json
import logging

# Configure module-level logger. This basic configuration writes
# timestamped messages with the module name and log level.
# Applications embedding this module can override the configuration
# if needed.
logging.basicConfig(
    level=logging.INFO,
    format="%(asctime)s - %(name)s - %(levelname)s - %(message)s"
)
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
            logger.info("Database state saved to %s", self.db_filepath)
        except IOError as e:
            logger.error("Error saving database state to %s: %s", self.db_filepath, e)

    def _load_from_file(self):
        """Loads the state of nodes and edges from a JSON file if it exists."""
        try:
            with open(self.db_filepath, 'r') as f:
                data = json.load(f)
                self._nodes = data.get("nodes", {})
                self._edges = data.get("edges", [])
            logger.info("Database state loaded from %s", self.db_filepath)
        except FileNotFoundError:
            logger.warning("Database file %s not found. Starting with an empty database.", self.db_filepath)
        except json.JSONDecodeError as e:
            logger.error("Error decoding JSON from %s: %s. Starting with an empty database.", self.db_filepath, e)
        except IOError as e:
            logger.error("Error loading database state from %s: %s. Starting with an empty database.", self.db_filepath, e)


    def _create_placeholder_resource(self, url: str) -> dict:
        """Helper to create a basic resource if it doesn't exist."""
        logger.info("Creating placeholder resource for URL: %s", url)
        return {
            "id": str(uuid.uuid4()),
            "url": url,
            "content": "N/A (placeholder)",
            "last_crawled_at": datetime.utcnow().isoformat(),
            "metadata": {"status": "placeholder"}
        }

    def add_or_update_resource(self, resource_data: dict) -> None:
        """Adds or updates a resource in ``self._nodes`` and persists changes.

        ``resource_data`` must contain a ``url`` key.
        """
        url = resource_data.get("url")
        if not url:
            logger.error("'url' is required to add or update a resource.")
            return

        if url in self._nodes:
            self._nodes[url].update(resource_data)
            logger.info("Updated resource: %s", url)
            self._save_to_file()
        else:
            # Ensure essential fields like 'id' are present if new
            if "id" not in resource_data:
                resource_data["id"] = str(uuid.uuid4())
            if "last_crawled_at" not in resource_data:
                resource_data["last_crawled_at"] = datetime.utcnow().isoformat()
            self._nodes[url] = resource_data
            logger.info("Added new resource: %s", url)
            # Persist changes for newly added resources
            self._save_to_file()

    def get_resource(self, url: str) -> dict | None:
        """Retrieves a resource from self._nodes by URL."""
        resource = self._nodes.get(url)
        if resource:
            logger.info("Retrieved resource: %s", url)
        else:
            logger.warning("Resource not found: %s", url)
        return resource

    def resource_exists(self, url: str) -> bool:
        """Checks if a resource URL exists in self._nodes."""
        exists = url in self._nodes
        logger.info("Resource exists check for %s: %s", url, exists)
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
            logger.error("'source_url' and 'target_url' are required to add a link.")
            return

        # Check for duplicate links
        for edge in self._edges:
            if edge.get("source_url") == source_url and edge.get("target_url") == target_url:
                logger.info("Link from %s to %s already exists. Skipping.", source_url, target_url)
                return

        # Ensure source resource exists or create a placeholder
        if not self.resource_exists(source_url):
            logger.info("Source resource %s not found. Creating placeholder.", source_url)
            placeholder_source = self._create_placeholder_resource(source_url)
            self.add_or_update_resource(placeholder_source)
        # Update link_data with the actual source resource ID
        link_data["source_resource_id"] = self._nodes[source_url]["id"]


        # Ensure target resource exists or create a placeholder
        if not self.resource_exists(target_url):
            logger.info("Target resource %s not found. Creating placeholder.", target_url)
            placeholder_target = self._create_placeholder_resource(target_url)
            self.add_or_update_resource(placeholder_target)
        # Update link_data with the actual target resource ID (if it was a placeholder)
        # In a real system, this might be just the URL, and ID resolution happens later.
        # For now, we'll assume we might want to store the target's ID if known.
        link_data["target_resource_id"] = self._nodes[target_url]["id"]


        # Ensure essential fields for the link itself
        if "id" not in link_data:
            link_data["id"] = str(uuid.uuid4())
        if "created_at" not in link_data:
            link_data["created_at"] = datetime.utcnow().isoformat()

        self._edges.append(link_data)
        logger.info("Added link: %s -> %s", source_url, target_url)
        self._save_to_file() # Save after adding a link

    def get_links_from_resource(self, url: str) -> list[dict]:
        """Returns a list of links where source_url matches the given URL."""
        links = [edge for edge in self._edges if edge.get("source_url") == url]
        logger.info("Found %d links from resource: %s", len(links), url)
        return links

    def get_links_to_resource(self, url: str) -> list[dict]:
        """Returns a list of links where target_url matches the given URL."""
        links = [edge for edge in self._edges if edge.get("target_url") == url]
        logger.info("Found %d links to resource: %s", len(links), url)
        return links

    def get_all_resources(self) -> list[dict]:
        """Returns a list of all resource dictionaries."""
        all_nodes = list(self._nodes.values())
        logger.info("Retrieved %d resources.", len(all_nodes))
        return all_nodes

    def get_all_links(self) -> list[dict]:
        """Returns a list of all link dictionaries."""
        logger.info("Retrieved %d links.", len(self._edges))
        return list(self._edges)

if __name__ == "__main__":
    db = GraphDBInterface()

    logger.info("--- Testing Resource Operations ---")
    # Add resources
    res1_data = {"url": "http://example.com/page1", "content": "Content of page 1", "metadata": {"title": "Page 1"}}
    db.add_or_update_resource(res1_data)

    res2_data = {"url": "http://example.com/page2", "content": "Content of page 2", "metadata": {"title": "Page 2"}}
    db.add_or_update_resource(res2_data)

    # Get a resource
    retrieved_res = db.get_resource("http://example.com/page1")
    if retrieved_res:
        logger.info("Retrieved content for page1: %s", retrieved_res.get('content'))

    # Check existence
    db.resource_exists("http://example.com/page1")
    db.resource_exists("http://example.com/nonexistent")

    # Update a resource
    res1_updated_data = {"url": "http://example.com/page1", "content": "Updated content of page 1", "metadata": {"status": "updated"}}
    db.add_or_update_resource(res1_updated_data)
    retrieved_res_updated = db.get_resource("http://example.com/page1")
    if retrieved_res_updated:
        logger.info(
            "Updated content for page1: %s, Status: %s",
            retrieved_res_updated.get('content'),
            retrieved_res_updated.get('metadata', {}).get('status')
        )

    logger.info("--- Testing Link Operations ---")
    # Add links
    link1_data = {"source_url": "http://example.com/page1", "target_url": "http://example.com/page2", "anchor_text": "Link to Page 2"}
    db.add_link(link1_data)

    link2_data = {"source_url": "http://example.com/page1", "target_url": "http://example.com/page3", "anchor_text": "Link to Page 3 (non-existent yet)"}
    db.add_link(link2_data) # This should create a placeholder for page3

    # Try adding a duplicate link
    db.add_link(link1_data)


    # Get links from a resource
    links_from_page1 = db.get_links_from_resource("http://example.com/page1")
    logger.info("Links from page1: %s", [(l['source_url'], l['target_url']) for l in links_from_page1])

    # Get links to a resource
    links_to_page2 = db.get_links_to_resource("http://example.com/page2")
    logger.info("Links to page2: %s", [(l['source_url'], l['target_url']) for l in links_to_page2])

    links_to_page3 = db.get_links_to_resource("http://example.com/page3")  # Page 3 was a placeholder
    logger.info("Links to page3: %s", [(l['source_url'], l['target_url']) for l in links_to_page3])


    logger.info("--- Testing Retrieval of All Data ---")
    all_res = db.get_all_resources()
    logger.info("Total resources: %d", len(all_res))
    # for r in all_res:
    #     print(f"  Resource URL: {r['url']}, Content: {r.get('content', 'N/A')[:30]}...")

    all_links = db.get_all_links()
    logger.info("Total links: %d", len(all_links))
    # for l_info in all_links:
    #     print(f"  Link: {l_info['source_url']} -> {l_info['target_url']}")

    # Demonstrate saving explicitly (though it happens on add/update)
    # db._save_to_file() # This line is for explicit testing if needed, remove for production if auto-save is enough

    logger.info("--- Verifying Placeholder Creation ---")
    page3_resource = db.get_resource("http://example.com/page3")
    if page3_resource:
        logger.info("Resource page3 data: %s", page3_resource)
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

    logger.info("--- Final State ---")
    logger.info("All Resources:")
    for r_url, r_data in db._nodes.items():
        logger.info(
            "  URL: %s, ID: %s, Content: %s..., Crawled: %s",
            r_url,
            r_data['id'],
            r_data.get('content', 'N/A')[:30],
            r_data.get('last_crawled_at')
        )
    logger.info("All Links:")
    for l_info in db._edges:
        logger.info(
            "  ID: %s, From: %s (ID: %s) -> To: %s (ID: %s), Anchor: %s",
            l_info['id'],
            l_info['source_url'],
            l_info['source_resource_id'],
            l_info['target_url'],
            l_info.get('target_resource_id', 'N/A'),
            l_info.get('anchor_text', 'N/A')
        )

    logger.info("Demo complete.")

    # Example of loading from a potentially existing file (if you run the test multiple times)
    logger.info("--- Testing loading from file (if exists from previous run) ---")
    db_loaded = GraphDBInterface()  # This will attempt to load from knowledge_graph_db.json
    logger.info(
        "Loaded %d resources and %d links on second init.",
        len(db_loaded.get_all_resources()),
        len(db_loaded.get_all_links())
    )
