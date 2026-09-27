# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

A boilerplate WordPress project, meant to be used as a template for new client sites. Theme:
`themes/boilerplate` — an ACF Pro flexible-content theme, general-purpose across projects. Frontend
build is Vite (SCSS + JS, per-block code-splitting). Local dev runs entirely in Docker.

When starting a new project from this boilerplate, see the README's "Starting a new project from
this boilerplate" section for what to rename/genericize first.

## Known gaps

The durable architecture facts live in the section below and stay current; this is just the
running list of things that are still actually broken or unbuilt. Detailed session-by-session
history lives in `git log` (`git log --oneline`), not here.

- `related_articles`/`custom_title` (used by both `single.php` and `single-portfolio.php` for
  their "related articles" section) have no ACF field group defining them anywhere in `acf-json/` -
  `get_field()` for both always returns empty, so that section never renders. Not yet rebuilt.
- `src/sass/components/_forms.scss` references `var(--base-color-1)` through `-5` (and
  `--base-color-4-60`) about 31 times (`.custom-dropdown`, `.custom-file-input-wrapper`,
  `.custom-radio-group`, `.wpcf7-response-output`, plain `input[type=text]`/`textarea` rules) -
  none of these are defined anywhere in `_variables.scss`, so that styling currently resolves to
  nothing/inherited. Needs a decision on what `--base-color-1..5` should map to in the current
  White/Light Grey/Orange/Blue palette before it's worth fixing.
- No Cypress specs yet (Cypress is already a devDependency, zero spec files exist). Specs for
  horizontal-scroll detection, broken-internal-link crawling, and console-error assertions across
  the main templates have been discussed but not built. (CI itself now exists - see Commands.)
