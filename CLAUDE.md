# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

A boilerplate WordPress project, meant to be used as a template for new client sites. Theme:
`themes/boilerplate` — an ACF Pro flexible-content theme, general-purpose across projects. Frontend
build is Vite (SCSS + JS, per-block code-splitting). Local dev runs entirely in Docker.

When starting a new project from this boilerplate, see the README's "Starting a new project from
this boilerplate" section for what to rename/genericize first.

## Current status (2026-09-22)

**Intent:** turn what was a specific client project (formerly "freckle"/"imp") into a genuinely
generic, reusable boilerplate — strip anything project-specific, keep the theme unstyled/neutral
enough to restyle per client, and make the common structural choices (header layout, content
types) swappable rather than hardcoded.

**Done this session:**
- Renamed the whole theme slug `freckle` → `boilerplate` (folder, text-domain, every `freckle_*`
  function, Vite/Docker/docs paths) — see git history for the full list of touched files.
- Stripped real leaked client data: a hardcoded Google Analytics ID, Pinterest tag ID, and Adobe
  Fonts kit ID in `header.php`; hardcoded former-client page IDs/labels in `hero-standard.php`
  (replaced with a generic dynamic "back to parent page" link).
- Renamed the `clients` CPT → `portfolio` (it's actually a case-study/portfolio type, not just a
  logo source) across its registration, single template, ACF field groups, and admin menu; fixed
  the `category` taxonomy (was registered against nonexistent `projects`/`team` post types);
  deleted the dead `carousels` CPT.
- Deleted dead/orphaned JS (`header.js`, `footer.js`, `instagram.js`, `accessibility.js`,
  `components.js` — empty or fully commented out) and a large amount of dead/commented-out code
  and narrative "history" comments across `src/js/`, `src/sass/`, and theme PHP (see git log for
  specifics — this was a broad pass, not itemized here).
- Added three swappable header styles (`header_style` option: standard mega-menu / centered logo /
  minimal off-canvas) — see "Header style dispatch" below. Visually verified all three at desktop
  + mobile widths, with a real nested dropdown, via a throwaway Playwright script (not committed;
  Playwright itself was installed only in the session scratchpad, not added to `package.json`).
- Extended `bin/wp/seed-content.php`: seeds two more example content blocks (stats, cards) on
  Home, a Footer Menu, and two extra pages (Services, Our Approach) nested under About so there's
  always at least one real dropdown to look at.
- Recovered from an unrelated incident mid-session: a research subagent deleted 3 files
  (`sidebar.php`, `single-clients.php`, `heading-fullstop.js`) despite being told to only research —
  caught via `git status` and restored from git before anything was lost.

**Still open / not yet done:**
- Nothing has been committed yet as of writing this — everything above is working-tree changes.
- ACF JSON → DB sync (the `wp eval` snippet further down) needs re-running after every ACF JSON
  edit, including the `header_style` field and the `clients`→`portfolio` relationship-field
  changes — done once already this session, but redo it if you pull these changes into a fresh DB.
