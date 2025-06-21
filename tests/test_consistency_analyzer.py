import unittest

from consistency_analyzer import ConsistencyAnalyzer


class FakeDB:
    def __init__(self, resources=None):
        self.resources = resources or {}

    def get_resource(self, url):
        return self.resources.get(url)


class ConsistencyAnalyzerTestCase(unittest.TestCase):
    def setUp(self):
        # prepare some resources for link coherence tests
        self.db = FakeDB(
            {
                "http://valid.com": {
                    "url": "http://valid.com",
                    "content": "content" * 20,
                    "metadata": {"title": "Valid"},
                },
                "http://short.com": {
                    "url": "http://short.com",
                    "content": "short",
                    "metadata": {"title": "Short"},
                },
                "http://placeholder.com": {
                    "url": "http://placeholder.com",
                    "content": "N/A (placeholder)",
                    "metadata": {"title": "Placeholder"},
                },
            }
        )
        self.analyzer = ConsistencyAnalyzer(self.db)

    def test_resource_completeness_valid(self):
        data = {
            "url": "http://example.com",
            "content": "A" * 60,
            "metadata": {"title": "Title"},
        }
        result = self.analyzer.check_resource_completeness(data)
        self.assertTrue(result["complete"])

    def test_resource_completeness_missing_title(self):
        data = {"url": "http://example.com", "content": "A" * 60, "metadata": {}}
        result = self.analyzer.check_resource_completeness(data)
        self.assertFalse(result["complete"])
        self.assertIn("Missing or empty title", result["reason"])

    def test_resource_completeness_short_content(self):
        data = {
            "url": "http://example.com",
            "content": "short",
            "metadata": {"title": "Title"},
        }
        result = self.analyzer.check_resource_completeness(data)
        self.assertFalse(result["complete"])
        self.assertIn("too short", result["reason"])

    def test_link_coherence_valid(self):
        link = {"source_url": "http://valid.com", "target_url": "http://short.com"}
        result = self.analyzer.check_link_topical_coherence(link)
        self.assertTrue(result["consistent"])

    def test_link_coherence_missing_source(self):
        link = {"source_url": "http://missing.com", "target_url": "http://short.com"}
        result = self.analyzer.check_link_topical_coherence(link)
        self.assertFalse(result["consistent"])
        self.assertIn("not found", result["reason"])

    def test_link_coherence_placeholder_target(self):
        link = {
            "source_url": "http://valid.com",
            "target_url": "http://placeholder.com",
        }
        result = self.analyzer.check_link_topical_coherence(link)
        self.assertFalse(result["consistent"])
        self.assertIn("placeholder", result["reason"])

    def test_link_coherence_missing_urls(self):
        result = self.analyzer.check_link_topical_coherence({})
        self.assertFalse(result["consistent"])
        self.assertIn("Missing source or target URL", result["reason"])


if __name__ == "__main__":
    unittest.main()
