import uuid
from datetime import datetime
import json
import logging
import os
from filelock import FileLock

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
    def __init__(self, config=None, db_filepath: str | None = None):
        """
        Initializes in-memory storage for nodes (resources) and edges (links).
        _nodes stores resources keyed by their URL for quick lookup.
        _edges is a list of link dictionaries.
        """
        self._nodes = {}  # Key: URL, Value: resource_data dictionary
        self._edges = []  # List of link_data dictionaries
        self.db_filepath = (
            db_filepath
            or os.environ.get("GRAPH_DB_PATH", "knowledge_graph_db.json")
        )
        self._lock = FileLock(f"{self.db_filepath}.lock")
        # Config is not used for now, but can be for future DB connections
        logger.info("GraphDBInterface initializing...")
        self._load_from_file() # Load data on initialization

    def _save_to_file(self):
        """Saves the current state of nodes and edges to a JSON file."""
        try:
            with self._lock:
                with open(self.db_filepath, 'w') as f:
                    json.dump({"nodes": self._nodes, "edges": self._edges}, f, indent=2)
            logger.info("Database state saved to %s", self.db_filepath)
        except IOError as e:
            logger.error("Error saving database state to %s: %s", self.db_filepath, e)

    def _load_from_file(self):
        """Loads the state of nodes and edges from a JSON file if it exists."""
        try:
            with self._lock:
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

# For a usage demonstration, see examples/graph_db_interface_demo.py
