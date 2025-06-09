#!/bin/bash

echo "Starting .cta-button refactoring in epic_theme.css..."

css_file="assets/css/epic_theme.css"
backup_file="assets/css/epic_theme.css.bak_ctabutton_refactor"

if [ ! -f "$css_file" ]; then
    echo "ERROR: $css_file not found."
    exit 1
fi

cp "$css_file" "$backup_file"
echo "Backed up $css_file to $backup_file."

# This is tricky. We need to find the *second* main definition of .cta-button
# and its associated :hover/:focus-visible rules.
# The file has:
# 1. Base .cta-button (around line 260 in the provided file)
# 2. Base .cta-button:hover, .cta-button:focus-visible
# 3. Merged .cta-button (around line 680) - THIS IS THE TARGET
# 4. Merged .cta-button:hover, .cta-button:focus-visible
# 5. .cta-button-small
# 6. .cta-button-small:hover, .cta-button-small:focus-visible

# Let's try to use awk. We need a state machine.
# State 0: Looking for the comment "/* .cta-button from estilos.css"
# State 1: Found comment, now looking for the next ".cta-button {"
# State 2: Found target .cta-button, replace it and its hover. Then go to state 3 (done with this block).

awk '
BEGIN { state = 0; }

# Match the start comment for the second CTA button block
/^\/\* \.cta-button from estilos\.css/ {
    print "Found comment for second CTA button block." > "/dev/stderr";
    state = 1;
    print $0;
    next;
}

# If in state 1, look for the .cta-button selector
state == 1 && /^\.cta-button \{/ {
    print "Found second .cta-button. Renaming to .cta-button--large-legacy." > "/dev/stderr";
    sub(/\.cta-button \{/, ".cta-button--large-legacy {");
    print $0;
    state = 2; # Now look for its hover/focus rule
    next;
}

# If in state 2, look for its hover/focus selector
state == 2 && /\.cta-button:hover, \.cta-button:focus-visible \{/ {
    print "Found hover/focus for second .cta-button. Renaming." > "/dev/stderr";
    sub(/\.cta-button:hover, \.cta-button:focus-visible \{/, ".cta-button--large-legacy:hover, .cta-button--large-legacy:focus-visible {");
    print $0;
    state = 3; # Done with this specific block
    next;
}

# Print all other lines
{ print $0; }

' "$css_file" > "${css_file}.tmp"

if [ $? -eq 0 ]; then
    mv "${css_file}.tmp" "$css_file"
    echo "Refactoring of .cta-button to .cta-button--large-legacy potentially complete."
    echo "Please verify the changes, especially that only the second instance was affected."

    echo "Verifying changes:"
    if grep -q -F '.cta-button--large-legacy {' "$css_file"; then
        echo "  .cta-button--large-legacy class found."
    else
        echo "  ERROR: .cta-button--large-legacy class NOT found. Rolling back."
        cp "$backup_file" "$css_file"
        exit 1
    fi
    if grep -q -F '.cta-button--large-legacy:hover, .cta-button--large-legacy:focus-visible {' "$css_file"; then
        echo "  .cta-button--large-legacy hover/focus-visible found."
    else
        echo "  ERROR: .cta-button--large-legacy hover/focus-visible NOT found. Rolling back."
        cp "$backup_file" "$css_file"
        exit 1
    fi

    # Count occurrences of ".cta-button {" to ensure the first one is still there
    base_cta_count=$(grep -E -c "^\.cta-button\s*\{" "$css_file")
    if [ "$base_cta_count" -eq 1 ]; then
        echo "  Base .cta-button definition seems intact (found $base_cta_count instance)."
    else
        echo "  WARNING: Base .cta-button definition count is $base_cta_count (expected 1). Manual check needed. Rolling back."
        cp "$backup_file" "$css_file"
        exit 1
    fi

else
    echo "AWK script failed. No changes made."
    rm -f "${css_file}.tmp"
    exit 1
fi

# Refactoring .cta-button-small to ensure it doesn't conflict and potentially uses base properties
# This is more complex as it requires understanding what to keep vs what to inherit.
# For now, the script will not attempt to modify .cta-button-small beyond ensuring its name is unique.
# A manual review would be better for .cta-button-small.
echo "Skipping .cta-button-small refactor in this automated step. Manual review recommended."


echo "Refactoring subtask finished."
