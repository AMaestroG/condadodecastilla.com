import os
from html.parser import HTMLParser
from urllib.parse import urldefrag

class LinkExtractor(HTMLParser):
    def __init__(self, current_file_path):
        super().__init__()
        self.current_file_path = current_file_path
        self.links = []

    def handle_starttag(self, tag, attrs):
        if tag == 'a':
            attrs = dict(attrs)
            if 'href' in attrs:
                href = attrs['href']
                if not (href.startswith('http://') or \
                        href.startswith('https://') or \
                        href.startswith('mailto:') or \
                        href.startswith('tel:') or \
                        href.startswith('#') or \
                        href.startswith('?')):
                    self.links.append(href)

def find_html_files(repo_root):
    html_files = []
    for root, _, files in os.walk(repo_root):
        for file in files:
            if file.endswith(".html") or file.endswith(".htm"):
                html_files.append(os.path.join(root, file))
    return html_files

def resolve_link(source_html_path, link_href, repo_root):
    link_href_no_fragment, fragment = urldefrag(link_href)

    if link_href_no_fragment.startswith('/'):
        # Absolute link from repo root
        resolved_path_no_fragment = os.path.normpath(os.path.join(repo_root, link_href_no_fragment.lstrip('/')))
    else:
        # Relative link
        source_dir = os.path.dirname(source_html_path)
        resolved_path_no_fragment = os.path.normpath(os.path.join(source_dir, link_href_no_fragment))

    # Ensure the resolved path is still within the repo_root for sanity, though os.path.exists will be the final check
    # This is more about ensuring our resolved paths are consistently absolute from repo_root for reporting
    if not resolved_path_no_fragment.startswith(repo_root):
        # This case should ideally not happen if links are well-behaved repo internal links
        # but as a fallback, treat it as relative to repo root if it somehow escaped.
        # However, given the problem description, this path should be directly checkable.
        pass


    # Re-attach fragment for reporting purposes if the base link is broken
    resolved_path_with_fragment_for_reporting = resolved_path_no_fragment
    if fragment:
        resolved_path_with_fragment_for_reporting += "#" + fragment

    return resolved_path_no_fragment, resolved_path_with_fragment_for_reporting


def main():
    repo_root = os.path.abspath(os.path.join(os.path.dirname(__file__), '..'))
    # In the test environment, the script is in /app/scripts, so repo_root will be /app
    # If running locally, this needs adjustment or to be passed as an argument.
    # For the agent environment, we assume /app is the repo root.
    if not os.path.exists(os.path.join(repo_root, '.git')): # A simple check for repo root
        # Fallback if script is not in a direct 'scripts' subdir of the repo root
        # This might happen if the script is run from a different working directory
        # For the current setup, we assume /app as repo root.
        repo_root = "/app"


    html_files = find_html_files(repo_root)
    broken_links_report = []

    for html_file_path in html_files:
        try:
            with open(html_file_path, 'r', encoding='utf-8', errors='ignore') as f:
                content = f.read()
        except Exception as e:
            print(f"Error reading file {html_file_path}: {e}")
            continue

        # Relative path from repo root for reporting
        relative_html_file_path = os.path.relpath(html_file_path, repo_root)
        if not relative_html_file_path.startswith('/'):
             relative_html_file_path = '/' + relative_html_file_path


        parser = LinkExtractor(html_file_path)
        parser.feed(content)

        for link_href in parser.links:
            resolved_path_no_fragment, resolved_path_for_reporting = resolve_link(html_file_path, link_href, repo_root)

            # The path to check for existence is without the fragment
            # and it should be an absolute filesystem path.
            # resolved_path_no_fragment is already absolute.

            if not os.path.exists(resolved_path_no_fragment):
                # For reporting, we want the resolved path to be from repo root
                report_resolved_path = os.path.relpath(resolved_path_for_reporting, repo_root)
                if not report_resolved_path.startswith('/'):
                    report_resolved_path = '/' + report_resolved_path

                broken_links_report.append({
                    "source_file": relative_html_file_path,
                    "link_href": link_href,
                    "resolved_path": report_resolved_path
                })

    if broken_links_report:
        print("Broken links found:")
        for entry in broken_links_report:
            print(f"- Source HTML: {entry['source_file']}")
            print(f"  Link Href: {entry['link_href']}")
            print(f"  Resolved Path (Broken): {entry['resolved_path']}\n")
    else:
        print("No broken links found.")

if __name__ == "__main__":
    main()
