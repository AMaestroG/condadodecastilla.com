# Frontend Structure

This directory hosts small demo projects using React and Svelte. All projects
import `frontend/styles/theme.css`, which exposes the purple and gold gradient
utilities and re‑exports the shared variables from `assets/css/epic_theme.css`.
Reusable UI pieces now live under `frontend/components` so the examples do not
duplicate markup.

For colour references see the [style guide](style-guide.md).

## Building the forum application

1. Install the dependencies inside `frontend/forum-app`:

   ```bash
   cd frontend/forum-app && npm install
   ```

2. From the repository root, build all frontend assets (Tailwind and the React
   forum) with:

   ```bash
   npm run build:frontend
   ```

   The compiled forum assets are written to `assets/forum-app/`.

3. Deploy by copying or syncing the `assets/forum-app` directory to the
   web server. The `foro/index.php` page loads `forum.js` from this location.
