# WP Boilerplate

A starting point for WordPress theme projects: an ACF Pro flexible-content theme
(`themes/freckle`) with a per-block Vite build (SCSS + JS, code-split per block), a Dockerised
local dev environment, and CI-ready linting/testing config already wired up.

This repo is meant to be **used as a template**, not developed on directly — see
[Starting a new project from this boilerplate](#starting-a-new-project-from-this-boilerplate)
below. Architecture and conventions are documented in [CLAUDE.md](CLAUDE.md); read that first if
you're adding a new block or touching the build.

## Requirements

- Docker Desktop
- Node.js (see `.browserslistrc`/`package.json` for supported ranges) + npm
- PHP 8.3+ and Composer, if you want to run the theme's own lint/test/analyze scripts outside
  Docker (`themes/freckle/composer.json`)

## Local development

```
cp .env.example .env      # then edit values as needed
npm install
npm run start               # docker compose up -d — WordPress on localhost:8000, phpMyAdmin on :8080
npm run dev                 # Vite dev server with HMR
```

Other useful commands:

```
npm run stop                 # docker compose down
npm run shell                 # shell into the wordpress container
npm run wp -- <cmd>           # wp-cli, e.g. npm run wp -- plugin list
npm run build                 # production build → themes/freckle/assets/
```

See [CLAUDE.md](CLAUDE.md) for the full command reference (linting, PHP tests, Cypress, ACF
field-group sync, etc).

## What's included

- **Flexible-content block system** — drop a new folder under
  `themes/freckle/template-parts/content/blocks/<name>/`, and it's automatically routed, built,
  and enqueued. No dispatcher/build-config edits needed for a new block.
- **BEM + shared panel-colour palette** — a consistent naming and colour-token convention across
  blocks (see CLAUDE.md).
- **CSS custom properties for brand palette** — `src/sass/abstracts/_variables.scss` holds
  placeholder brand colours; swap them for the client's real brand hex codes and every block
  picks it up.
- **ACF Pro field group JSON** already version-controlled (`themes/freckle/acf-json/`) — sync into
  the DB via wp-admin or the `wp eval` snippet in CLAUDE.md.
- **A handful of example content types** (team, testimonials, clients, carousels) to show the
  pattern — delete or rename what a given project doesn't need.
- **Docker Compose** local WordPress + MySQL + phpMyAdmin + wp-cli stack.
- **CI-ready tooling**: PHPCS/PHPCBF (WPCS), PHPStan, PHPUnit, ESLint, Stylelint, Prettier,
  Husky + lint-staged pre-commit hooks, Cypress E2E scaffold, and GitHub Actions deploy workflows
  (`.github/workflows/`, driven by `.github/config/deploy.json` — fill in real FTP/SFTP host and
  path per project; deploys are disabled by default).

## Starting a new project from this boilerplate

1. Click **"Use this template"** on the GitHub repo (or `git clone` + re-`git init` if you'd
   rather not link history at all) to create a new repo for the client project.
2. Rename things that are still boilerplate-generic:
   - `themes/freckle/style.css` — `Theme Name`
   - `themes/freckle/composer.json` — `name`, PSR-4 namespace if you want one specific to the
     project
   - `package.json` — `name`, `repository`/`bugs`/`homepage`
   - `src/sass/abstracts/_variables.scss` — swap placeholder brand colours for the real ones
   - `.github/config/deploy.json` — real FTP/SFTP host + remote path, then flip `enabled: true`
     once the deploy target exists
3. `cp .env.example .env`, fill in real local credentials, `npm run start`.
4. Delete the example content types/blocks you don't need for this project; add new ones
   following the per-block folder convention in CLAUDE.md.
5. Anything you build that's generically useful (a new block, a build fix, a CI improvement) —
   consider porting it back into this boilerplate repo for next time.

## License

ISC
