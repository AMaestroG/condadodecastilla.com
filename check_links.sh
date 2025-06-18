#!/bin/bash
# Subtask to check for broken internal links in key files

# Files to check
files_to_check=("index.php" "_header.php" "_footer.php")

# Output file for broken links
output_file="broken_links_report.txt"
echo "Broken Link Report:" > "$output_file"
echo "---------------------" >> "$output_file"

# Function to check a single file's links
check_links_in_file() {
    local filepath="$1"
    echo "Checking links in: $filepath" >> "$output_file"

    # Extract href attributes from <a> tags using grep and sed
    # This regex tries to capture the value within href="..." or href='...'
    # It's a simplified regex and might not cover all edge cases.
    raw_hrefs=$(grep -Eo 'href=("[^"]*"|'"'[^']*'"')' "$filepath")
    hrefs=$(echo "$raw_hrefs" | sed -E 's/href=//; s/^"//; s/"$//; s/^'"'"'//; s/'"'"'$//')

    if [ -z "$hrefs" ]; then
        echo "No internal page/file links found to check in $filepath." >> "$output_file"
        return
    fi

    for link in $hrefs; do
        # Ignore external links, mailto, tel, #fragment links and empty links
        if [[ "$link" == http* || "$link" == mailto:* || "$link" == tel:* || "$link" == \#* || -z "$link" ]]; then
            continue
        fi

        # Skip pure language selector links like ?lang=es
        if [[ "$link" == \?lang=* ]]; then
            continue
        fi

        # Strip query parameters to check actual file path
        link_no_query="${link%%\?*}"

        # Normalize link: remove leading slash for ls compatibility if it's not the root
        normalized_link="$link_no_query"
        if [[ "$link_no_query" == /* ]]; then
            # If link starts with /, treat it as relative to repo root
            normalized_link="${link_no_query:1}"
        fi

        # Handle cases where normalized_link might be empty after stripping leading /
        if [ -z "$normalized_link" ]; then
            if [ "$link_no_query" == "/" ]; then # Special case for root link
                 if [ -f "index.php" ] || [ -f "index.html" ]; then
                    echo "  OK: $link (points to root)" >> "$output_file"
                else
                    echo "  BROKEN?: $link (points to root, but no index.php/html found immediately)" >> "$output_file"
                fi
            fi
            continue
        fi


        # Check if the file or directory exists
        # We also check for common index files if it's a directory path
        if [ -e "$normalized_link" ]; then
            echo "  OK: $link (points to $normalized_link)" >> "$output_file"
        elif [ -d "$normalized_link" ] && { [ -f "${normalized_link%/}/index.html" ] || [ -f "${normalized_link%/}/index.php" ]; }; then
            echo "  OK (Directory with index): $link (points to $normalized_link)" >> "$output_file"
        elif [[ "$normalized_link" == *.html || "$normalized_link" == *.php ]]; then
            # If it looks like a file and wasn't found directly
             echo "  BROKEN: $link (points to $normalized_link)" >> "$output_file"
        elif [[ ! "$normalized_link" =~ \. ]]; then
            # If it doesn't have an extension, it might be a directory without a trailing slash
            # or a clean URL that needs server-side rewriting (which we can't fully check here)
            # Let's assume it might be a directory for now.
             if [ -d "$normalized_link" ]; then
                echo "  OK (Directory): $link (points to $normalized_link)" >> "$output_file"
             else
                echo "  POTENTIALLY BROKEN (Clean URL or missing directory): $link (points to $normalized_link)" >> "$output_file"
             fi
        else
            # For other file types (e.g. images, css, js if they were in <a> tags by mistake)
            echo "  BROKEN (Resource): $link (points to $normalized_link)" >> "$output_file"
        fi
    done
    echo "" >> "$output_file"
}

# Check each file
for f in "${files_to_check[@]}"; do
    if [ -f "$f" ]; then
        check_links_in_file "$f"
    else
        echo "File not found: $f" >> "$output_file"
    fi
done

echo "Link checking complete. Report generated in $output_file"
cat "$output_file"
