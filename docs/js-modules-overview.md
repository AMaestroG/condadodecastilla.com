# JavaScript Modules Overview

This document summarizes the purpose of the main JavaScript files present in the repository after consolidation.

| File | Description |
|------|-------------|
| `assets/js/main.js` | Handles sliding menu interactions, closing behavior, and the light/dark theme toggle used across all pages. |
| `assets/js/foro.js` | Simple toggling for the forum agents menu. |
| `js/config.js` | Defines `API_BASE_URL` and `DEBUG_MODE` globals for other scripts. |
| `js/layout.js` | Loads external CSS/JS libraries on demand, initializes the flashlight effect and other page-level utilities. |
| `js/load_menu_parts.js` | Dynamically loads menu fragments into the header when needed. |
| `js/header-loader.js` | **Deprecated**. Static pages now include `/_header.php` directly without this helper. |
| `js/ia-tools.js` | Implements AI assistant utilities such as summaries, translations and chat. |
| `js/lang-bar.js` | Controls the language selection bar and triggers Google Translate. |
| `js/lugares-data.js` | Provides static data used by `lugares-dynamic-list.js`. |
| `js/lugares-dynamic-list.js` | Generates the list of places dynamically from `lugares-data.js`. |
| `js/museo-2d-gallery.js` | Logic for the collaborative museum 2D gallery including uploads and modals. |
| `js/museo-3d-main.js` | Initializes the 3D museum viewer built on Three.js. |
| `js/museum-3d/` | Additional modules used by the 3D viewer. |

Deprecated or merged scripts such as `js/menu-controller.js` and `js/sliding-menu.js` have been removed in favour of `assets/js/main.js`.
