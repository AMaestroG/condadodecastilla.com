import uuid
from dataclasses import dataclass, asdict, field
from datetime import datetime, timezone
import json
import logging
import os
from filelock import FileLock


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


@dataclass
class Resource:
    url: str
    content: str = "N/A (placeholder)"
    id: str = field(default_factory=lambda: str(uuid.uuid4()))
    last_crawled_at: str = field(default_factory=lambda: datetime.now(timezone.utc).isoformat())
    metadata: dict = field(default_factory=dict)


@dataclass
class Link:
    source_url: str
    target_url: str
    id: str = field(default_factory=lambda: str(uuid.uuid4()))
    source_resource_id: str | None = None
    target_resource_id: str | None = None
    created_at: str = field(default_factory=lambda: datetime.now(timezone.utc).isoformat())
    anchor_text: str | None = None

class GraphDBInterface:
    def __init__(self, config=None, db_filepath: str | None = None):
        """
        Initializes in-memory storage for nodes (resources) and edges (links).
        _nodes stores resources keyed by their URL for quick lookup.
        _edges is a list of link dictionaries.
        """
        self._nodes: dict[str, Resource] = {}  # Key: URL, Value: Resource
        self._edges: list[Link] = []  # List of Link objects
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
                    nodes_dict = {url: asdict(res) for url, res in self._nodes.items()}
                    edges_list = [asdict(edge) for edge in self._edges]
                    json.dump({"nodes": nodes_dict, "edges": edges_list}, f, indent=2)
            logger.info("Database state saved to %s", self.db_filepath)
        except IOError as e:
            logger.error("Error saving database state to %s: %s", self.db_filepath, e)

    def _load_from_file(self):
        """Loads the state of nodes and edges from a JSON file if it exists."""
        try:
            with self._lock:
                with open(self.db_filepath, 'r') as f:
                    data = json.load(f)
                    nodes = data.get("nodes", {})
                    edges = data.get("edges", [])
                    self._nodes = {url: Resource(**res) for url, res in nodes.items()}
                    self._edges = [Link(**edge) for edge in edges]
            logger.info("Database state loaded from %s", self.db_filepath)
        except FileNotFoundError:
            logger.warning("Database file %s not found. Starting with an empty database.", self.db_filepath)
        except json.JSONDecodeError as e:
            logger.error("Error decoding JSON from %s: %s. Starting with an empty database.", self.db_filepath, e)
        except IOError as e:
            logger.error("Error loading database state from %s: %s. Starting with an empty database.", self.db_filepath, e)


    def _create_placeholder_resource(self, url: str) -> Resource:
        """Helper to create a basic resource if it doesn't exist."""
        logger.info("Creating placeholder resource for URL: %s", url)
        return Resource(url=url, metadata={"status": "placeholder"})

    def add_or_update_resource(self, resource: Resource) -> None:
        """Adds or updates a resource in ``self._nodes`` and persists changes."""

        url = resource.url
        if not url:
            logger.error("'url' is required to add or update a resource.")
            return

        if url in self._nodes:
            existing = self._nodes[url]
            for field_name, value in asdict(resource).items():
                setattr(existing, field_name, value)
            logger.info("Updated resource: %s", url)
        else:
            self._nodes[url] = resource
            logger.info("Added new resource: %s", url)

        self._save_to_file()

    def get_resource(self, url: str) -> Resource | None:
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

    def add_link(self, link: Link) -> None:
        """
        Adds a link to self._edges.
        Requires 'source_url' and 'target_url' in link_data.
        Creates placeholder resources if source_url or target_url don't exist.
        Prevents duplicate links based on (source_url, target_url) pair.
        """
        source_url = link.source_url
        target_url = link.target_url

        if not source_url or not target_url:
            logger.error("'source_url' and 'target_url' are required to add a link.")
            return

        # Check for duplicate links
        for edge in self._edges:
            if edge.source_url == source_url and edge.target_url == target_url:
                logger.info("Link from %s to %s already exists. Skipping.", source_url, target_url)
                return

        # Ensure source resource exists or create a placeholder
        if not self.resource_exists(source_url):
            logger.info("Source resource %s not found. Creating placeholder.", source_url)
            placeholder_source = self._create_placeholder_resource(source_url)
            self.add_or_update_resource(placeholder_source)
        link.source_resource_id = self._nodes[source_url].id


        # Ensure target resource exists or create a placeholder
        if not self.resource_exists(target_url):
            logger.info("Target resource %s not found. Creating placeholder.", target_url)
            placeholder_target = self._create_placeholder_resource(target_url)
            self.add_or_update_resource(placeholder_target)
        link.target_resource_id = self._nodes[target_url].id


        # Ensure essential fields for the link itself
        self._edges.append(link)
        logger.info("Added link: %s -> %s", source_url, target_url)
        self._save_to_file() # Save after adding a link

    def get_links_from_resource(self, url: str) -> list[Link]:
        """Returns a list of links where source_url matches the given URL."""
        links = [edge for edge in self._edges if edge.source_url == url]
        logger.info("Found %d links from resource: %s", len(links), url)
        return links

    def get_links_to_resource(self, url: str) -> list[Link]:
        """Returns a list of links where target_url matches the given URL."""
        links = [edge for edge in self._edges if edge.target_url == url]
        logger.info("Found %d links to resource: %s", len(links), url)
        return links

    def get_all_resources(self) -> list[Resource]:
        """Returns a list of all resources."""
        all_nodes = list(self._nodes.values())
        logger.info("Retrieved %d resources.", len(all_nodes))
        return all_nodes

    def get_all_links(self) -> list[Link]:
        """Returns a list of all links."""
        logger.info("Retrieved %d links.", len(self._edges))
        return list(self._edges)

# For a usage demonstration, see examples/graph_db_interface_demo.py
