import os
import tempfile
import unittest

from graph_db_interface import GraphDBInterface


class GraphDBInterfaceTestCase(unittest.TestCase):
    def setUp(self):
        # create temporary directory and switch into it so the DB file is isolated
        self.orig_cwd = os.getcwd()
        self.temp_dir = tempfile.TemporaryDirectory()
        os.chdir(self.temp_dir.name)
        self.db = GraphDBInterface()

    def tearDown(self):
        os.chdir(self.orig_cwd)
        self.temp_dir.cleanup()

    def test_add_or_update_resource_and_resource_exists(self):
        url = "http://example.com/page1"
        data = {"url": url, "content": "contenido"}
        self.db.add_or_update_resource(data)
        # verify the resource was added
        self.assertTrue(self.db.resource_exists(url))
        self.assertEqual(self.db.get_resource(url)["content"], "contenido")
        # update existing resource
        updated = {"url": url, "content": "actualizado"}
        self.db.add_or_update_resource(updated)
        self.assertEqual(self.db.get_resource(url)["content"], "actualizado")

    def test_add_link_creates_placeholders_and_prevents_duplicates(self):
        link = {"source_url": "http://src.com", "target_url": "http://dst.com"}
        self.db.add_link(link)
        # file should exist after save
        self.assertTrue(os.path.exists(self.db.db_filepath))
        self.assertEqual(len(self.db.get_all_links()), 1)
        # placeholders were created
        self.assertTrue(self.db.resource_exists("http://src.com"))
        self.assertTrue(self.db.resource_exists("http://dst.com"))
        # adding same link again should not create a duplicate
        self.db.add_link(link)
        self.assertEqual(len(self.db.get_all_links()), 1)

    def test_get_all_resources(self):
        res1 = {"url": "http://a.com", "content": "A"}
        res2 = {"url": "http://b.com", "content": "B"}
        self.db.add_or_update_resource(res1)
        self.db.add_or_update_resource(res2)
        urls = {r["url"] for r in self.db.get_all_resources()}
        self.assertEqual(urls, {"http://a.com", "http://b.com"})

    def test_get_links_from_resource(self):
        self.db.add_link({"source_url": "http://src.com", "target_url": "http://a.com"})
        self.db.add_link({"source_url": "http://src.com", "target_url": "http://b.com"})
        self.db.add_link({"source_url": "http://other.com", "target_url": "http://a.com"})

        links = self.db.get_links_from_resource("http://src.com")
        targets = {l["target_url"] for l in links}
        self.assertEqual(targets, {"http://a.com", "http://b.com"})

    def test_get_links_to_resource(self):
        self.db.add_link({"source_url": "http://src.com", "target_url": "http://dst.com"})
        self.db.add_link({"source_url": "http://other.com", "target_url": "http://dst.com"})
        self.db.add_link({"source_url": "http://src.com", "target_url": "http://else.com"})

        links = self.db.get_links_to_resource("http://dst.com")
        sources = {l["source_url"] for l in links}
        self.assertEqual(sources, {"http://src.com", "http://other.com"})


if __name__ == "__main__":
    unittest.main()
