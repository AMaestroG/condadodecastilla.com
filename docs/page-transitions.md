# Page Transitions

This guide explains how to configure the optional page-transition module used by the website.

## Installing dependencies

The transitions rely on GSAP and custom CSS. Install JS dependencies with:

```bash
npm install
```

## Building assets

To bundle the transition scripts and styles run:

```bash
npm run build
```

The output CSS is written to `assets/css/custom.css` and the JS bundle in `dist/`.

## Usage

Include the generated files in your template and call `initPageTransitions()` from `js/page-transitions.js`.

The module fades the old page out and slides in the new content using Cerezo purple and old gold colors. The effect reinforces the mission by offering a smooth and appealing navigation experience that invites visitors to explore the heritage of **Cerezo de Río Tirón**.

## Haptic feedback

Menu interactions trigger a short vibration when supported. This subtle cue helps users notice when sidebars or panels open and close. The vibration also fires on general button clicks, providing consistent physical feedback across the site.
