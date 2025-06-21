# Page Transitions

This module applies simple GSAP animations when navigating between internal pages.

## Usage

1. The script `page-transitions.js` is loaded automatically via `includes/head_common.php`.
2. Any click on an internal `<a>` element triggers a random transition: **fade**, **slide** or **rotate**.
3. An overlay with id `page-transition-overlay` covers the page during the animation.

The overlay color uses `rgba(var(--epic-alabaster-bg-rgb), 0.85)` so it blends with the alabaster background defined in [style-guide.md](style-guide.md). Customize this value in `assets/css/page-transitions.css` if needed.

## Development notes

Transitions rely on GSAP 3.13.0. The script attempts to load GSAP from CDN and falls back to the bundled copy in `/assets/vendor/js/gsap.min.js` when offline.
