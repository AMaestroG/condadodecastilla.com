#!/bin/bash
# Subtask to check for broken internal links in key files AND HTML FRAGMENTS

# Files to check initially
files_to_check=("index.php" "_header.php" "_footer.php")

# HTML Fragments to also check (header components)
# Note: admin-menu.php is PHP; static analysis might be limited.
html_fragments=(
    "fragments/header/language-bar.html"
    "fragments/header/navigation.html"
    "fragments/menus/main-menu.html"
    "fragments/menus/admin-menu.php"
    "fragments/menus/social-menu.html"
)

# Combine the arrays
all_files_to_check=("${files_to_check[@]}" "${html_fragments[@]}")

# Files that were previously loaded dynamically but no longer exist should be skipped
skip_files=()

# Output file for broken links
output_file="broken_links_report_extended.txt"
echo "Broken Link Report (Extended):" > "$output_file"
echo "------------------------------" >> "$output_file"
echo "Checked on: $(date)" >> "$output_file"
echo "" >> "$output_file"

# Function to check a single file's links
check_links_in_file() {
    local filepath="$1"
    echo "Checking links in: $filepath" >> "$output_file"

    if [ ! -f "$filepath" ]; then
        echo "  File NOT FOUND: $filepath" >> "$output_file"
        echo "" >> "$output_file"
        return
    fi

    # Extract href attributes from <a> tags using grep and sed
    raw_hrefs=$(grep -Eo 'href=("[^"#]*"|'"'[^'#]*'"')' "$filepath")
    hrefs_values=$(echo "$raw_hrefs" | sed -E 's/href=//; s/^"//; s/"$//; s/^'"'"'//; s/'"'"'$//')

    # Extract src attributes from <img> and <script> tags
    raw_srcs=$(grep -Eo 'src=("[^"]*"|'"'[^']*'"')' "$filepath")
    srcs_values=$(echo "$raw_srcs" | sed -E 's/src=//; s/^"//; s/"$//; s/^'"'"'//; s/'"'"'$//')

    # Extract url() from CSS (simple version, might get false positives)
    raw_css_urls=$(grep -Eo 'url[[:space:]]*\([[:space:]]*[^)]+[[:space:]]*\)' "$filepath")
    css_urls_values=$(echo "$raw_css_urls" | sed -E 's/url[[:space:]]*\([[:space:]]*["'"'"']?//; s/["'"'"']?[[:space:]]*\)$//')

    all_links_values="${hrefs_values} ${srcs_values} ${css_urls_values}"

    if [ -z "$all_links_values" ]; then
        echo "  No internal page/file links found to check in $filepath." >> "$output_file"
        echo "" >> "$output_file"
        return
    fi

    found_links_count=0
    broken_links_count=0

    for link_value in $all_links_values; do # Renamed loop variable
        # Ignore external links, mailto, tel, data URIs, and empty links
        if [[ "$link_value" == http* || "$link_value" == mailto:* || "$link_value" == tel:* || "$link_value" == data:* || -z "$link_value" ]]; then
            continue
        fi

        ((found_links_count++))
        normalized_link="$link_value"
        original_link_for_reporting="$link_value" # Keep original for report

        # Handle absolute paths from root vs relative paths
        if [[ "$link_value" == /* ]]; then
            normalized_link="${link_value:1}" # Remove leading slash
        else
            # For relative links, prepend the directory of the file being checked if link is not already path-like
            if [[ ! "$link_value" == */* && "$filepath" == */* ]]; then
                 normalized_link="$(dirname "$filepath")/$link_value"
            fi
        fi

        # Clean up ./ if any
        normalized_link=$(echo "$normalized_link" | sed 's#\./##g')

        # Handle cases where normalized_link might be empty after stripping leading /
        if [ -z "$normalized_link" ]; then
            if [ "$original_link_for_reporting" == "/" ]; then
                 if [ -f "index.php" ] || [ -f "index.html" ]; then
                    echo "  OK (root): $original_link_for_reporting" >> "$output_file"
                else
                    echo "  BROKEN? (root): $original_link_for_reporting (no index.php/html found at root)" >> "$output_file"
                    ((broken_links_count++))
                fi
            fi
            continue # Skip if it became empty and wasn't just "/"
        fi

        if [ -e "$normalized_link" ]; then
            echo "  OK: $original_link_for_reporting (resolved to $normalized_link)" >> "$output_file"
        # Check for directory with index file
        elif [ -d "$normalized_link" ] && { [ -f "${normalized_link%/}/index.html" ] || [ -f "${normalized_link%/}/index.php" ]; }; then
            echo "  OK (Dir with Index): $original_link_for_reporting (resolved to $normalized_link)" >> "$output_file"
        else
            # More specific check for typical file extensions if not found directly
            if [[ "$normalized_link" == *.html || \
                  "$normalized_link" == *.php || \
                  "$normalized_link" == *.css || \
                  "$normalized_link" == *.js || \
                  "$normalized_link" == *.jpg || \
                  "$normalized_link" == *.jpeg || \
                  "$normalized_link" == *.png || \
                  "$normalized_link" == *.gif || \
                  "$normalized_link" == *.svg || \
                   "$normalized_link" == *.ico ]]; then
                echo "  BROKEN (File): $original_link_for_reporting (resolved to $normalized_link)" >> "$output_file"
                ((broken_links_count++))
            # Consider it a directory if it has no extension (could be clean URL)
            elif [[ ! "$normalized_link" =~ \. ]]; then
                if [ -d "$normalized_link" ]; then # Check if it's a directory
                    echo "  OK (Dir): $original_link_for_reporting (resolved to $normalized_link)" >> "$output_file"
                else
                    echo "  POTENTIALLY BROKEN (Clean URL or missing dir): $original_link_for_reporting (resolved to $normalized_link)" >> "$output_file"
                    # Not strictly incrementing broken_links_count for clean URLs as they might be valid server-side
                fi
            else # Other extensions not explicitly listed
                echo "  BROKEN (Resource/Other): $original_link_for_reporting (resolved to $normalized_link)" >> "$output_file"
                ((broken_links_count++))
            fi
        fi
    done

    if [ "$found_links_count" -eq 0 ]; then
        echo "  No processable internal links found in $filepath." >> "$output_file"
    elif [ "$broken_links_count" -gt 0 ]; then
        echo "  SUMMARY for $filepath: $broken_links_count broken link(s) out of $found_links_count processable links." >> "$output_file"
    else
        echo "  SUMMARY for $filepath: All $found_links_count processable links appear OK." >> "$output_file"
    fi
    echo "" >> "$output_file"
}

# Check each file
for f in "${all_files_to_check[@]}"; do
    skip=false
    for s in "${skip_files[@]}"; do
        if [[ "$f" == "$s" ]]; then
            skip=true
            break
        fi
    done
    if [ "$skip" = true ]; then
        continue
    fi
    check_links_in_file "$f"
done

echo "Extended link checking complete. Report generated in $output_file"
cat "$output_file"