- Two ACF fields with no JSON definition (`field_617e9fb9b4fbc`, used by map/accordion's "Block
  Width" clone) - cosmetic/minor, only worth fixing if you're already touching those blocks.
- `src/js/modules/anim.js` (GSAP-based `data-scroll-animation`/`data-lottie` system - see
  Architecture below) is only exercised on `cards`, `stats`, and `team` so far; every other block
  template has no `data-scroll-animation` attributes at all. Not browser-tested against a running
  Docker stack.
- The Docker `uploads/` permission fix (`chmod 777`, wp-cli container's `www-data` is UID 82 vs the
  wordpress container's UID 33) is session-only, not persisted in `docker-compose.yml` - redo it
  after any container rebuild if image uploads via wp-cli fail again.

## Commands

**Local WordPress (Docker, `localhost:8000`; phpMyAdmin on `:8080`):**

```
npm run start          # docker compose up -d
npm run stop           # docker compose down
npm run shell          # shell into the wordpress container
npm run wp -- <cmd>    # wp-cli via the wp-cli service, e.g. npm run wp -- plugin list
npm run wp:seed        # seed starter pages/home content/main menu (idempotent, see README)
npm run wp:seed:blocks # overwrite Home's content_blocks with every flexible-content layout, for
                        # visually testing all blocks at once (NOT idempotent - always overwrites)
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

Pre-commit (`husky` + `lint-staged`, `.husky/pre-commit`) is deliberately formatting-only -
`prettier --write` on staged JS/SCSS, `phpcbf` + `prettier --write` on staged theme PHP - and never
blocks a commit. `eslint`/`stylelint` (the actual judgment-call linting) run in CI instead
(`.github/workflows/ci.yml`, on push/PR), not locally on every commit. `fix:php`'s script
deliberately always exits 0 - `phpcbf` returns exit code 1 to mean "fixes were applied
successfully" (not failure), which `lint-staged` would otherwise treat as a failed task and revert
the entire commit; don't remove that `exit 0` without accounting for that.

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
    if ( ! $fg ) continue;
    // Without this lookup, acf_import_field_group() always inserts a new
    // acf-field-group post (the JSON has no "ID", only "key") - running
    // this snippet more than once silently piles up duplicate DB rows per
    // group, which then confuses ACFs own key lookup and makes future
    // syncs duplicate too. Resolving the real post ID first makes the
    // import idempotent (update in place, not insert).
    $existing = acf_get_field_group( $fg["key"] );
    if ( $existing && ! empty( $existing["ID"] ) ) {
        $fg["ID"] = $existing["ID"];
    }
    acf_import_field_group( $fg );
}
' --path=/var/www/html
```

Or via wp-admin: Custom Fields → Field Groups → "Sync available".

**Known exception**: `group_5b8ea458deea0_home` ("Hero Options - Home") still duplicates even with
the ID lookup above - `acf_get_field_group('group_5b8ea458deea0_home')` returns `ID => 0` for it
even when a DB post with that exact `post_name` already exists (root cause not tracked down -
this key predates the sync-idempotency fix and is the one group whose DB post carries no `key`
postmeta at all, unlike every other group, which may be related). After any sync, check
`wp post list --post_type=acf-field-group` for a second "Hero Options - Home" row and
`wp post delete <id> --force` the older one if so.

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

### Scroll/entrance animations

`src/js/modules/anim.js` (GSAP + ScrollTrigger, plus Lottie) drives all entrance animation -
there's no other JS-based animation left in the theme (the old hand-rolled scroll-rotate/scroll-
scale/parallax code was removed). Add `data-scroll-animation="fadeIn|fadeOut|zoomIn|zoomOut|
slideIn|slideOut|slideUp|slideUpNoOpacity"` to any element to animate it; optional
`data-scroll-start`, `data-scroll-duration`, `data-scroll-delay`, `data-disable-mobile`,
`data-play-once`, `data-animate-on-load-only` override the per-call defaults in `anim.js`'s
`defaultConfig`. `data-lottie="/path/to/file.json"` (plus optional `data-lottie-loop`/
`data-lottie-autoplay`, both default true) loads a Lottie animation into that element. Currently
only exercised on the `cards`, `stats`, and `team` blocks - see Known gaps.

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
directly with BEM children. The old grid system (`_cols.scss`, `.row`/`.col-*`) predated this
convention and backed `single-portfolio.php`, `side-bar.php`, and `component-icon.php` — all three
are gone now (dead sidebar feature, orphaned component, and `single-portfolio.php` rewritten to
match `single.php`'s shape), so `_cols.scss` was deleted along with them.

### Shared "panel colour" palette

`$panel-colours` (a Sass map in [abstracts/_mixins.scss](src/sass/abstracts/_mixins.scss)) is the
single source of truth for the White/Light Grey/Orange/Blue colour choice offered on cards,
content-grid columns, and section backgrounds — `panel-colour($name, bg|fg|accent)` looks up a
token, `panel-colour-vars($name, ...)` sets a component's custom properties from it. Each consumer
still owns _how_ it applies the colour (custom properties for a card's coordinated bg/text/accent
vs a flat `background-color`/`color` for a section) — only the name→token mapping is shared. Card/
content-grid per-item colour fields additionally offer a `none` choice (no clone/panel involved -
"none" just skips applying any panel colour), which is not part of `$panel-colours` itself.

### Section background colour vs. block-local overrides

Section-level background colours (`section.white`/`.grey-light`/`.orange`/`.blue`, the only four
values `background_colour` can ever hold) and their text-contrast flip (dark bg → white text) live
unlayered in [themes/_default.scss](src/sass/themes/_default.scss), deliberately outside `@layer
theme` — a layer can never win against unlayered content, so background-colour assignment has to
stay unlayered to reliably beat a block's own hardcoded default background. `.transparent`/
`.gradient` used to be handled here too but were dead (no field or block default could ever
produce them) - removed. The `:not(#\#)` hack appears throughout this file and elsewhere
(`_modal.scss`, `_forms.scss`) purely to add CSS-specificity weight without changing what a
selector matches — used whenever a rule needs to outrank another selector of otherwise-equal class
count (a plain class-count tie is broken by element count, which silently favours the _wrong_ rule
more than once in this codebase — check for this class of bug first when a colour/text override
"isn't applying", and check any per-block `section.white &`-style guard covers `.grey-light` too -
`_stats.scss` didn't, until this session, and stayed invisible-white-on-light-grey until it did).

### `background_colour` field

A sitewide ACF clone field (`field_617ea04499fac`, defined once in
[group-618080750e91b.json](themes/boilerplate/acf-json/group-618080750e91b.json)), cloned into
individual layouts rather than redefined per layout. `button_colour` (`field_63ee393743836`, same
file) intentionally mirrors this field's choice list so a button can always match its section -
keep the two in sync by hand if either changes.

### CSS custom properties, not Sass variables, for the brand palette

`--primary-color-1` (orange accent), `--primary-color-2` (blue accent), `--primary-color-3` (dark
neutral - also `--primary-text-color`, the sitewide default body/heading text colour, so don't
repoint this at a saturated colour without checking every page's text), `--secondary-color-1`
(white) and `--secondary-color-2` (light grey) are defined as **CSS** custom properties on `:root`
in [abstracts/_variables.scss](src/sass/abstracts/_variables.scss) (not Sass `$variables`) —
deliberately, so they resolve at runtime and could in principle be themed/overridden per-scope.
Values are currently eyeballed placeholders, not final brand hex — expect these to change. The
palette was cut from 6 colours to 3 (White/Orange/Navy) earlier in this file's own session log,
then this session added a light-grey neutral and swapped Navy for a true Blue accent (its own
`--primary-color-2` slot, kept separate from `--primary-color-3` specifically so the sitewide text
colour didn't silently become blue too) - White/Light Grey/Orange/Blue is the current set.

### WordPress plumbing specifics

- `themes/boilerplate/includes/` autoloads under the `Boilerplate\` PSR-4 namespace (composer.json) —
  rename this per project if you want a project-specific namespace; it's a one-line composer.json
  edit, nothing else references it by name.
- `wp-config.php`/`.htaccess`/`uploads.ini` are bind-mounted from `config/` (docker-compose.yml),
  not baked into the theme or the WordPress image.
- `.env` is real, not decorative — `docker-compose.yml` reads `WP_PORT`/`PHPMYADMIN_PORT`/
  `DB_NAME`/`DB_USER`/`DB_PASS`/`DB_ROOT_PASSWORD` via `${VAR:-default}` substitution (Compose
  loads `.env` from the project root automatically for this, no `env_file:` directive needed).
  `wp-config.php` itself only ever reads `WORDPRESS_*`-prefixed vars (the official image's own
  convention, via `getenv_docker()`) - `docker-compose.yml`'s `environment:` blocks are what bridge
  the two names. If you add a new `.env` variable, it needs a matching `${...}` in
  `docker-compose.yml` or it does nothing - this exact gap (a fully vestigial `.env.example` with
  zero working variables, copied from some other project's compose setup) went unnoticed for a
  long time before being wired up for real.
- `bin/wp/*.php` are one-off content/seed scripts run via `wp eval-file` against the repo root
  (mounted read-only into the `wp-cli` container at `/workspace`).
- `bin/db/{backup,import,restore}.js` (Node) handle DB dump/restore — `npm run db:backup` etc.
