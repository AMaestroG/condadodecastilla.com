from graph_db_interface import GraphDBInterface, logger
"""Demonstration of GraphDBInterface usage."""

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