- `npm install` had never been run before this session (devDependencies, incl. stylelint/eslint,
  weren't installed) — now installed, but `npm run lint:scss`/`eslint:js` haven't actually been
  run/fixed yet, only `php -l` / `sass --compile` / `node --check` were used to verify edits.
- The standard header's mega-menu CTA-card feature (`mega_cta_*` fields) is untouched/unverified —
  no seeded menu item has one configured, so it's never been visually exercised.
- The centered/minimal off-canvas dropdown styling is intentionally simpler than the standard
  header's mega-menu (flat list, no columns/CTA) — `Simple_Menu_Walker` doesn't build that markup.
- General "is this generic enough" review was broad but not exhaustive — more leftover
  project-specific assumptions may still turn up in less-visited template parts.

## Commands

**Local WordPress (Docker, `localhost:8000`; phpMyAdmin on `:8080`):**
```
npm run start          # docker compose up -d
npm run stop           # docker compose down
npm run shell          # shell into the wordpress container
npm run wp -- <cmd>    # wp-cli via the wp-cli service, e.g. npm run wp -- plugin list
npm run wp:seed        # seed starter pages/home content/main menu (idempotent, see README)
```
`wp-cli` is a long-running compose service (`sleep 9999` entrypoint) — always invoke it via
`docker compose exec wp-cli wp ...` / `npm run wp --`, never expect a one-shot container.

**Frontend build (Vite):**
```
npm install
npm run dev            # dev server, HMR — writes themes/boilerplate/hot so PHP knows to load from it
npm run build          # production build → themes/boilerplate/assets/
npm run prod           # vite build --mode production
```

**Linting / formatting:**
```
npm run lint:scss      # stylelint "**/*.scss"
npm run lint:fix       # stylelint --fix
npm run eslint:js      # eslint "src/**/*.js"
npm run eslint:fix
npm run format:scss    # prettier --write "**/*.scss"
npm run format:php-all # prettier --write "**/*.php"
npm run lint:php       # cd themes/boilerplate && composer lint   (phpcs, WPCS standard)
npm run fix:php        # phpcbf
```
Pre-commit (`husky` + `lint-staged`) runs prettier/eslint/stylelint on staged JS/SCSS and
phpcbf+prettier on staged theme PHP automatically.

**PHP tests / static analysis** (run from `themes/boilerplate/`, needs `composer install` there first):
```
composer test          # phpunit (config: phpunit.xml, bootstrap: tests/bootstrap.php)
composer analyze        # phpstan analyse --level=5 includes/
composer qa             # lint + analyze + test
```
Only `tests/FunctionsTest.php` exists currently — PHPUnit tests live in `themes/boilerplate/tests/`.

**E2E (Cypress)**, baseUrl `http://localhost:8000` (needs the Docker stack running):
```
npx cypress open        # interactive
npx cypress run         # headless, all specs in cypress/e2e/**/*.cy.js
```

**ACF field group sync** — after hand-editing any `themes/boilerplate/acf-json/*.json`, sync it into
the DB (ACF has no bundled wp-cli command for this, so it goes through `wp eval`):
```
docker compose exec wp-cli wp eval '
$files = acf_get_local_json_files();
foreach ( $files as $path ) {
    $fg = json_decode( file_get_contents( $path ), true );
    if ( $fg ) acf_import_field_group( $fg );
}
' --path=/var/www/html
```
Or via wp-admin: Custom Fields → Field Groups → "Sync available".

## Architecture

### Flexible-content block dispatch

Pages render a `content_blocks` ACF flexible-content field. [content-blocks.php](themes/boilerplate/template-parts/content/content-blocks.php)
loops each row and turns its `acf_fc_layout` name into a template path:
`template-parts/content/blocks/<name>/block-<name>.php` if that folder exists, else falling back
to a flat `template-parts/content/blocks/block-<name>.php`. **Moving a block into its own folder
with a matching filename is the entire routing change — no dispatcher edit needed.**

### Header style dispatch

[site-header.php](themes/boilerplate/template-parts/header/site-header.php) is a dispatcher, not a
header itself — it reads the sitewide `header_style` option (Options → Header: `standard` /
`centered` / `minimal`, ACF field on group_5e999896a827d) and loads
`site-header-<style>.php` from the same folder. `standard` uses the full mega-menu
`Custom_Menu_Walker` (dropdown columns + CTA card, driven by each menu item's `mega_*` ACF
fields); `centered` and `minimal` both use the flatter `Simple_Menu_Walker` (plain nested
`<ul>`s, no columns) — `includes/menu_walkers/`. `centered` also needs a menu split in half
around the logo, done by `boilerplate_split_menu_items()` (includes/functions.php), which keeps
each dropdown's children on the same side as their top-level parent rather than just slicing the
flat item list in two. The centered/minimal nav CSS (`simple-nav-list` mixin, `_navigation.scss`)
overrides several of that mixin's own `min-width $grid-nav` rules (flex direction, dropdown
position) at the same class-selector specificity — Vite's production CSS minifier reorders/merges
same-breakpoint `@media` blocks, so a same-specificity override can silently lose despite looking
correct in an un-minified dev build. Those overrides use the project's `:not(#\#)` specificity
hack (see below) rather than relying on source order — do the same for any new override in this
area, and don't trust a `sass --compile` smoke test alone; check the actual `npm run build` output
in a browser.

### Per-block folder convention

Each `template-parts/content/blocks/<name>/` folder colocates `block-<name>.php`, `_<name>.scss`,
and `<name>.js`. The **JS file imports its own SCSS** (`import './<name>.scss'`) — that's what
pulls the block's styles into the build; nothing auto-discovers SCSS otherwise. A block with no
JS behaviour still needs a minimal `<name>.js` that just does that import, so Vite has something
to key an entry on.

