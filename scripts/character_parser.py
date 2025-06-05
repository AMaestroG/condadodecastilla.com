# pip install beautifulsoup4
import os
from bs4 import BeautifulSoup

def parse_character_html(file_path):
    """
    Parses an HTML file to extract character information.

    Args:
        file_path (str): The path to the HTML file.

    Returns:
        dict: A dictionary containing extracted character information,
              or None if the file cannot be processed or essential elements are missing.
    """
    # Extract category from file path first
    category_name = "Unknown" # Default
    try:
        # e.g., file_path can be "personajes/Condes_Alava/rodrigo.html" or an absolute path from testing
        # We want the name of the directory containing the file, relative to "personajes" or just the immediate parent.
        parent_dir_path = os.path.dirname(file_path)
        category_name_candidate = os.path.basename(parent_dir_path)

        # Avoid using "personajes" itself as a category if the file is directly under it
        # or if the path is already relative like "Condes_Alava/rodrigo.html"
        if category_name_candidate and category_name_candidate.lower() != "personajes":
            category_name = category_name_candidate
        elif category_name_candidate.lower() == "personajes": # File is directly under "personajes"
             # Try to see if the file_path itself was like "personajes/SomeCategory/file.html"
             # and dirname gave "personajes/SomeCategory", then basename gave "SomeCategory" - this is good.
             # If dirname was "personajes", then it means file is like "personajes/file.html" -> category "Unknown"
             # This logic is a bit tricky due to how base_dir is handled in process_characters.py
             # Let's assume for now if parent_dir_path ends with 'personajes', it's an unknown category,
             # unless the file_path itself is like 'Category/file.html' (then parent_dir_path is 'Category')
             # This part might need refinement based on how paths are consistently passed.
             # The current logic in get_character_html_files returns full absolute paths.
             # So, os.path.basename(os.path.dirname(file_path)) is the most direct parent.
             # If this parent is 'personajes', then the category is 'Unknown'.

             # Let's re-evaluate: if path is /abs/path/to/repo/personajes/Category/file.html
             # dirname is /abs/path/to/repo/personajes/Category
             # basename is Category - this is correct.
             # if path is /abs/path/to/repo/personajes/file.html
             # dirname is /abs/path/to/repo/personajes
             # basename is personajes - this should be Unknown.
            pass # category_name remains "Unknown" if os.path.basename(parent_dir_path) == "personajes"

    except Exception as e:
        # This error would be very unusual for basic path operations
        print(f"Error extracting category from path {file_path}: {e}")
        category_name = "Unknown"

    character_data = {
        'name': None,
        'bio_snippet': None,
        'key_facts': [],
        'file_path': file_path,
        'category': category_name
    }

    try:
        with open(file_path, 'r', encoding='utf-8') as f:
            html_content = f.read()
    except FileNotFoundError:
        print(f"Error: File not found at {file_path}")
        return None
    except Exception as e:
        print(f"Error reading file {file_path}: {e}")
        return None

    soup = BeautifulSoup(html_content, 'html.parser')

    # 1. Extract Name
    # Try from <header class="page-header-personaje"> <h1>
    name_header = soup.find('header', class_='page-header-personaje')
    if name_header:
        h1_tag = name_header.find('h1')
        if h1_tag:
            character_data['name'] = h1_tag.get_text(strip=True)

    # If not found, try from <title>
    if not character_data['name']:
        title_tag = soup.find('title')
        if title_tag:
            title_text = title_tag.get_text(strip=True)
            # Clean up common suffixes
            suffixes_to_remove = ["- Condado de Castilla", "- Historia y Legado"]
            for suffix in suffixes_to_remove:
                if title_text.endswith(suffix):
                    title_text = title_text[:-len(suffix)].strip()
            character_data['name'] = title_text

    if not character_data['name']:
         # Fallback if no name could be extracted from common locations
        page_header_generic = soup.find('header', class_='page-header')
        if page_header_generic:
            h1_generic = page_header_generic.find('h1')
            if h1_generic:
                 character_data['name'] = h1_generic.get_text(strip=True)


    # 2. Extract Biography Snippet
    # Look for <div class="content-wrapper"> or <div class="page-content-block personaje-detail-content">
    content_wrapper = soup.find('div', class_='content-wrapper')
    if not content_wrapper:
        content_wrapper = soup.find('div', class_='page-content-block personaje-detail-content')

    if content_wrapper:
        # Try to find a <p> that is a direct child or within a few levels
        # and seems like an introduction (not inside a specific sub-section like blockquote or alert)
        intro_paragraph = None
        for p in content_wrapper.find_all('p', recursive=True):
            # Avoid paragraphs inside tables, blockquotes, or other specific containers if needed
            if not p.find_parent('table') and not p.find_parent('blockquote') and not p.find_parent(class_='alert'):
                text = p.get_text(strip=True)
                if len(text) > 50: # Assume an intro paragraph has some substance
                    intro_paragraph = text
                    break
        if intro_paragraph:
            character_data['bio_snippet'] = intro_paragraph[:300] + '...' if len(intro_paragraph) > 300 else intro_paragraph
        elif content_wrapper.find('p'): # Fallback to the very first paragraph if no better candidate
             first_p_text = content_wrapper.find('p').get_text(strip=True)
             character_data['bio_snippet'] = first_p_text[:300] + '...' if len(first_p_text) > 300 else first_p_text


    # 3. Extract Key Facts (Hitos Importantes)
    hitos_heading = soup.find(['h2', 'h3'], string=lambda text: text and "Hitos Importantes" in text)
    if not hitos_heading: # Try another common heading
        hitos_heading = soup.find(['h2', 'h3'], string=lambda text: text and "Legado y Relevancia" in text)

    if hitos_heading:
        next_ul = hitos_heading.find_next_sibling('ul')
        if next_ul:
            for li in next_ul.find_all('li'):
                fact_text = li.get_text(strip=True)
                if fact_text: # Ensure not empty
                    character_data['key_facts'].append(fact_text)
        else: # Sometimes facts are in <p> tags after the heading
            next_element = hitos_heading.find_next_sibling()
            while next_element and next_element.name == 'p':
                fact_text = next_element.get_text(strip=True)
                if fact_text:
                     # Try to split if it's a long paragraph with multiple facts
                    if '. ' in fact_text or '; ' in fact_text:
                        facts = [f.strip() for f in fact_text.replace('; ', '. ').split('. ') if f.strip()]
                        character_data['key_facts'].extend(facts)
                    else:
                        character_data['key_facts'].append(fact_text)
                next_element = next_element.find_next_sibling()


    # Basic validation: if no name was found, it's probably not a valid character page for our purposes
    if not character_data['name']:
        # print(f"Warning: Could not extract character name from {file_path}. Skipping.")
        return None # Or return the partial data if that's preferred

    return character_data

