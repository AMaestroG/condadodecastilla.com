import json
import os
import random  # <--- Add this import
import sys

# Ensure the scripts directory is in the Python path for module imports
# This is important if the script is run from the repository root.
current_dir = os.path.dirname(os.path.abspath(__file__))
if current_dir not in sys.path:
    sys.path.append(current_dir)

try:
    from ai_whisper_generator import generate_whisper
    from character_parser import parse_character_html
except ImportError as e:
    print(f"Error importing modules: {e}")
    print(
        "Please ensure character_parser.py and ai_whisper_generator.py are in the same directory or accessible in PYTHONPATH."
    )
    print(
        "If running from a different directory, ensure 'scripts' is in PYTHONPATH or adjust imports."
    )
    exit(1)

# Attempt to install BeautifulSoup if not present (for character_parser)
# This is primarily for the subtask runner; direct execution might need manual installation.
try:
    import bs4
except ImportError:
    print("BeautifulSoup4 not found for character_parser. Attempting to install...")
    try:
        import subprocess

        subprocess.check_call(
            [sys.executable, "-m", "pip", "install", "beautifulsoup4"]
        )
        print("BeautifulSoup4 installed successfully.")
        # Re-check import after installation attempt (though script would need restart for it to be available)
    except Exception as e:
        print(f"Failed to install beautifulsoup4: {e}")
        print("Please install it manually: pip install beautifulsoup4")
        # We don't exit here, character_parser might handle its own import error if installation fails mid-run.


def get_character_html_files(base_dir):
    """
    Traverses subdirectories of base_dir to collect HTML file paths,
    excluding specific index files.
    """
    html_files = []
    excluded_files = [
        "indice_personajes.html",
        "galeria_3d.html",
        "dummy_character.html",
        "dummy_no_hitos.html",
    ]

    # Ensure base_dir path is correct (e.g., relative to script location or repo root)
    # If this script is in 'scripts/' and base_dir is 'personajes/', adjust path:
    abs_base_dir = os.path.join(
        os.path.dirname(current_dir), base_dir
    )  # Go up one level from 'scripts'
    if not os.path.isdir(abs_base_dir):
        print(
            f"Error: Base directory '{abs_base_dir}' not found. Make sure it exists relative to the project root."
        )
        # Try base_dir as is, in case it's already a full path or correct relative path from execution point
        abs_base_dir = base_dir
        if not os.path.isdir(abs_base_dir):
            print(f"Error: Base directory '{base_dir}' also not found.")
            return []

    for root, _, files in os.walk(abs_base_dir):
        for file in files:
            if file.endswith(".html") and file not in excluded_files:
                full_path = os.path.join(root, file)
                # Normalize path to use forward slashes for consistency in JSON, especially if used by web part
                html_files.append(full_path.replace("\\", "/"))
    return html_files


def main():
    """
    Main execution logic to process character HTML files and generate enriched JSON data.
    """
    base_character_dir = "personajes/"
    output_dir = "assets/data/"
    output_file_name = "characters_enriched.json"

    # Adjust output_dir path to be relative to project root
    project_root = os.path.dirname(
        current_dir
    )  # Assumes script is in 'scripts/' directory
    abs_output_dir = os.path.join(project_root, output_dir)

    print(f"Starting character processing...")
    print(f"Character HTML base directory: {base_character_dir}")
    print(f"Output JSON directory: {abs_output_dir}")

    character_html_files = get_character_html_files(base_character_dir)

    if not character_html_files:
        print("No character HTML files found. Exiting.")
        return

    print(f"Found {len(character_html_files)} character HTML files to process.")

    enriched_characters_data = []
    coord_counter = 0
    processing_errors = 0

    for file_path in character_html_files:
        relative_file_path = file_path.replace(
            os.path.join(project_root, "").replace("\\", "/"), ""
        )
        print(f"\nProcessing: {relative_file_path}...")

        char_data = parse_character_html(file_path)

        if char_data and char_data.get(
            "name"
        ):  # Ensure name was parsed, as it's crucial
            whisper = generate_whisper(char_data)
            char_data["whisper"] = whisper

            # Assign placeholder 3D coordinates (simple linear arrangement)
            # For a spiral or more complex layout, this logic would be more involved.
            # Simple X-axis spread for now.
            char_data["coordinates"] = {
                "x": coord_counter * 2.5 - (len(character_html_files) * 2.5 / 2),
                "y": 0,
                "z": random.uniform(-2, 2),
            }  # Spread along X, slight Z variation
            coord_counter += 1

            # Ensure file_path in char_data is relative to the project root for web use
            # The get_character_html_files already gives paths relative to project root if called correctly
            # We need to ensure it's relative from where assets are served, usually project root.
            # Example: if project_root is /app, and file_path is /app/personajes/file.html
            # we want it to be "personajes/file.html" or "/personajes/file.html" depending on web server setup.
            # For now, let's assume paths from get_character_html_files are already appropriately relative
            # to the project root if the base_dir was correctly specified.
            # The `file_path` from `get_character_html_files` is an absolute path on the FS.
            # We should make it relative to the project root for the JSON.

            # Standardize path for web (relative to project root)
            # char_data['file_path'] should be like "personajes/category/name.html"
            web_accessible_path = os.path.relpath(file_path, project_root).replace(
                "\\", "/"
            )
            char_data["web_path"] = (
                f"/{web_accessible_path}"  # Prepend / for absolute path from site root
            )

            enriched_characters_data.append(char_data)
            print(
                f"Successfully processed and enriched: {char_data.get('name')} (Category: {char_data.get('category', 'N/A')})"
            )
            print(f"  Whisper: {char_data['whisper'][:50]}...")
            print(f"  Coordinates: {char_data['coordinates']}")
            print(f"  Web Path: {char_data['web_path']}")

        elif char_data:  # Parsed but no name, still log category if available
            processing_errors += 1
            print(
                f"Processed file (no name found, skipped): {relative_file_path} (Category: {char_data.get('category', 'N/A')})"
            )
        else:  # char_data is None
            processing_errors += 1
            print(f"Failed to parse (returned None): {relative_file_path}")

    if not enriched_characters_data:
        print(
            "\nNo characters were successfully processed. Output JSON will be empty or not created."
        )
        return

    # Ensure output directory exists
    os.makedirs(abs_output_dir, exist_ok=True)
    output_json_path = os.path.join(abs_output_dir, output_file_name)

    try:
        with open(output_json_path, "w", encoding="utf-8") as f:
            json.dump(enriched_characters_data, f, indent=4, ensure_ascii=False)
        print(f"\nSuccessfully wrote enriched character data to: {output_json_path}")
        print(f"Total characters processed: {len(enriched_characters_data)}")
        print(f"Processing errors (skipped files): {processing_errors}")
    except Exception as e:
        print(f"\nError writing JSON file: {e}")


if __name__ == "__main__":
    main()
    print("\n--- Character processing script finished ---")
