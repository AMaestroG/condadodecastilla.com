from graph_db_interface import GraphDBInterface # Assuming graph_db_interface.py is in the same directory
from datetime import datetime
import uuid
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity
import logging

logger = logging.getLogger(__name__)

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
        source_text = source_resource.get("content", "")
        target_text = target_resource.get("content", "")

        MIN_TEXT_LENGTH = 20  # Minimum characters for content to be considered for TF-IDF
        MIN_WORD_COUNT = 3    # Minimum words for content

        if not source_text or len(source_text.strip()) < MIN_TEXT_LENGTH or len(source_text.strip().split()) < MIN_WORD_COUNT:
            result["reason"] = f"Source resource {source_url} content is missing or too short for meaningful comparison."
            result["score"] = 0.0
            result["consistent"] = False
            return result
        if not target_text or len(target_text.strip()) < MIN_TEXT_LENGTH or len(target_text.strip().split()) < MIN_WORD_COUNT:
            result["reason"] = f"Target resource {target_url} content is missing or too short for meaningful comparison."
            result["score"] = 0.0
            result["consistent"] = False
            return result

        try:
            vectorizer = TfidfVectorizer()
            tfidf_matrix = vectorizer.fit_transform([source_text, target_text])
            similarity_score = cosine_similarity(tfidf_matrix[0:1], tfidf_matrix[1:2])[0][0]

            result["score"] = float(similarity_score)
            # threshold is already a parameter, defaults to 0.1
            result["consistent"] = similarity_score >= threshold
            if result["consistent"]:
                result["reason"] = f"Coherence score: {similarity_score:.2f} meets threshold ({threshold})."
            else:
                result["reason"] = f"Coherence score: {similarity_score:.2f} is below threshold ({threshold})."
        except Exception as e:
            result["reason"] = f"Error during TF-IDF calculation: {e}"
            result["score"] = 0.0
            result["consistent"] = False

        return result

    def analyze_graph_consistency(self) -> list[dict]:
        """
        Analyzes the entire graph for resource completeness and link coherence.
        Returns a list of identified issues.
        """
        all_issues = []

        logger.info("Starting analysis of resource completeness...")
        all_resources = self.db.get_all_resources() # db methods now use logging
        logger.info(f"Retrieved {len(all_resources)} resources for completeness check.")
        for resource in all_resources:
            completeness_check = self.check_resource_completeness(resource)
            if not completeness_check["complete"]:
                all_issues.append(completeness_check)
                logger.debug(f"Resource completeness issue found for {resource.get('url')}: {completeness_check['reason']}")

        logger.info("Starting analysis of link topical coherence...")
        all_links = self.db.get_all_links() # db methods now use logging
        logger.info(f"Retrieved {len(all_links)} links for coherence check.")
        for link in all_links:
            # Skip coherence check if source or target content is known to be insufficient
            # This pre-check can be based on resource completeness status if available
            # For now, check_link_topical_coherence handles missing content internally
            logger.debug(f"Checking link coherence for: {link.get('source_url')} -> {link.get('target_url')}")
            coherence_check = self.check_link_topical_coherence(link)
            if not coherence_check["consistent"]:
                all_issues.append(coherence_check)
                logger.debug(f"Link coherence issue found for {link.get('source_url')} -> {link.get('target_url')}: {coherence_check['reason']} (Score: {coherence_check['score']})")

        logger.info(f"Consistency analysis complete. Found {len(all_issues)} issues.")
        return all_issues

