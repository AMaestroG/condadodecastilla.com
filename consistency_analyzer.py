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

class ConsistencyAnalyzer:
    def __init__(self, db_interface: GraphDBInterface):
        """
        Initializes the analyzer with a GraphDBInterface instance.
        """
        self.db = db_interface
        logger.info("ConsistencyAnalyzer initialized.")

    def check_resource_completeness(self, resource_data: dict) -> dict:
        """
        Analyzes a resource_data dictionary for completeness.
        Checks for title and minimum content length.
        """
        url = resource_data.get("url", "N/A")
        metadata = resource_data.get("metadata", {})
        title = metadata.get("title")
        # Assuming 'content' from resource_data is the processed_content for this check
        # In a more complex system, this might be a specific 'processed_content' field
        processed_content = resource_data.get("content", "")

        issues = []
        complete = True
        reason = "Resource is complete."

        if not title or title.strip() == "":
            issues.append("Missing or empty title.")
            complete = False

        if not processed_content or len(processed_content.strip()) <= 50:
            issues.append(f"Processed content is too short (length: {len(processed_content.strip())}).")
            complete = False

        if not complete:
            reason = "Issues found: " + " ".join(issues)

        return {
            "check_name": "resource_completeness",
            "resource_url": url,
            "resource_id": resource_data.get("id"),
            "complete": complete,
            "reason": reason,
        }

    def check_link_topical_coherence(self, link_data: dict, threshold=0.1) -> dict:
        """
        Checks topical coherence between source and target of a link.
        Placeholder logic: checks if both resources and their content exist.
        """
        link_id = link_data.get("id", "N/A")
        source_url = link_data.get("source_url")
        target_url = link_data.get("target_url")

        result = {
            "check_name": "link_topical_coherence",
            "link_id": link_id,
            "source_url": source_url,
            "target_url": target_url,
            "consistent": False, # Default to False
            "score": 0.0,
            "reason": ""
        }

        if not source_url or not target_url:
            result["reason"] = "Missing source or target URL in link data."
            return result

        source_resource = self.db.get_resource(source_url)
        target_resource = self.db.get_resource(target_url)

        if not source_resource:
            result["reason"] = f"Source resource {source_url} not found in DB."
            return result
        if not target_resource:
            result["reason"] = f"Target resource {target_url} not found in DB."
            return result

        # Using 'content' as placeholder for 'processed_content'
        source_content = source_resource.get("content", "")
        target_content = target_resource.get("content", "")

        if not source_content or source_content.strip() == "" or source_content == "N/A (placeholder)":
            result["reason"] = f"Missing or placeholder content for source resource {source_url}."
            return result
        if not target_content or target_content.strip() == "" or target_content == "N/A (placeholder)":
            result["reason"] = f"Missing or placeholder content for target resource {target_url}."
            return result

        # Future: Implement actual NLP similarity check here (e.g., TF-IDF, word embeddings, sentence transformers).
        # For now, if both have content, consider it coherent.
        result["consistent"] = True
        result["score"] = 1.0
        result["reason"] = "Placeholder: Both source and target pages have content. Actual NLP similarity check needed."

        return result

    def analyze_graph_consistency(self) -> list[dict]:
        """
        Analyzes the entire graph for resource completeness and link coherence.
        Returns a list of identified issues.
        """
        all_issues = []

        logger.info("\nAnalyzing resource completeness...")
        all_resources = self.db.get_all_resources()
        for resource in all_resources:
            completeness_check = self.check_resource_completeness(resource)
            if not completeness_check["complete"]:
                all_issues.append(completeness_check)

        logger.info("\nAnalyzing link topical coherence...")
        all_links = self.db.get_all_links()
        for link in all_links:
            # Skip coherence check if source or target content is known to be insufficient
            # This pre-check can be based on resource completeness status if available
            # For now, check_link_topical_coherence handles missing content internally
            coherence_check = self.check_link_topical_coherence(link)
            if not coherence_check["consistent"]:
                all_issues.append(coherence_check)

        logger.info(
            "\nConsistency analysis complete. Found %d issues.", len(all_issues)
        )
        return all_issues

