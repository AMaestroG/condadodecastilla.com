#!/bin/bash

echo "Starting CSS and JS fixes subtask..."

# --- 1. Modify assets/css/epic_theme.css ---
css_epic_theme_file="assets/css/epic_theme.css"
backup_epic_theme_file="assets/css/epic_theme.css.bak_fixes"

echo "Processing $css_epic_theme_file..."
if [ -f "$css_epic_theme_file" ]; then
    cp "$css_epic_theme_file" "$backup_epic_theme_file"

    # Map --color-acento-rojo
    # Initial check if the variable is used
    if grep -q -- "--color-acento-rojo" "$css_epic_theme_file"; then
        # Replace with --epic-gold-main for now as a suitable theme color
        sed -i 's/var(--color-acento-rojo)/var(--epic-gold-main)/g' "$css_epic_theme_file"
        echo "  Mapped --color-acento-rojo to var(--epic-gold-main)."
    else
        echo "  --color-acento-rojo not found, skipping mapping."
    fi

    # Add .ia-tool-error class and --epic-error-red variable
    # Check if :root exists to add the variable
    if grep -q ":root {" "$css_epic_theme_file"; then
        # Add --epic-error-red if not already present
        if ! grep -q -- "--epic-error-red:" "$css_epic_theme_file"; then
            sed -i '/:root {/a \    --epic-error-red: #D9534F; /* Default error red */' "$css_epic_theme_file"
            echo "  Added --epic-error-red variable to :root."
        fi
    else
        # If :root doesn't exist (unlikely for this file), add it with the variable
        echo -e ":root {\n    --epic-error-red: #D9534F; /* Default error red */\n}\n" | cat - "$css_epic_theme_file" > temp && mv temp "$css_epic_theme_file"
        echo "  Created :root and added --epic-error-red variable."
    fi

    # Add .ia-tool-error class definition if not already present
    if ! grep -q ".ia-tool-error {" "$css_epic_theme_file"; then
        echo -e "
.ia-tool-error {
  color: var(--epic-error-red, #D9534F);
}" >> "$css_epic_theme_file"
        echo "  Added .ia-tool-error class definition."
    fi
else
    echo "  ERROR: $css_epic_theme_file not found."
fi

# --- 2. Modify assets/css/header/nav.css ---
css_nav_file="assets/css/header/nav.css"
backup_nav_file="assets/css/header/nav.css.bak_fixes"

echo "Processing $css_nav_file..."
if [ -f "$css_nav_file" ]; then
    cp "$css_nav_file" "$backup_nav_file"
    # Uncomment display: none !important; for .navbar .nav-links in @media (max-width: 768px)
    # This is tricky with sed due to multiline context.
    # Assuming it's the only display: none !important; commented out for .navbar .nav-links
    if grep -q '/\* display: none !important; \*/' "$css_nav_file"; then
        sed -i '/\.navbar \.nav-links {/,/}/s#/\* display: none !important; \*/#display: none !important;#' "$css_nav_file"
        # Verify change
        if grep -A 5 '\.navbar \.nav-links' "$css_nav_file" | grep -q 'display: none !important;'; then
            echo "  Uncommented 'display: none !important;' for .navbar .nav-links in mobile media query."
        else
            echo "  WARNING: Failed to uncomment 'display: none !important;' for .navbar .nav-links as expected. Manual check might be needed."
        fi
    else
        echo "  '.navbar .nav-links' with commented out 'display: none !important;' not found as expected."
    fi
else
    echo "  ERROR: $css_nav_file not found."
fi


# --- 3. Modify js/ia-tools.js ---
js_ia_tools_file="js/ia-tools.js"
backup_js_ia_tools_file="js/ia-tools.js.bak_fixes"

echo "Processing $js_ia_tools_file..."
if [ -f "$js_ia_tools_file" ]; then
    cp "$js_ia_tools_file" "$backup_js_ia_tools_file"

    # Remove makeDraggable function
    # Using awk to print lines outside the function definition
    awk 'BEGIN{printing=1} /function makeDraggable\(/{printing=0} /});/{if(printing==0){printing=1;next}} printing==1{print}' "$js_ia_tools_file" > tmp_ia_tools.js && mv tmp_ia_tools.js "$js_ia_tools_file"
    if ! grep -q "function makeDraggable(" "$js_ia_tools_file"; then
        echo "  Removed makeDraggable function."
    else
        echo "  WARNING: Failed to remove makeDraggable function. It might still be present."
    fi

    # Replace inline error styles with class .ia-tool-error
    # Example: <p style="color:red;">${data.error}</p> -> <p class="ia-tool-error">${data.error}</p>
    # Need to handle variations: message, data.error, "Respuesta inesperada"
    sed -i 's|<p style="color:red;">\([^<]*\)</p>|<p class="ia-tool-error">\1</p>|g' "$js_ia_tools_file"
    # Verify one of the replacements
    if grep -q '<p class="ia-tool-error">\${data.error}</p>' "$js_ia_tools_file"; then
        echo "  Replaced inline error styles with .ia-tool-error class."
    else
        echo "  WARNING: Failed to replace all inline error styles with .ia-tool-error class as expected. Manual check might be needed."
    fi
else
    echo "  ERROR: $js_ia_tools_file not found."
fi

echo "CSS and JS fixes subtask complete."
