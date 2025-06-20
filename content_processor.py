from bs4 import BeautifulSoup

class ContentProcessor:
    def extract_text_from_html(self, html_content: str) -> str:
        """
        Extracts all text from HTML content, removing script and style tags.
        """
        soup = BeautifulSoup(html_content, 'html.parser')

        # Remove script and style tags
        for script_or_style in soup(["script", "style"]):
            script_or_style.decompose()

        # Get text using stripped_strings to remove extra whitespace and join with spaces
        text_pieces = [text for text in soup.stripped_strings]
        cleaned_text = " ".join(text_pieces)

        return cleaned_text

    def extract_main_content(self, html_content: str) -> str:
        """
        Extracts the main content from HTML.
        Currently, this is a simplified version that extracts all text.
        """
        # Future: Enhance with more advanced techniques (e.g., using libraries
        # like newspaper3k or readability-lxml) for better main content identification.
        return self.extract_text_from_html(html_content)

    def process_content(self, html_content: str) -> dict:
        """
        Processes HTML content to extract text.
        The returned dictionary can be expanded with more NLP features in the future.
        """
        extracted_text = self.extract_main_content(html_content)

        # Future: Add more keys like "summary", "topics", "entities"
        # from NLP processing steps.
        return {"processed_text": extracted_text}

# For a usage demonstration, see examples/content_processor_demo.py
