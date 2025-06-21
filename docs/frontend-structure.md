# Frontend Structure

This directory hosts small demo projects using React and Svelte. All projects
import `frontend/styles/theme.css`, which simply re-exports the shared
variables from `assets/css/epic_theme.css` using `@import`.

For colour references see the [style guide](style-guide.md).

## Building the forum application

1. Navigate to the React project and install dependencies:

   ```bash
   cd frontend/forum-app
   npm install
   ```

2. Create the production bundle:

   ```bash
   npm run build
   ```

   The compiled assets are written to `assets/forum-app/` at the project root.

3. Deploy by copying or syncing the `assets/forum-app` directory to the
   web server. The `foro/index.php` page loads `forum.js` from this location.
