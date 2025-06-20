from link_discoverer import LinkDiscoverer
from graph_db_interface import GraphDBInterface
import uuid
from datetime import datetime
"""Demonstration of LinkDiscoverer usage."""

if __name__ == "__main__":
    # 1. Instantiate GraphDBInterface and populate with sample data
    db = GraphDBInterface()

    # Resource 1: Fully "crawled" (has URL and content)
    crawled_res_id = str(uuid.uuid4())
    crawled_res_url = "http://example.com/crawled-page"
    db.add_or_update_resource({
        "id": crawled_res_id, "url": crawled_res_url,
        "content": "This is the detailed processed content of the crawled page. It's long enough.",
        "metadata": {"title": "Crawled Page Title"}, "last_crawled_at": datetime.utcnow().isoformat()
    })

    # Resource 2: Exists but lacks "processed_content" (content is empty)
    no_content_res_id = str(uuid.uuid4())
    no_content_res_url = "http://example.com/no-content-page"
    db.add_or_update_resource({
        "id": no_content_res_id, "url": no_content_res_url,
        "content": "", # Empty content
        "metadata": {"title": "No Content Page Title"}, "last_crawled_at": datetime.utcnow().isoformat()
    })

    # Resource 3: Exists but content is placeholder
    placeholder_content_res_id = str(uuid.uuid4())
    placeholder_content_res_url = "http://example.com/placeholder-content.html"
    db.add_or_update_resource({
        "id": placeholder_content_res_id, "url": placeholder_content_res_url,
        "content": "N/A (placeholder)",
        "metadata": {"title": "Placeholder Content Page Title"}, "last_crawled_at": datetime.utcnow().isoformat()
    })


    # Links from the "crawled" resource
    # Link to the resource without content
    db.add_link({
        "id": str(uuid.uuid4()), "source_url": crawled_res_url, "target_url": no_content_res_url,
        "anchor_text": "Link to no-content", "created_at": datetime.utcnow().isoformat()
    })

    # Link to the resource with placeholder content
    db.add_link({
        "id": str(uuid.uuid4()), "source_url": crawled_res_url, "target_url": placeholder_content_res_url,
        "anchor_text": "Link to placeholder-content", "created_at": datetime.utcnow().isoformat()
    })

    # Link to a URL that doesn't exist as a resource yet
    non_existent_url1 = "http://example.com/non-existent-page1"
    db.add_link({
        "id": str(uuid.uuid4()), "source_url": crawled_res_url, "target_url": non_existent_url1,
        "anchor_text": "Link to non-existent 1", "created_at": datetime.utcnow().isoformat()
    })

    # Link to another URL that doesn't exist as a resource yet
    non_existent_url2 = "http://example.com/non-existent-page2"
    db.add_link({
        "id": str(uuid.uuid4()), "source_url": crawled_res_url, "target_url": non_existent_url2,
        "anchor_text": "Link to non-existent 2", "created_at": datetime.utcnow().isoformat()
    })

    # Link from no_content_res_url to crawled_res_url (to ensure it's not picked as uncrawled)
    db.add_link({
        "id": str(uuid.uuid4()), "source_url": no_content_res_url, "target_url": crawled_res_url,
        "anchor_text": "Link back to crawled", "created_at": datetime.utcnow().isoformat()
    })


    # 2. Instantiate LinkDiscoverer
    discoverer = LinkDiscoverer(db)

    # 3. Call find_uncrawled_links()
    print("\n--- Finding Uncrawled Links ---")
    uncrawled = discoverer.find_uncrawled_links(limit=5)
    print("\nUncrawled URLs found:")
    if uncrawled:
        for url in uncrawled:
            print(f"  - {url}")
    else:
        print("  No uncrawled URLs found (up to limit).")

    # Expected: no_content_res_url, placeholder_content_res_url, non_existent_url1, non_existent_url2

    # 4. Call suggest_search_queries_from_topics()
    print("\n--- Suggesting Search Queries ---")
    queries = discoverer.suggest_search_queries_from_topics(top_n_resources=3)
    print("\nSuggested Search Queries:")
    if queries:
        for q in queries:
            print(f"  - \"{q}\"")
    else:
        print("  No queries suggested.")

    # Expected queries based on titles of "Crawled Page Title", "No Content Page Title", "Placeholder Content Page Title"

    print("\n--- Verifying DB state (placeholders for non-existent links are created by GraphDBInterface.add_link) ---")
    all_db_resources = db.get_all_resources()
    print(f"Total resources in DB: {len(all_db_resources)}")
    # for res in all_db_resources:
    #     print(f"  URL: {res['url']}, Content: '{res.get('content', '')[:30]}...'")
    assert db.resource_exists(non_existent_url1) # Should have been created as placeholder by add_link
    assert db.resource_exists(non_existent_url2) # Should have been created as placeholder by add_link

    res_ne1 = db.get_resource(non_existent_url1)
    assert res_ne1 and res_ne1.get("content") == "N/A (placeholder)"

    print("\nDemo complete.")
