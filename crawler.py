import requests
from bs4 import BeautifulSoup
from urllib.parse import urljoin, urlparse
import uuid
from datetime import datetime

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
        Simulates fetching a page.
        For now, it returns dummy HTML for 'http://example.com' and an error for others.
        """
        # Future: Implement actual HTTP GET requests.
        # Future: Implement robots.txt checking before fetching.
        # Future: Implement comprehensive error handling (timeouts, status codes, etc.).
        if url == "http://example.com":
            dummy_html = """
            <html>
            <head><title>Example Domain</title></head>
            <body>
            <h1>Example Domain</h1>
            <p>This domain is for use in illustrative examples in documents. You may use this
            domain in literature without prior coordination or asking for permission.</p>
            <p><a href="http://www.iana.org/domains/example">More information...</a></p>
            <a href="/another-page">Another Page</a>
            <a href="https://example.net/different-site">Different Site</a>
            </body>
            </html>
            """
            return dummy_html, None
        elif url == "http://example.com/another-page":
            dummy_html = """
            <html>
            <head><title>Another Page</title></head>
            <body>
            <h1>Another Page on Example Domain</h1>
            <p>This is another page for testing.</p>
            <a href="/">Back to Home</a>
            </body>
            </html>
            """
            return dummy_html, None
        return None, "Page not found or not implemented for this URL."

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
        # Future: Add logging throughout the process.
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

    # Test with another example.com URL
    print("Crawling http://example.com/another-page...")
    resource, links, error = crawler.crawl("http://example.com/another-page")
    if error:
        print(f"Error: {error}")
    else:
        print("\nWebResource Data:")
        print(resource)
        print("\nLinks Data:")
        for link in links:
            print(link)

    print("\n" + "="*50 + "\n")

    # Test with a non-implemented URL
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