if __name__ == '__main__':
    # Attempt to install BeautifulSoup if not present and in an environment where this is allowed.
    # This is primarily for the subtask runner; direct execution might need manual installation.
    try:
        import bs4
    except ImportError:
        print("BeautifulSoup4 not found. Attempting to install...")
        try:
            import subprocess
            import sys
            subprocess.check_call([sys.executable, "-m", "pip", "install", "beautifulsoup4"])
            print("BeautifulSoup4 installed successfully.")
        except Exception as e:
            print(f"Failed to install beautifulsoup4: {e}")
            print("Please install it manually: pip install beautifulsoup4")
            exit(1)

    # Example usage:
    # Ensure this path is relative to the root of the repository where the script is run from,
    # or use an absolute path for testing.
    # For the subtask, we assume the script might be run from the repo root.

    # Create a dummy HTML file for testing if it doesn't exist
    # Modify path to include a test category
    dummy_category_name = "Test_Category_Dummies"
    dummy_html_path = os.path.join("personajes", dummy_category_name, "dummy_character.html")

    dummy_html_content = """
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Personaje de Prueba - Condado de Castilla</title>
    </head>
    <body>
        <header class="page-header-personaje">
            <h1>Nombre del Personaje de Prueba</h1>
        </header>
        <div class="page-content-block personaje-detail-content">
            <p>Esta es una breve biografía introductoria del personaje de prueba. Es una figura muy importante en la historia de la región, conocida por sus grandes hazañas y su impacto duradero. Este texto es lo suficientemente largo como para ser truncado si supera los 300 caracteres, lo que permite probar la funcionalidad de recorte del snippet biográfico.</p>
            <p>Otro párrafo que no debería ser el snippet principal.</p>
            <h3>Hitos Importantes</h3>
            <ul>
                <li>Primer hito importante del personaje.</li>
                <li>Segundo hito, demostrando su valentía.</li>
                <li>Tercer acontecimiento relevante en su vida.</li>
            </ul>
            <h3>Legado y Relevancia</h3>
            <ul>
                <li>Legado uno.</li>
                <li>Legado dos con más detalles.</li>
            </ul>
        </div>
    </body>
    </html>
    """
    # Ensure the directory exists
    os.makedirs(os.path.dirname(dummy_html_path), exist_ok=True)
    with open(dummy_html_path, 'w', encoding='utf-8') as f:
        f.write(dummy_html_content)
    print(f"Created dummy HTML file at: {dummy_html_path}")

    print(f"\n--- Parsing {dummy_html_path} ---")
    parsed_data = parse_character_html(dummy_html_path)
    if parsed_data:
        print(f"Name: {parsed_data['name']}")
        print(f"Bio Snippet: {parsed_data['bio_snippet']}")
        print(f"Key Facts: {parsed_data['key_facts']}")
        print(f"File Path: {parsed_data['file_path']}")
        print(f"Category: {parsed_data['category']} (Expected: {dummy_category_name})")
    else:
        print("No data parsed or file not suitable.")

    # Test with a non-existent file
    print(f"\n--- Parsing non_existent_file.html ---")
    parsed_data_non_existent = parse_character_html("personajes/non_existent_file.html")
    if parsed_data_non_existent:
        print("Parsed data from non-existent file (should not happen).")
    else:
        print("Correctly handled non-existent file.")

    # Test with a file that might be missing some elements (e.g. no Hitos)
    dummy_no_hitos_path = "personajes/dummy_no_hitos.html"
    dummy_no_hitos_content = """
    <!DOCTYPE html><html lang="es"><head><title>Sin Hitos - Condado de Castilla</title></head>
    <body><header class="page-header-personaje"><h1>Sin Hitos</h1></header>
    <div class="page-content-block personaje-detail-content"><p>Biografía breve.</p></div></body></html>
    """
    os.makedirs(os.path.dirname(dummy_no_hitos_path), exist_ok=True)
    with open(dummy_no_hitos_path, 'w', encoding='utf-8') as f:
        f.write(dummy_no_hitos_content)
    print(f"Created dummy HTML file (no hitos) at: {dummy_no_hitos_path}")

    print(f"\n--- Parsing {dummy_no_hitos_path} ---")
    parsed_data_no_hitos = parse_character_html(dummy_no_hitos_path)
    if parsed_data_no_hitos:
        print(f"Name: {parsed_data_no_hitos['name']}")
        print(f"Bio Snippet: {parsed_data_no_hitos['bio_snippet']}")
        print(f"Key Facts: {parsed_data_no_hitos['key_facts']} (Expected: [])")
        print(f"File Path: {parsed_data_no_hitos['file_path']}")
        print(f"Category: {parsed_data_no_hitos['category']} (Expected: personajes or Unknown based on path)")
    else:
        print("No data parsed or file not suitable for no-hitos test.")

    # Test with a real file path if available (example, adjust if needed)
    # This path needs to exist in the repo structure when the script is run.
    # Example: real_file_path = "personajes/Condes_de_Castilla_Alava_y_Lantaron/rodrigo_el_conde.html"
    # if os.path.exists(real_file_path):
    #     print(f"\n--- Parsing {real_file_path} (if exists) ---")
    #     parsed_data_real = parse_character_html(real_file_path)
    #     if parsed_data_real:
    #         import json
    #         print(json.dumps(parsed_data_real, indent=4, ensure_ascii=False))
    #     else:
    #         print(f"Could not parse {real_file_path} or file not found.")
    # else:
    #     print(f"\nSkipping test for real file {real_file_path} as it does not exist at this location.")

    print("\n--- Parser script finished ---")
