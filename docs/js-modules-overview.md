# JavaScript Modules Overview

This document summarizes the purpose of the main JavaScript files present in the repository after consolidation.

| File                             | Description                                                                                                                                                                                                     |
| -------------------------------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `assets/js/sliding-menu.js`      | Handles sliding menu open/close logic and updates body classes `menu-open-left` and `menu-open-right`. The previous mobile trigger cloning has been removed.
| `assets/js/main.js`              | Manages theme toggles and other global helpers used across all pages.
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

Deprecated scripts such as `js/menu-controller.js` remain removed. The new `assets/js/sliding-menu.js` module now contains the sliding menu logic previously in `assets/js/main.js`.
The old header loading helper was also dropped as noted in the project README. During cleanup we removed `assets/js/escudo-reveal.js`, `assets/js/escudo-drag.js` and `assets/js/cave_mask.js` along with `assets/css/cave_mask.css`. If a reveal effect is required again, consider using modern CSS masks or the existing `torch_cursor.js` overlay.
## Sound Assets

Small audio clips provide feedback when the menu opens or closes. To keep the repository lightweight, the MP3 files are not included.

Download them from the links below and place them under `assets/sounds/`:

- `menu-open.mp3` – <https://example.com/audio/menu-open.mp3>
- `menu-close.mp3` – <https://example.com/audio/menu-close.mp3>


## Simplified Translation

Pages are translated by passing a `lang` query parameter in the URL. When this parameter is present, `js/lang-bar.js` dynamically loads the Google Translate widget and applies the selected language without showing flag icons or a language bar.
