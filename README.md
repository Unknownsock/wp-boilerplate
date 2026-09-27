# WP Boilerplate

[![CI](https://github.com/unknownsock/wp-boilerplate/actions/workflows/ci.yml/badge.svg)](https://github.com/unknownsock/wp-boilerplate/actions/workflows/ci.yml)
[![License: ISC](https://img.shields.io/badge/license-ISC-blue.svg)](LICENSE)

A starting point for WordPress theme projects: an ACF Pro flexible-content theme
(`themes/boilerplate`) with a per-block Vite build (SCSS + JS, code-split per block), a Dockerised
local dev environment, and a lint/test/CI pipeline that's actually wired up and running, not just
configured.

This repo is meant to be **used as a template**, not developed on directly — see
[Starting a new project from this boilerplate](#starting-a-new-project-from-this-boilerplate)
below. Architecture and conventions are documented in [CLAUDE.md](CLAUDE.md); read that first if
you're adding a new block or touching the build.

## Contents

- [Requirements](#requirements)
- [Local development](#local-development)
- [What's included](#whats-included)
- [Project structure](#project-structure)
- [Versioning](#versioning)
- [Starting a new project from this boilerplate](#starting-a-new-project-from-this-boilerplate)
- [License](#license)

## Requirements

- Docker Desktop
- Node.js (see `.browserslistrc`/`package.json` for supported ranges) + npm
- PHP 8.3+ and Composer, if you want to run the theme's own lint/test/analyze scripts outside
  Docker (`themes/boilerplate/composer.json`)

## Local development

```
cp .env.example .env      # then edit values as needed
npm install
npm run start               # docker compose up -d — WordPress on localhost:8000, phpMyAdmin on :8080
npm run dev                 # Vite dev server with HMR
```

`.env` controls the ports and DB credentials (`WP_PORT`, `PHPMYADMIN_PORT`, `DB_NAME`, `DB_USER`,
`DB_PASS`, `DB_ROOT_PASSWORD` — see `.env.example`) via `docker-compose.yml`'s `${VAR:-default}`
substitution; running two boilerplate-based projects locally at once is just a matter of giving
each its own ports. `DB_ROOT_PASSWORD`/`DB_NAME`/etc. only take effect on a **fresh** `db` volume —
MySQL/MariaDB's official image only reads them the first time it initialises an empty data
directory, so changing them after the stack has already run once won't retroactively change an
existing database (`npm run clean` first if you need a truly fresh start).

Other useful commands:

```
npm run stop                 # docker compose down
npm run shell                 # shell into the wordpress container
npm run wp -- <cmd>           # wp-cli, e.g. npm run wp -- plugin list
npm run wp:seed               # create starter pages, home content, and the main menu (idempotent)
npm run wp:seed:blocks        # overwrite Home with one example of every block, for visual QA
npm run build                 # production build → themes/boilerplate/assets/
```

`npm run wp:seed` is safe to run more than once. It creates Home, About, and Contact pages,
adds placeholder hero and WYSIWYG content to a new Home page, creates a Main Menu, and assigns it
to the primary menu location. Existing pages, menu items, front-page settings, and menu assignments
are left unchanged. `npm run wp:seed:blocks` is the opposite — it always overwrites Home's content
with one example of every flexible-content layout, so you can eyeball the whole block library at
once; don't run it against content you want to keep.

See [CLAUDE.md](CLAUDE.md) for the full command reference (linting, PHP tests, Cypress, ACF
field-group sync, etc).

## What's included

- **Flexible-content block system** — drop a new folder under
  `themes/boilerplate/template-parts/content/blocks/<name>/`, and it's automatically routed, built,
  and enqueued. No dispatcher/build-config edits needed for a new block.
- **BEM + shared panel-colour palette** — a consistent naming and colour-token convention
  (White / Light Grey / Orange / Blue) across cards, content-grid, and section backgrounds (see
  CLAUDE.md).
- **CSS custom properties for brand palette** — `src/sass/abstracts/_variables.scss` holds
  placeholder brand colours; swap them for the client's real brand hex codes and every block
  picks it up.
- **ACF Pro field group JSON** already version-controlled (`themes/boilerplate/acf-json/`) — sync
  into the DB via wp-admin or the `wp eval` snippet in CLAUDE.md.
- **A handful of example content types** (Portfolio, Team, Testimonials, News) to show the
  pattern — delete or rename what a given project doesn't need.
- **Three swappable header styles** (standard mega-menu, centered logo, minimal off-canvas) —
  pick one via Options → Header, or delete the two you don't need.
- **GSAP-based scroll/entrance animation system** — `data-scroll-animation="fadeIn|slideUp|..."`
  on any element, no per-block JS needed.
- **Docker Compose** local WordPress + MySQL + phpMyAdmin + wp-cli stack.
- **A working lint/test pipeline, not just config files** — ESLint, Stylelint (SCSS + BEM
  plugins), Prettier, PHPCS/PHPCBF (WPCS), PHPStan, and PHPUnit are all installed and runnable
  today. Pre-commit (Husky + lint-staged) is deliberately formatting-only — it never blocks a
  commit — while a [GitHub Actions CI workflow](.github/workflows/ci.yml) runs the full lint/
  analyze/test/build suite on every push and PR.
- **GitHub Actions deploy workflows** (`.github/workflows/`, driven by
  `.github/config/deploy.json` — fill in real FTP/SFTP host and path per project; deploys are
  disabled by default).

## Project structure

```
themes/boilerplate/          the theme - PHP, ACF field groups, tests
  acf-json/                  version-controlled ACF field group definitions
  includes/                  PSR-4 autoloaded PHP (Boilerplate\ namespace)
  template-parts/
    content/blocks/<name>/   one folder per flexible-content block (PHP + SCSS + JS)
    header/, footer/         swappable header styles, shared footer
  tests/                     PHPUnit
src/
  js/                        app.js + shared modules (nav, forms, animation, ...)
  sass/                      abstracts, base, layout, components, themes
bin/
  wp/                        one-off seed/content scripts (wp eval-file)
  db/                        DB backup/restore (Node)
config/                      bind-mounted wp-config.php, .htaccess, uploads.ini
.github/
  workflows/                 CI + deploy workflows
  config/deploy.json         per-project FTP/SFTP target
```

## Versioning

This repo follows [Semantic Versioning](https://semver.org/) (`package.json`'s `version` field is
the source of truth), with changes tracked in [CHANGELOG.md](CHANGELOG.md).

Because this is a **template**, not a published package, "versioning" here means something
slightly different than for a library:

- A version bump marks a meaningful snapshot of the boilerplate itself — a new block, a build/CI
  fix, a convention change — not a release of any one client site built from it.
- **When you start a new project from this template** (see below), note the version you started
  from — e.g. in your new project's own README, or as a comment near the top of its `CHANGELOG.md`
  (`Forked from wp-boilerplate v1.2.0`). That's the only record of which version a given client
  project is based on; nothing here tracks it for you automatically.
- **Bumping the version** (when you're working on the boilerplate itself, not a client project) is
  manual: update `package.json`'s `version`, add an entry to `CHANGELOG.md` under a new heading
  (move anything queued under `[Unreleased]` there), commit, then tag the release on GitHub
  (`git tag v1.2.0 && git push --tags`, or use the GitHub UI's Releases page).
- Bump **patch** (`1.0.x`) for fixes that don't change how a project uses the template, **minor**
  (`1.x.0`) for new blocks/features/conventions that are additive, and **major** (`x.0.0`) for
  anything that would require changes in a project already built from an earlier version (a
  renamed convention, a removed block, a changed field-group shape).

## Starting a new project from this boilerplate

1. Click **"Use this template"** on the GitHub repo (or `git clone` + re-`git init` if you'd
   rather not link history at all) to create a new repo for the client project.
2. Rename things that are still boilerplate-generic:
   - `themes/boilerplate/style.css` — `Theme Name`
   - `themes/boilerplate/composer.json` — `name`, PSR-4 namespace if you want one specific to the
     project
   - `package.json` — `name`, `repository`/`bugs`/`homepage`
   - `src/sass/abstracts/_variables.scss` — swap placeholder brand colours for the real ones
   - `.github/config/deploy.json` — real FTP/SFTP host + remote path, then flip `enabled: true`
     once the deploy target exists
3. `cp .env.example .env`, fill in real local credentials, `npm run start`.
4. Delete the example content types/blocks you don't need for this project; add new ones
   following the per-block folder convention in CLAUDE.md.
5. Note which boilerplate version you started from (see [Versioning](#versioning) above).
6. Anything you build that's generically useful (a new block, a build fix, a CI improvement) —
   consider porting it back into this boilerplate repo for next time.

## License

[ISC](LICENSE)
