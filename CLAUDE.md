# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

A boilerplate WordPress project, meant to be used as a template for new client sites. Theme:
`themes/freckle` — an ACF Pro flexible-content theme, general-purpose across projects. Frontend
build is Vite (SCSS + JS, per-block code-splitting). Local dev runs entirely in Docker.

When starting a new project from this boilerplate, see the README's "Starting a new project from
this boilerplate" section for what to rename/genericize first.

## Commands

**Local WordPress (Docker, `localhost:8000`; phpMyAdmin on `:8080`):**
```
npm run start          # docker compose up -d
npm run stop           # docker compose down
npm run shell          # shell into the wordpress container
npm run wp -- <cmd>    # wp-cli via the wp-cli service, e.g. npm run wp -- plugin list
```
`wp-cli` is a long-running compose service (`sleep 9999` entrypoint) — always invoke it via
`docker compose exec wp-cli wp ...` / `npm run wp --`, never expect a one-shot container.

**Frontend build (Vite):**
```
npm install
npm run dev            # dev server, HMR — writes themes/freckle/hot so PHP knows to load from it
npm run build          # production build → themes/freckle/assets/
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
npm run lint:php       # cd themes/freckle && composer lint   (phpcs, WPCS standard)
npm run fix:php        # phpcbf
```
Pre-commit (`husky` + `lint-staged`) runs prettier/eslint/stylelint on staged JS/SCSS and
phpcbf+prettier on staged theme PHP automatically.

**PHP tests / static analysis** (run from `themes/freckle/`, needs `composer install` there first):
```
composer test          # phpunit (config: phpunit.xml, bootstrap: tests/bootstrap.php)
composer analyze        # phpstan analyse --level=5 includes/
composer qa             # lint + analyze + test
```
Only `tests/FunctionsTest.php` exists currently — PHPUnit tests live in `themes/freckle/tests/`.

**E2E (Cypress)**, baseUrl `http://localhost:8000` (needs the Docker stack running):
```
npx cypress open        # interactive
npx cypress run         # headless, all specs in cypress/e2e/**/*.cy.js
```

**ACF field group sync** — after hand-editing any `themes/freckle/acf-json/*.json`, sync it into
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

Pages render a `content_blocks` ACF flexible-content field. [content-blocks.php](themes/freckle/template-parts/content/content-blocks.php)
loops each row and turns its `acf_fc_layout` name into a template path:
`template-parts/content/blocks/<name>/block-<name>.php` if that folder exists, else falling back
to a flat `template-parts/content/blocks/block-<name>.php`. **Moving a block into its own folder
with a matching filename is the entire routing change — no dispatcher edit needed.**

### Per-block folder convention

Each `template-parts/content/blocks/<name>/` folder colocates `block-<name>.php`, `_<name>.scss`,
and `<name>.js`. The **JS file imports its own SCSS** (`import './<name>.scss'`) — that's what
pulls the block's styles into the build; nothing auto-discovers SCSS otherwise. A block with no
JS behaviour still needs a minimal `<name>.js` that just does that import, so Vite has something
to key an entry on.

- [vite.config.mjs](vite.config.mjs)'s `findBlockEntries()` scans that blocks directory and turns
  every `<name>/<name>.js` into its own Vite build entry, keyed by folder name.
- [includes/asset-management.php](themes/freckle/includes/asset-management.php)'s
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
still backs pages/parts that predate this convention (`single-clients.php`, `side-bar.php`,
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
[group-618080750e91b.json](themes/freckle/acf-json/group-618080750e91b.json)), cloned into
individual layouts rather than redefined per layout.

### CSS custom properties, not Sass variables, for the brand palette

`--primary-color-1` through `-6`, `--secondary-color-1/2`, etc. are defined as **CSS** custom
properties on `:root` in [abstracts/_variables.scss](src/sass/abstracts/_variables.scss) (not Sass
`$variables`) — deliberately, so they resolve at runtime and could in principle be themed/overridden
per-scope. Values are currently eyeballed from a rebrand screenshot, not final brand hex — expect
these to change.

### WordPress plumbing specifics

- `themes/freckle/includes/` autoloads under the `Boilerplate\` PSR-4 namespace (composer.json) —
  rename this per project if you want a project-specific namespace; it's a one-line composer.json
  edit, nothing else references it by name.
- `wp-config.php`/`.htaccess`/`uploads.ini` are bind-mounted from `config/` (docker-compose.yml),
  not baked into the theme or the WordPress image.
- `bin/wp/*.php` are one-off content/seed scripts run via `wp eval-file` against the repo root
  (mounted read-only into the `wp-cli` container at `/workspace`).
- `bin/db/{backup,import,restore}.js` (Node) handle DB dump/restore — `npm run db:backup` etc.
