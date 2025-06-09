#!/bin/bash

echo "Starting .card refactoring in epic_theme.css..."

css_file="assets/css/epic_theme.css"
backup_file="assets/css/epic_theme.css.bak_card_refactor"

if [ ! -f "$css_file" ]; then
    echo "ERROR: $css_file not found."
    exit 1
fi

cp "$css_file" "$backup_file"
echo "Backed up $css_file to $backup_file."

# The file structure for .card is:
# 1. Base .card-grid
# 2. Base .card
# 3. Base .card:hover
# 4. Base .card img
# 5. Base .card:hover img
# 6. Base .card-content
# 7. Base .card-content h3
# 8. Base .card-content h3::before
# 9. Base .card-content p
# 10. Base .card-content .read-more
# 11. Base .card-content .read-more:hover
# ... then later, in merged styles ...
# 12. Merged .card-grid (around line 600 in the provided file)
# 13. Merged .card (THIS IS THE TARGET for renaming main class)
# 14. Merged .card:hover (THIS IS THE TARGET for renaming hover)
# 15. Merged .card img
# 16. Merged .card:hover img
# 17. Merged .card-content
# ... and so on.

# We want to rename the .card definition that appears *after* the .section-title::after animation.
# A unique comment or line number would be more robust if available.
# Let's use the comment "/* --- Tarjetas (Cards) --- */" which appears twice. We target the second one.

awk '
BEGIN { state = 0; }

# Match the unique anchor comment for the second card block section
/\/\* epic_theme\.css has \.card-grid and \.card\. estilos\.css also has these\. Appending for now\. \*\// {
    state = 1;
    print $0;
    next;
}

# If in state 1, the next .card { is the one we want to target
state == 1 && /^[[:space:]]*\.card \{/ {
    sub(/\.card \{/, ".card--legacy-hover-effect {");
    print $0;
    state = 2;
    next;
}

# If in state 2, look for its hover
state == 2 && /^[[:space:]]*\.card:hover \{/ {
    sub(/\.card:hover \{/, ".card--legacy-hover-effect:hover {");
    print $0;
    state = 3;
    next;
}

# Print all other lines
{ print $0; }

' "$css_file" > "${css_file}.tmp"

if [ $? -eq 0 ]; then
    mv "${css_file}.tmp" "$css_file"
    echo "Refactoring of .card to .card--legacy-hover-effect potentially complete."

    echo "Verifying changes:"
    if grep -q -F ".card--legacy-hover-effect {" "$css_file"; then
        echo "  .card--legacy-hover-effect class found."
    else
        echo "  ERROR: .card--legacy-hover-effect class NOT found. Rolling back."
        cp "$backup_file" "$css_file"
        exit 1
    fi
    if grep -q -F ".card--legacy-hover-effect:hover {" "$css_file"; then
        echo "  .card--legacy-hover-effect hover found."
    else
        echo "  ERROR: .card--legacy-hover-effect hover NOT found. Rolling back."
        cp "$backup_file" "$css_file"
        exit 1
    fi

    # Count occurrences of ".card {" to ensure the first one is still there
    # Note: This simple grep might catch other selectors that *contain* ".card {"
    # A more precise regex would be "^\.card \{"
    base_card_count=$(grep -E -c "^\.card \{" "$css_file")
    if [ "$base_card_count" -eq 1 ]; then
        echo "  Base .card definition seems intact (found $base_card_count instance of '^\.card \{')."
    else
        echo "  WARNING: Base .card definition count is $base_card_count (expected 1 for '^\.card \{'). Manual check needed. Rolling back."
        cp "$backup_file" "$css_file"
        exit 1
    fi
else
    echo "AWK script failed. No changes made."
    rm -f "${css_file}.tmp"
    exit 1
fi

echo "Refactoring subtask for .card finished."
