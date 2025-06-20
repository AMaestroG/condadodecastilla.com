from content_processor import ContentProcessor
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
