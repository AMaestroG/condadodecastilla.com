#!/bin/bash

# Files
html_file="index.php"
css_file="assets/css/pages/index.css"
backup_html_file="index.php.bak_inline_style"
backup_css_file="assets/css/pages/index.css.bak_inline_style"

# Inline style to find and class to add
inline_style="style=\"margin-top: 2.5em;\""
# Note: Escaped quotes for sed. The actual style has unescaped quotes.
# A more robust regex might be needed if variants exist.
# For this specific known instance: <p style="margin-top: 2.5em;">
# We are looking for this exact string.

class_name="cta-group" # Class for the paragraph that contains a CTA button

# CSS rule to add
css_rule=".${class_name} {\n  margin-top: 2.5em;\n}"
# Escaped newline for sed append

echo "Starting HTML and CSS modification for inline style."

# Backup files
cp "$html_file" "$backup_html_file"
echo "Backed up $html_file to $backup_html_file"
if [ -f "$css_file" ]; then
    cp "$css_file" "$backup_css_file"
    echo "Backed up $css_file to $backup_css_file"
else
    echo "CSS file $css_file not found. Will create it."
    # Ensure directory exists if we need to create the file
    mkdir -p "$(dirname "$css_file")"
fi

# Modify HTML file
# Using sed to replace the specific paragraph tag.
# This is specific to the known structure: <p style="margin-top: 2.5em;"><a href="..." class="cta-button">...</a></p>
# It will add a class to the <p> tag and remove the style attribute.

# Target string: <p style="margin-top: 2.5em;">
# Replacement: <p class="cta-group">
# This is quite specific. Let's make it more robust by targeting any <p> with that exact style.
sed -i 's|<p style="margin-top: 2.5em;">|<p class="cta-group">|g' "$html_file"

# Check if replacement happened
if grep -q '<p class="cta-group">' "$html_file"; then
    echo "Successfully modified $html_file: added class '$class_name' and removed inline style."
else
    echo "ERROR: Failed to modify $html_file as expected. The specific <p> tag with the style might not have been found or sed command failed."
    # Restore from backup if sed failed to make the expected change
    cp "$backup_html_file" "$html_file"
    echo "Restored $html_file from backup."
    exit 1
fi

# Add CSS rule to CSS file
# Check if the rule already exists (simple check)
if grep -q ".${class_name} {" "$css_file"; then
    echo "CSS rule for .${class_name} already seems to exist in $css_file. Skipping addition."
else
    # Add the CSS rule to the end of the file
    echo -e "\n${css_rule}" >> "$css_file"
    echo "Added CSS rule for .${class_name} to $css_file"
fi

echo "HTML and CSS modification complete."

# Display changes for verification (optional, good for debugging)
echo "--- Changes in $html_file ---"
grep "class="${class_name}"" "$html_file"
echo "--- Content of $css_file ---"
cat "$css_file"
