from content_processor import ContentProcessor
import logging


def configure_logger() -> logging.Logger:
    """Return a logger configured once for this module."""
    logger = logging.getLogger(__name__)
    if not logger.handlers:
        logging.basicConfig(
            level=logging.INFO,
            format="%(asctime)s - %(name)s - %(levelname)s - %(message)s",
        )
    return logger


logger = configure_logger()
"""Demonstration of ContentProcessor usage."""

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

    logger.info("Original HTML:\n%s", sample_html)

    # Test extract_text_from_html
    extracted_text = processor.extract_text_from_html(sample_html)
    logger.info("\nExtracted Text (from extract_text_from_html):\n%s", extracted_text)

    # Test extract_main_content
    main_content_text = processor.extract_main_content(sample_html)
    logger.info("\nMain Content Text (from extract_main_content):\n%s", main_content_text)

    # Test process_content
    processed_data = processor.process_content(sample_html)
    logger.info("\nProcessed Data (from process_content):\n%s", processed_data)

    # Test with empty HTML
    empty_html = ""
    logger.info("\nProcessing empty HTML string:")
    processed_empty = processor.process_content(empty_html)
    logger.info("Processed Data for empty HTML:\n%s", processed_empty)

    # Test with HTML having only script/style
    script_style_only_html = "<script>var x=1;</script><style>p{color:red;}</style>"
    logger.info("\nProcessing HTML with only script/style tags:")
    processed_script_style = processor.process_content(script_style_only_html)
    logger.info("Processed Data for script/style only HTML:\n%s", processed_script_style)
