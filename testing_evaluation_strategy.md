# Knowledge Graph System: Testing and Evaluation Strategy

## 1. Introduction

The purpose of testing and evaluation is to ensure the knowledge graph system is robust, reliable, accurate, and performs efficiently. This strategy outlines the different types of testing to be employed, key metrics for evaluation, methods for carrying out these evaluations, and an initial focus for testing efforts.

## 2. Types of Testing

*   **Unit Tests:**
    *   Focus: Testing individual functions or methods within each module in isolation.
    *   Goal: Verify that each component behaves as expected given specific inputs.
    *   Example: Testing `ContentProcessor.extract_text_from_html()` with various HTML snippets to ensure correct text extraction and removal of script/style tags.

*   **Integration Tests:**
    *   Focus: Testing the interaction between different modules.
    *   Goal: Ensure that modules work together correctly and data flows as expected.
    *   Example: Testing the flow from `WebCrawler` fetching a page, to `ContentProcessor` processing it, and then `GraphDBInterface` storing the results.

*   **End-to-End (E2E) System Tests:**
    *   Focus: Testing the entire system workflow from a user's perspective or a complete operational cycle.
    *   Goal: Validate the overall functionality and ensure all parts integrate to achieve the system's objectives.
    *   Example: Using the `CLI` to add a new URL, then verifying the resource and its links are correctly represented in the graph and that consistency checks report expected outcomes.

*   **Performance Tests:**
    *   Focus: Evaluating the system's responsiveness, stability, and scalability under load.
    *   Goal: Identify bottlenecks, measure resource utilization (CPU, memory, I/O), and ensure the system can handle expected data volumes and request rates.
    *   Example: Measuring the time taken to crawl and process a large number of pages, or the query time for specific graph patterns.

## 3. Key Metrics for Evaluation

*   **Graph Coverage & Scale:**
    *   `num_resources`: Total number of unique web resources in the graph.
    *   `num_links`: Total number of links (relationships) between resources.
    *   Rate of growth of resources and links over time.

*   **Content Quality:**
    *   `% resources with processed_content`: Percentage of resources for which textual content has been successfully extracted.
    *   `% resources with titles`: Percentage of resources that have a valid title.
    *   Average length of `processed_content`.
    *   (Future) `num_duplicate_content_detected`.

*   **Link Relevance (Future):**
    *   Average `topical_coherence_score` for links (requires actual NLP similarity).
    *   Percentage of links deemed "highly coherent" vs. "low coherence".

*   **Consistency & Accuracy:**
    *   `num_issues_from_ConsistencyAnalyzer`: Number and types of issues reported (e.g., incomplete resources, broken links, incoherent links).
    *   `accuracy_of_extracted_data` (e.g., title, main content) based on sampling and comparison with ground truth.
    *   `num_broken_links_detected_by_validation`.

*   **Crawler Performance:**
    *   `pages_crawled_per_unit_time`.
    *   `http_error_rates` during crawling (e.g., 404s, 500s).
    *   `avg_time_per_page_crawl_and_process`.
    *   `num_robots_txt_blocks_respected`.

## 4. Evaluation Methods

*   **Automated Test Suites:**
    *   Implementing unit, integration, and some E2E tests using frameworks like `unittest` or `pytest`.
    *   Running these tests regularly (e.g., on every commit via CI/CD).

*   **Golden Datasets / Benchmarks:**
    *   Creating or using predefined sets of web pages with known characteristics and expected extraction results.
    *   Running the system against these datasets to measure accuracy and performance changes over time.

*   **Manual Inspection and Sampling:**
    *   Periodically reviewing samples of graph data (resources, links, processed content) to identify qualitative issues or anomalies not caught by automated tests.

*   **Log Analysis:**
    *   Analyzing system logs (from crawler, processor, DB interface) to identify errors, performance trends, and operational patterns.

## 5. Initial Testing Focus

*   **Unit Tests:** Prioritize creating unit tests for core, complex, or critical functions within each module. Examples include:
    *   `ContentProcessor`: Text extraction, main content identification.
    *   `GraphDBInterface`: Resource/link addition, retrieval, placeholder logic.
    *   `WebCrawler`: URL fetching (mocked), HTML parsing logic.
    *   `ConsistencyAnalyzer`: Specific check functions.
    *   `LinkDiscoverer`: Uncrawled link identification.
*   **Basic Integration Tests:**
    *   Use the `CLI` as a primary means for initial integration tests, scripting sequences of commands and verifying outputs or DB state (e.g., checking the `knowledge_graph_db.json` file).
    *   Directly call sequences of module methods in test scripts to verify interactions without the full CLI overhead.

## 6. Example Unit Test (Illustrative)

The following is a conceptual Python code snippet showing unit tests for the `ContentProcessor.extract_text_from_html` method, typically using the `unittest` framework. This code would be placed in a dedicated test file (e.g., `tests/test_content_processor.py`) and executed by a test runner.

```python
# In a file like tests/test_content_processor.py
import unittest
# Assuming content_processor.py is in the python path or project structure allows direct import
from content_processor import ContentProcessor

class TestContentProcessor(unittest.TestCase):
    def setUp(self):
        self.processor = ContentProcessor()

    def test_extract_text_simple_html(self):
        html = "<html><head><title>Test Page Title</title></head><body><p>Hello World content.</p></body></html>"
        # Depending on how extract_text_from_html is implemented,
        # it might include the title text or only body text.
        # For this example, assume it extracts text from title and body.
        expected_text = "Test Page Title Hello World content."
        self.assertEqual(self.processor.extract_text_from_html(html), expected_text)

    def test_extract_text_with_scripts_styles(self):
        html = "<html><head><style>.foo{color:red}</style></head><body><script>alert('hi this is script')</script>Just the main text.</body></html>"
        expected_text = "Just the main text."
        self.assertEqual(self.processor.extract_text_from_html(html), expected_text)

    def test_extract_text_empty_input(self):
        html = ""
        expected_text = ""
        self.assertEqual(self.processor.extract_text_from_html(html), expected_text)

    def test_extract_text_only_scripts_styles(self):
        html = "<script>var x=1;</script><style>p{color:red;}</style>"
        expected_text = ""
        self.assertEqual(self.processor.extract_text_from_html(html), expected_text)

# To run these tests (if this file were executed directly):
# if __name__ == '__main__':
#     unittest.main()
```

This strategy will evolve as the system grows in complexity and features. Regular review and adaptation of the testing and evaluation approach will be necessary.
```

## Troubleshooting

If `npm run test:puppeteer` exits immediately or shows a connection error, the embedded PHP server may not have started correctly.

1. Check `/tmp/php-server.log` for startup messages or errors.
2. Make sure no other service is using port `8080`.
3. Stop stray PHP processes with `pkill -f 'php -S'` and rerun the tests.

You can also launch the server manually with `php -S localhost:8080` and confirm it responds at <http://localhost:8080> before executing the test suite.