if __name__ == "__main__":
    # 1. Instantiate GraphDBInterface and populate with sample data
    db = GraphDBInterface()

    # Resources
    res_comp_valid_id = str(uuid.uuid4())
    res_comp_valid = {
        "id": res_comp_valid_id, "url": "http://example.com/valid",
        "content": "This is a valid resource with sufficient processed content for analysis.",
        "metadata": {"title": "Valid Page"}, "last_crawled_at": datetime.utcnow().isoformat()
    }
    db.add_or_update_resource(res_comp_valid)

    res_inc_no_title_id = str(uuid.uuid4())
    res_inc_no_title = {
        "id": res_inc_no_title_id, "url": "http://example.com/no-title",
        "content": "This page has content but is missing a title for the check.",
        "metadata": {}, "last_crawled_at": datetime.utcnow().isoformat() # No title
    }
    db.add_or_update_resource(res_inc_no_title)

    res_inc_short_content_id = str(uuid.uuid4())
    res_inc_short_content = {
        "id": res_inc_short_content_id, "url": "http://example.com/short-content",
        "content": "Too short.", # Content too short
        "metadata": {"title": "Short Content Page"}, "last_crawled_at": datetime.utcnow().isoformat()
    }
    db.add_or_update_resource(res_inc_short_content)

    # Placeholder resource, created implicitly by a link later or explicitly
    # This one will have "N/A (placeholder)" content
    res_placeholder_id = str(uuid.uuid4())
    res_placeholder = {
        "id": res_placeholder_id, "url": "http://example.com/placeholder-page",
        "content": "N/A (placeholder)",
        "metadata": {"title": "Placeholder Page", "status": "placeholder"}, "last_crawled_at": datetime.utcnow().isoformat()
    }
    db.add_or_update_resource(res_placeholder)


    # Links
    # Link 1: Valid source and target with content
    db.add_link({
        "id": str(uuid.uuid4()), "source_url": "http://example.com/valid", "target_url": "http://example.com/no-title",
        "anchor_text": "Link to no-title page", "created_at": datetime.utcnow().isoformat()
    })

    # Link 2: Source has content, target has short/insufficient content
    db.add_link({
        "id": str(uuid.uuid4()), "source_url": "http://example.com/valid", "target_url": "http://example.com/short-content",
        "anchor_text": "Link to short-content page", "created_at": datetime.utcnow().isoformat()
    })

    # Link 3: Source has content, target is a placeholder page
    db.add_link({
        "id": str(uuid.uuid4()), "source_url": "http://example.com/valid", "target_url": "http://example.com/placeholder-page",
        "anchor_text": "Link to placeholder page", "created_at": datetime.utcnow().isoformat()
    })

    # Link 4: Source is a placeholder page
    db.add_link({
        "id": str(uuid.uuid4()), "source_url": "http://example.com/placeholder-page", "target_url": "http://example.com/valid",
        "anchor_text": "Link from placeholder page", "created_at": datetime.utcnow().isoformat()
    })

    # Link 5: Target resource does not exist at all (will be created as placeholder by add_link)
    db.add_link({
        "id": str(uuid.uuid4()), "source_url": "http://example.com/valid", "target_url": "http://example.com/non-existent-target",
        "anchor_text": "Link to non-existent page", "created_at": datetime.utcnow().isoformat()
    })


    # 2. Instantiate ConsistencyAnalyzer
    analyzer = ConsistencyAnalyzer(db)

    # 3. Call analyze_graph_consistency() and print results
    logger.info("\n--- Running Consistency Analysis ---")
    issues = analyzer.analyze_graph_consistency()

    if not issues:
        logger.info("\nNo consistency issues found.")
    else:
        logger.info("\nFound %d consistency issues:", len(issues))
        for issue in issues:
            logger.info("  - Type: %s", issue['check_name'])
            if "resource_url" in issue:
                logger.info("    Resource URL: %s", issue['resource_url'])
            if "link_id" in issue:
                logger.info(
                    "    Link ID: %s (From: %s To: %s)",
                    issue['link_id'],
                    issue['source_url'],
                    issue['target_url'],
                )
            logger.info(
                "    Status: %s%s",
                'Complete' if issue.get('complete', False) else 'Incomplete' if 'complete' in issue else '',
                'Consistent' if issue.get('consistent', False) else 'Inconsistent' if 'consistent' in issue else '',
            )
            logger.info("    Reason: %s", issue['reason'])
            if "score" in issue:
                logger.info("    Score: %s", issue['score'])
            logger.info("-" * 20)

    logger.info("\n--- Verifying DB state after analysis (especially for placeholders) ---")
    all_db_resources = db.get_all_resources()
    logger.info("Total resources in DB now: %d", len(all_db_resources))
    for res in all_db_resources:
        logger.info(
            "  URL: %s, Title: %s, Content: '%s...'",
            res['url'],
            res.get('metadata', {}).get('title', 'N/A'),
            res.get('content', '')[:30],
        )