if __name__ == "__main__":
    # Basic logging setup for standalone script execution
    # This allows seeing logs from this script and from GraphDBInterface when run directly
    logging.basicConfig(level=logging.INFO, format='%(asctime)s - %(name)s - %(levelname)s - %(message)s')

    # 1. Instantiate GraphDBInterface and populate with sample data
    # GraphDBInterface will use its own logger, but output will be handled by basicConfig here.
    db = GraphDBInterface()

    # Resources
    res_comp_valid_id = str(uuid.uuid4())
    res_comp_valid = {
        "id": res_comp_valid_id, "url": "http://example.com/valid-apples", # Changed URL for clarity
        "content": "Detailed guide on apple harvesting techniques and best seasons for picking delicious apples.",
        "metadata": {"title": "Apple Harvesting Guide"}, "last_crawled_at": datetime.utcnow().isoformat()
    }
    db.add_or_update_resource(res_comp_valid)

    res_inc_no_title_id = str(uuid.uuid4()) # This resource will be linked to res_comp_valid
    res_inc_no_title = {
        "id": res_inc_no_title_id, "url": "http://example.com/bananas-no-title", # Changed URL
        "content": "Information about banana cultivation methods and the ideal climate for growing bananas.",
        "metadata": {}, "last_crawled_at": datetime.utcnow().isoformat() # No title
    }
    db.add_or_update_resource(res_inc_no_title)

    # New resource with similar content to res_comp_valid for testing high coherence
    res_similar_apples_id = str(uuid.uuid4())
    res_similar_apples = {
        "id": res_similar_apples_id, "url": "http://example.com/similar-apples",
        "content": "This article discusses apple farming, covering various picking season tips and apple types.",
        "metadata": {"title": "Apple Farming Tips"}, "last_crawled_at": datetime.utcnow().isoformat()
    }
    db.add_or_update_resource(res_similar_apples)

    res_inc_short_content_id = str(uuid.uuid4())
    res_inc_short_content = {
        "id": res_inc_short_content_id, "url": "http://example.com/short-content",
        "content": "Too short.", # Content too short
        "metadata": {"title": "Short Content Page"}, "last_crawled_at": datetime.utcnow().isoformat()
    }
    db.add_or_update_resource(res_inc_short_content)

    # Placeholder resource, created implicitly by a link later or explicitly
    # This one will have "N/A (placeholder)" content, now explicitly created before linking
    res_placeholder_id = str(uuid.uuid4())
    res_placeholder = {
        "id": res_placeholder_id, "url": "http://example.com/placeholder-page",
        "content": "N/A (placeholder)", # Explicitly set for clarity in tests
        "metadata": {"title": "Placeholder Page", "status": "placeholder"}, "last_crawled_at": datetime.utcnow().isoformat()
    }
    db.add_or_update_resource(res_placeholder)


    # Links
    # Link 1: Apples (valid) to Bananas (no-title) - Expect LOW coherence
    db.add_link({
        "id": str(uuid.uuid4()), "source_url": "http://example.com/valid-apples", "target_url": "http://example.com/bananas-no-title",
        "anchor_text": "Link from Apples to Bananas", "created_at": datetime.utcnow().isoformat()
    })

    # Link 2: Apples (valid) to Similar Apples - Expect HIGH coherence
    db.add_link({
        "id": str(uuid.uuid4()), "source_url": "http://example.com/valid-apples", "target_url": "http://example.com/similar-apples",
        "anchor_text": "Link from Apples to Similar Apples", "created_at": datetime.utcnow().isoformat()
    })

    # Link 3: Apples (valid) to Short Content - Expect INCONSISTENT (due to short content)
    db.add_link({
        "id": str(uuid.uuid4()), "source_url": "http://example.com/valid-apples", "target_url": "http://example.com/short-content",
        "anchor_text": "Link to short-content page", "created_at": datetime.utcnow().isoformat()
    })

    # Link 4: Apples (valid) to Placeholder Page - Expect INCONSISTENT (due to placeholder content)
    db.add_link({
        "id": str(uuid.uuid4()), "source_url": "http://example.com/valid-apples", "target_url": "http://example.com/placeholder-page",
        "anchor_text": "Link to placeholder page", "created_at": datetime.utcnow().isoformat()
    })

    # Link 5: Placeholder Page to Apples (valid) - Expect INCONSISTENT (due to source placeholder content)
    db.add_link({
        "id": str(uuid.uuid4()), "source_url": "http://example.com/placeholder-page", "target_url": "http://example.com/valid-apples",
        "anchor_text": "Link from placeholder page", "created_at": datetime.utcnow().isoformat()
    })

    # Link 6: Apples (valid) to Non-Existent Target - Expect INCONSISTENT (target becomes placeholder)
    db.add_link({
        "id": str(uuid.uuid4()), "source_url": "http://example.com/valid-apples", "target_url": "http://example.com/non-existent-target",
        "anchor_text": "Link to non-existent page", "created_at": datetime.utcnow().isoformat()
    })


    # 2. Instantiate ConsistencyAnalyzer
    analyzer = ConsistencyAnalyzer(db)

    # 3. Call analyze_graph_consistency() and print results
    print("\n--- Running Consistency Analysis ---")
    issues = analyzer.analyze_graph_consistency()

    if not issues:
        print("\nNo consistency issues found.")
    else:
        print(f"\nFound {len(issues)} consistency issues:")
        for issue in issues:
            print(f"  - Type: {issue['check_name']}")
            if "resource_url" in issue:
                print(f"    Resource URL: {issue['resource_url']}")
            if "link_id" in issue:
                 print(f"    Link ID: {issue['link_id']} (From: {issue['source_url']} To: {issue['target_url']})")
            print(f"    Status: {'Complete' if issue.get('complete', False) else 'Incomplete' if 'complete' in issue else ''}{'Consistent' if issue.get('consistent', False) else 'Inconsistent' if 'consistent' in issue else ''}")
            print(f"    Reason: {issue['reason']}")
            if "score" in issue:
                print(f"    Score: {issue['score']}")
            print("-" * 20)

    print("\n--- Verifying DB state after analysis (especially for placeholders) ---")
    all_db_resources = db.get_all_resources()
    print(f"Total resources in DB now: {len(all_db_resources)}")
    for res in all_db_resources:
        print(f"  URL: {res['url']}, Title: {res.get('metadata', {}).get('title', 'N/A')}, Content: '{res.get('content', '')[:30]}...'")
