import requests
from bs4 import BeautifulSoup
from urllib.parse import urljoin, urlparse
import uuid
from datetime import datetime
import logging

logger = logging.getLogger(__name__)

class WebCrawler:
    def __init__(self, user_agent="KnowledgeGraphBot/0.1"):
        """
        Initializes a requests session with a user agent.
        """
        self.session = requests.Session()
        self.session.headers.update({'User-Agent': user_agent})
        # Future: Add more sophisticated session configuration (e.g., retries, proxies)

    def fetch_page(self, url: str) -> tuple[str | None, str | None]:
        """
        Fetches a page using the configured session.
        Handles basic errors and HTTP status codes.
        """
        # Future: Implement robots.txt checking before fetching.
        try:
            logger.info(f"Fetching URL: {url}")
            response = self.session.get(url, timeout=10)
            response.raise_for_status()  # Raises an HTTPError for bad responses (4XX or 5XX)
            logger.info(f"Successfully fetched URL: {url}, status: {response.status_code}")
            return response.text, None
        except requests.exceptions.HTTPError as e:
            logger.error(f"HTTP error for {url}: {e.response.status_code} - {e}")
            return None, f"HTTP error: {e.response.status_code}"
        except requests.exceptions.RequestException as e:
            # This catches other exceptions like ConnectionError, Timeout, etc.
            logger.error(f"Error fetching URL {url}: {e}")
            return None, f"Error fetching URL: {e}"

    def parse_html(self, html_content: str, base_url: str) -> tuple[str | None, list[dict]]:
        """
        Parses HTML content to extract title and links.
        Uses BeautifulSoup for basic parsing.
        """
        # Future: Implement more robust parsing (e.g., using lxml, advanced selectors).
        # Future: Extract more data (e.g., main content, metadata).
        if not html_content:
            return None, []

        soup = BeautifulSoup(html_content, 'html.parser')

        title_tag = soup.find('title')
        title = title_tag.string.strip() if title_tag else None

        links = []
        for a_tag in soup.find_all('a', href=True):
            href = a_tag['href']
            # Resolve relative URLs
            absolute_url = urljoin(base_url, href)
            # Basic validation to ensure it's a web link and not mailto: or js:
            parsed_url = urlparse(absolute_url)
            if parsed_url.scheme in ['http', 'https']:
                links.append({
                    "anchor_text": a_tag.string.strip() if a_tag.string else "",
                    "target_url": absolute_url
                })
        return title, links

    def crawl(self, url: str) -> tuple[dict | None, list[dict] | None, str | None]:
        """
        Orchestrates the crawling of a single URL.
        Fetches and parses the page, then transforms data into WebResource and Link structures.
        """
        logger.info(f"Starting crawl for URL: {url}")
        # Future: Handle redirects.

        html_content, error = self.fetch_page(url)

        if error:
            return None, None, error

        if not html_content:
            return None, None, "No content fetched."

        # For WebResource, we need an ID, the URL, the content (simplified to title for now),
        # and a crawl timestamp. Metadata can be added later.
        # For Links, we need an ID, source_resource_id (can be set post-crawl),
        # target_resource_id (will be the URL for now, resolved later), anchor_text, created_at.

        page_title, extracted_links = self.parse_html(html_content, url)

        # For now, content will be the page title.
        # In a real scenario, this would be the main textual content of the page.
        web_resource_data = {
            "id": str(uuid.uuid4()), # Generate a unique ID
            "url": url,
            "content": page_title if page_title else "N/A", # Simplified: using title as content
            "last_crawled_at": datetime.utcnow().isoformat(),
            "metadata": {"title": page_title if page_title else "N/A"} # Example metadata
        }

        links_data = []
        source_resource_id = web_resource_data["id"] # The ID of the page we just crawled

        for link_info in extracted_links:
            links_data.append({
                "id": str(uuid.uuid4()), # Generate a unique ID for the link
                "source_resource_id": source_resource_id,
                # target_resource_id will be resolved when the target URL is crawled and gets its own WebResource ID
                "target_url": link_info["target_url"],
                "anchor_text": link_info["anchor_text"],
                "created_at": datetime.utcnow().isoformat()
            })

        return web_resource_data, links_data, None

if __name__ == '__main__':
    # Basic logging setup for standalone script execution
    logging.basicConfig(level=logging.INFO, format='%(asctime)s - %(name)s - %(levelname)s - %(message)s')
    # Example Usage (for testing purposes)
    crawler = WebCrawler()

    # Test with the example.com URL
    print("Crawling http://example.com...")
    resource, links, error = crawler.crawl("http://example.com")
    if error:
        print(f"Error: {error}")
    else:
        print("\nWebResource Data:")
        print(resource)
        print("\nLinks Data:")
        for link in links:
            print(link)

    print("\n" + "="*50 + "\n")

    # Test with a non-existent URL
    print("Crawling http://nonexistentpage.com...")
    resource, links, error = crawler.crawl("http://nonexistentpage.com")
    if error:
        print(f"Error: {error}")
    else:
        print("\nWebResource Data:")
        print(resource)
        print("\nLinks Data:")
        for link in links:
            print(link)
