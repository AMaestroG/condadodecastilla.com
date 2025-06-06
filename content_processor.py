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

if __name__ == '__main__':
    processor = ContentProcessor()

    sample_html = """
    <html>
    <head>
        <title>Sample Page</title>
        <style>body { font-size: 16px; }</style>
    </head>
    <body>
        <header><h1>Welcome!</h1></header>
        <script>alert('This is a script');</script>
        <main>
            <p>This is the main content of the page. It has <b>bold text</b> and <i>italic text</i>.</p>
            <p>Another paragraph here with a <a href="/en_construccion.html">link</a>.</p>
            <ul>
                <li>Item 1</li>
                <li>Item 2</li>
            </ul>
        </main>
        <footer><p>Copyright 2023</p></footer>
    </body>
    </html>
    """

    print("Original HTML:\n", sample_html)

    # Test extract_text_from_html
    extracted_text = processor.extract_text_from_html(sample_html)
    print("\nExtracted Text (from extract_text_from_html):\n", extracted_text)

    # Test extract_main_content
    main_content_text = processor.extract_main_content(sample_html)
    print("\nMain Content Text (from extract_main_content):\n", main_content_text)

    # Test process_content
    processed_data = processor.process_content(sample_html)
    print("\nProcessed Data (from process_content):\n", processed_data)

    # Test with empty HTML
    empty_html = ""
    print("\nProcessing empty HTML string:")
    processed_empty = processor.process_content(empty_html)
    print("Processed Data for empty HTML:\n", processed_empty)

    # Test with HTML having only script/style
    script_style_only_html = "<script>var x=1;</script><style>p{color:red;}</style>"
    print("\nProcessing HTML with only script/style tags:")
    processed_script_style = processor.process_content(script_style_only_html)
    print("Processed Data for script/style only HTML:\n", processed_script_style)