- [vite.config.mjs](vite.config.mjs)'s `findBlockEntries()` scans that blocks directory and turns
  every `<name>/<name>.js` into its own Vite build entry, keyed by folder name.
- [includes/asset-management.php](themes/boilerplate/includes/asset-management.php)'s
  `enqueue_block_assets()` scans the current page's (and, if a shared footer is enabled, that
  footer page's) `content_blocks` rows and only enqueues each used block's JS+CSS entry, as a
  module script (`type="module"` is force-added via `script_loader_tag` for any handle ending
  `-js`). `latest_blogs` is enqueued explicitly too since it's also rendered directly by
  index/single templates outside `content_blocks` (for "related articles").
- SCSS partials colocated in a block folder use `@use 'abstracts/mixins' as *;` (no relative
  `../../` traversal) — this works because `vite.config.mjs` adds `src/sass` to the Sass
  `loadPaths`. Components under `src/sass/**` instead use relative paths
  (`@use '../abstracts/mixins' as *;`) since they aren't reached through that load path.

### BEM naming

`$block = 'b-' . str_replace('_', '-', $content['acf_fc_layout'])` gives `.b-accordion`,
`.b-feature-grid`, etc. Children: `$block . '__element'`. Modifiers: `$block . '__element--variant'`.
No grid wrapper (`.row > .col-*`) on modern blocks — a block renders `<section class="b-x">`
directly with BEM children. The old grid system ([_cols.scss](src/sass/layout/_cols.scss)) only
still backs pages/parts that predate this convention (`single-portfolio.php`, `side-bar.php`,
`component-icon.php`).

### Shared "panel colour" palette

`$panel-colours` (a Sass map in [abstracts/_mixins.scss](src/sass/abstracts/_mixins.scss)) is the
single source of truth for the White/Blue/Orange/Navy colour choice offered on cards, content-grid
columns, and section backgrounds — `panel-colour($name, bg|fg|accent)` looks up a token,
`panel-colour-vars($name, ...)` sets a component's custom properties from it. Each consumer still
owns *how* it applies the colour (custom properties for a card's coordinated bg/text/accent vs a
flat `background-color`/`color` for a section) — only the name→token mapping is shared.

### Section background colour vs. block-local overrides

Section-level background colours (`section.white`/`.blue`/`.orange`/`.navy`/`.grey-light`/
`.transparent`/`.gradient`) and their text-contrast flip (dark bg → white text) live unlayered in
[themes/_default.scss](src/sass/themes/_default.scss), deliberately outside `@layer theme` — a
layer can never win against unlayered content, so background-colour assignment has to stay
unlayered to reliably beat a block's own hardcoded default background. The `:not(#\#)` hack
appears throughout this file and elsewhere (`_modal.scss`, `_forms.scss`) purely to add
CSS-specificity weight without changing what a selector matches — used whenever a rule needs to
outrank another selector of otherwise-equal class count (a plain class-count tie is broken by
element count, which silently favours the *wrong* rule more than once in this codebase — check for
this class of bug first when a colour/text override "isn't applying").

### `background_colour` field

A sitewide ACF clone field (`field_617ea04499fac`, defined once in
[group-618080750e91b.json](themes/boilerplate/acf-json/group-618080750e91b.json)), cloned into
individual layouts rather than redefined per layout.

### CSS custom properties, not Sass variables, for the brand palette

`--primary-color-1` through `-6`, `--secondary-color-1/2`, etc. are defined as **CSS** custom
properties on `:root` in [abstracts/_variables.scss](src/sass/abstracts/_variables.scss) (not Sass
`$variables`) — deliberately, so they resolve at runtime and could in principle be themed/overridden
per-scope. Values are currently eyeballed from a rebrand screenshot, not final brand hex — expect
these to change.

### WordPress plumbing specifics

- `themes/boilerplate/includes/` autoloads under the `Boilerplate\` PSR-4 namespace (composer.json) —
  rename this per project if you want a project-specific namespace; it's a one-line composer.json
  edit, nothing else references it by name.
- `wp-config.php`/`.htaccess`/`uploads.ini` are bind-mounted from `config/` (docker-compose.yml),
  not baked into the theme or the WordPress image.
- `bin/wp/*.php` are one-off content/seed scripts run via `wp eval-file` against the repo root
  (mounted read-only into the `wp-cli` container at `/workspace`).
- `bin/db/{backup,import,restore}.js` (Node) handle DB dump/restore — `npm run db:backup` etc.
