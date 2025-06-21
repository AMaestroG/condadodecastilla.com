import argparse
import logging
from crawler import WebCrawler
from graph_db_interface import GraphDBInterface, Resource, Link
from consistency_analyzer import ConsistencyAnalyzer

logging.basicConfig(level=logging.INFO,
                    format="%(asctime)s - %(levelname)s - %(message)s")


def update_graph(urls: list[str]) -> None:
    """Crawl given URLs, update the graph database and run consistency checks."""
    db = GraphDBInterface()
    crawler = WebCrawler()

    for url in urls:
        logging.info("Crawling %s", url)
        resource_data, links_data, error = crawler.crawl(url)
        if error:
            logging.error("Error crawling %s: %s", url, error)
            continue

        resource = Resource(**resource_data)
        db.add_or_update_resource(resource)

        for link_info in links_data:
            link = Link(
                id=link_info["id"],
                source_url=url,
                target_url=link_info["target_url"],
                anchor_text=link_info.get("anchor_text"),
                created_at=link_info["created_at"],
                source_resource_id=link_info.get("source_resource_id"),
            )
            db.add_link(link)

    analyzer = ConsistencyAnalyzer(db)
    issues = analyzer.analyze_graph_consistency()
    if issues:
        logging.info("Consistency issues detected: %d", len(issues))
    else:
        logging.info("No consistency issues detected")


if __name__ == "__main__":
    parser = argparse.ArgumentParser(description="Fetch pages and update the graph database")
    parser.add_argument("urls", nargs="+", help="URLs to crawl")
    args = parser.parse_args()
    update_graph(args.urls)
