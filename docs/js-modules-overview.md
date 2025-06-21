# JavaScript Modules Overview

This document summarizes the purpose of the main JavaScript files present in the repository after consolidation.

| File                             | Description                                                                                                                                                                                                     |
| -------------------------------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `assets/js/main.js`              | Handles sliding menu interactions, closing behavior, and the light/dark theme toggle used across all pages.                                                                                                     |
| `assets/js/homonexus-toggle.js`  | Toggles Homonexus mode, storing the preference in a cookie.                                                                                                                                                     |
| `assets/js/foro.js`              | Simple toggling for the forum agents menu.                                                                                                                                                                      |
| `/assets/js/audio-controller.js` | Lowers audio/video volume when sliding menus open. Other scripts, such as `assets/js/main.js`, invoke its `handleMenuToggle` function directly.                                                                 |
| `js/config.js`                   | Defines `API_BASE_URL` and `DEBUG_MODE` globals for other scripts.                                                                                                                                              |
| `js/layout.js`                   | Loads external CSS/JS libraries on demand, initializes the flashlight effect and other page-level utilities. If CDN requests fail, it falls back to bundled copies of GSAP and AOS located in `assets/vendor/`. |
| ~~Header loader script~~         | **Deprecated.** The header is now loaded directly without this helper. See the README note on its removal.                                                                                                      |
| `js/ia-tools.js`                 | Implements AI assistant utilities such as summaries, translations and chat.                                                                                                                                     |
| `js/lang-bar.js`                 | Loads Google Translate when a `?lang=` URL parameter is present.                                                                                                                                                |
| `js/lugares-data.js`             | Provides static data used by `lugares-dynamic-list.js`.                                                                                                                                                         |
| `js/lugares-dynamic-list.js`     | Generates the list of places dynamically from `lugares-data.js`.                                                                                                                                                |
| `js/museo-2d-gallery.js`         | Logic for the collaborative museum 2D gallery including uploads and modals.                                                                                                                                     |
| `js/museo-3d-main.js`            | Initializes the 3D museum viewer built on Three.js.                                                                                                                                                             |
| `js/museum-3d/`                  | Additional modules used by the 3D viewer.                                                                                                                                                                       |
| `assets/js/toc-generator.js`     | Builds a table of contents from headings inside `<main>` and injects a list of links styled with Tailwind.                                                                                                      |

Deprecated or merged scripts such as `js/menu-controller.js` and `js/sliding-menu.js` have been removed in favour of `assets/js/main.js`. The old header loading helper was also dropped as noted in the project README.

## Simplified Translation

Pages are translated by passing a `lang` query parameter in the URL. When this parameter is present, `js/lang-bar.js` dynamically loads the Google Translate widget and applies the selected language without showing flag icons or a language bar.
