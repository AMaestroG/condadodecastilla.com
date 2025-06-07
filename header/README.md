# Header Component Structure

This folder contains the markup for the main site header, separated into logical
subcomponents. Each menu has its own HTML fragment and accompanying CSS under
`assets/css/header/`.

- `topbar/` – language selection bar and theme toggle button
- `sidebar/` – primary navigation sidebar and toggle button
- `ia/` – AI chat toggle and sidebar interface

`index.html` mirrors the public `_header.html` file.

Additional styles:
- `overlay.css` – backdrop used when menus are active
- `responsive.css` – mobile layout adjustments
