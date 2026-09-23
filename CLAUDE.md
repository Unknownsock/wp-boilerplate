# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

A boilerplate WordPress project, meant to be used as a template for new client sites. Theme:
`themes/boilerplate` — an ACF Pro flexible-content theme, general-purpose across projects. Frontend
build is Vite (SCSS + JS, per-block code-splitting). Local dev runs entirely in Docker.

When starting a new project from this boilerplate, see the README's "Starting a new project from
this boilerplate" section for what to rename/genericize first.

## Current status (2026-09-23)

**Intent:** turn what was a specific client project (formerly "freckle"/"imp") into a genuinely
generic, reusable boilerplate — strip anything project-specific, keep the theme unstyled/neutral
enough to restyle per client, and make the common structural choices (header layout, content
types) swappable rather than hardcoded. Two commits exist so far (both local only, never pushed):
the rename/cleanup/header-styles work, then a second pass trimming blocks and dead styling. This
session's remaining work (block-title inlining, accordion simplification, jcf/sidebar removal,
the new GSAP anim system) has **not been committed yet** — do that first in the next session,
after re-verifying the checklist below still holds.

**Done across prior sessions** (rename to `boilerplate`, leaked client data removed, `clients`→
`portfolio` CPT rename, three swappable header styles, dead JS/SCSS cleanup, `bin/wp/seed-content.php`
extended with extra pages/menus) — see git log (`git log --oneline`) for the two commit messages,
which are detailed enough to stand in for a longer recap here.

**Done this session (uncommitted):**
- Removed 7 more content blocks (instagram, page_link_grid, our_work_feed, full_image_gallery,
  services, columns, testimonial_contact) - consolidating listing-style needs onto the one
  `listing` block. Their ACF layouts, block folders, and seed rows are all gone.
- Footer redesign: logo + address + phone/email is now one left column, two footer menus render
  next to it (`menu-3` "Footer Menu 2" - the location existed but was never seeded), copyright
  stays in the bottom bar. Fixed a real contrast bug this surfaced (placeholder logo text was
  invisible against the footer's own navy background via `currentColor` inheritance).
- Removed npm packages `sass-fluid`, `pace-js`, `ll`, `jcf` (none were wired up) and their dead
  CSS/markup (`.jcf-checkbox`, `.jcf-button`, `jcf-ignore`).
- Deleted the entire dead sidebar feature: root `sidebar.php` always no-oped (checked
  `is_active_sidebar('sidebar-1')`, never registered anywhere), `template-parts/layout/side-bar.php`
  had zero callers, `_sidebar.scss`'s `.site-sidebar` was never referenced anywhere.
- Removed the sticky block's decorative rotating SVG shapes, feature-grid layout-1's extra
  bordered "panel" wrapper div, and the small-card hover lift/zoom (kept just a title colour
  change).
- Fixed a real header/off-canvas-panel height mismatch: `.o-site-header`'s own mobile height was
  `--nav-height-xl` (12rem) while every off-canvas panel assumed `--nav-height-sm` (8rem), so the
  panel opened 4rem into the header instead of right below it.
- Inlined the shared `component-block-title.php` into `block-listing.php` (its only caller) and
  deleted the shared component - confirmed legacy/single-use per your call.
- Simplified the accordion block: removed the swap-image-on-expand behaviour
  (`accordion.js`'s `setImage()`, the per-item `image` ACF field, `.b-accordion__media`) - it's a
  plain text accordion now.
- Removed the old hand-rolled scroll animations entirely: `layout.js`'s `scrollRotate()`
  (`[data-rotate-scroll]`, already fully dead - nothing rendered that attribute after the sticky
  shapes were removed) and `scrollScale()` (`.js-sticky-scale`, also already dead), plus
  `hero.js` (100% parallax, deleted outright) and `layout.js`'s `parallax()`/`.js-parallax`.
- Added a new GSAP-based animation system: `src/js/modules/anim.js` (data-attribute driven -
  `data-scroll-animation="fadeIn|fadeOut|zoomIn|zoomOut|slideIn|slideOut|slideUp|slideUpNoOpacity"`,
  plus `data-lottie="/path.json"` for Lottie), `gsap`/`lottie-web` added as npm deps, wired into
  `app.js`, minimal `src/sass/base/_anim.scss` (`will-change` + `[data-lottie]` sizing). **Nothing
  in any template actually uses these data attributes yet** - the system is wired up and builds
  clean, but is currently inert until someone adds `data-scroll-animation="..."` to real markup.

**Still open / not yet done:**
- **Wireframe/colour system**: strip the primary palette down to a max of 3 colours and restyle
  toward a neutral wireframe look - not started. Also asked: remove `border-radius` everywhere
  (explicitly called out as "a theme style" that shouldn't be baked into the generic boilerplate).
  This is the single biggest remaining task - touches `abstracts/_variables.scss`'s
  `--primary-color-1` through `-6`, the `$panel-colours` map in `abstracts/_mixins.scss`
  (White/Blue/Orange/Navy section-colour choices - CLAUDE.md's own documented "Shared panel
  colour palette" section describes this system), and `border-radius` declarations scattered
  across most component/block SCSS files. Do this as its own focused pass, not mixed with other
  edits - it's cross-cutting and easy to half-finish.
- **Seed alternating background colours**: `bin/wp/seed-all-blocks.php` should cycle each block's
  `background_colour` field (white/grey-light/blue/navy/gradient) through the seeded rows instead
  of leaving most at their default, purely so adjacent sections are visually distinguishable when
  testing. Only blocks that actually have a `background_colour` field: separator, stats, cards,
  feature_grid, content_grid, accordion, buttons, contact.
- **More placeholder content**: add more example Team Member posts (currently only one) and more
  example blog posts (`post` type - needed for the `listing` block's query mode and
  `latest_blogs` to have more than a couple of items to show).
- **Broad SCSS/JS comment audit**: asked twice this session ("too many comments... AI-like") -
  only partially addressed as a side effect of other edits. Needs a dedicated pass over
  `src/sass/**` and `src/js/**` specifically looking for over-explained/narrative comments, per
  the WHY-only rule already in this file's top-level instructions. Don't touch the comments
  CLAUDE.md itself calls out as deliberately load-bearing (the `:not(#\#)` specificity hack, the
  unlayered background-colour rationale, the header-dispatch pattern, the `simple-nav-list`
  same-specificity `@media` gotcha below) - those exist because the mistake was already made once.
- **CI / testing**: discussed but not built. Offered and the user was interested in: (1) a GitHub
  Actions workflow running `composer qa` + eslint + stylelint + build on push/PR, with the local
  pre-commit hook loosened to formatting-only; (2) a Cypress suite (already a devDependency,
  zero spec files currently) for horizontal-scroll detection, broken-internal-link crawling, and
  console-error assertions across the main templates. Neither exists yet - both are concrete,
  well-scoped next steps if asked for.
- The GSAP anim system (above) needs someone to actually add `data-scroll-animation` attributes
  to real block templates to be useful - right now it's dead weight in the bundle until used.
- The standard header's mega-menu CTA-card feature (`mega_cta_*` fields) remains untouched/
  unverified - no seeded menu item has one configured.
- `client_logo` is still not a registered ACF field on the `portfolio` CPT (the `logos` block
  reads it via `get_field('client_logo', $client)` but nothing defines it) - pre-existing gap,
  flagged repeatedly, never fixed.
- The `--sidebar-width` variable removal and the two ACF-field-with-no-JSON-definition gaps
  (`field_617e9fb9b4fbc`, used by map/accordion's "Block Width" clone) are cosmetic/minor and
  probably not worth a dedicated pass on their own - only worth fixing if you're already touching
  those specific blocks.
- Environment notes: the Docker `uploads/` permission fix (`chmod 777`, wp-cli container's
  `www-data` is UID 82 vs the wordpress container's UID 33) is session-only, not persisted in
  `docker-compose.yml` - it'll need redoing after a container rebuild if image uploads via wp-cli
  fail again. There was also a stray `npm run dev` process left running from before a recent
  session (unrelated to any of this work) - if asset loading looks like it's hitting `:5173`
  unexpectedly, check for a leftover dev server before assuming a build problem.

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

### Scroll/entrance animations

`src/js/modules/anim.js` (GSAP + ScrollTrigger, plus Lottie) drives all entrance animation -
there's no other JS-based animation left in the theme (the old hand-rolled scroll-rotate/scroll-
scale/parallax code was removed). Add `data-scroll-animation="fadeIn|fadeOut|zoomIn|zoomOut|
slideIn|slideOut|slideUp|slideUpNoOpacity"` to any element to animate it; optional
`data-scroll-start`, `data-scroll-duration`, `data-scroll-delay`, `data-disable-mobile`,
`data-play-once`, `data-animate-on-load-only` override the per-call defaults in `anim.js`'s
`defaultConfig`. `data-lottie="/path/to/file.json"` (plus optional `data-lottie-loop`/
`data-lottie-autoplay`, both default true) loads a Lottie animation into that element. As of
2026-09-23 no template actually uses either attribute yet - the system is wired up but inert.

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
owns _how_ it applies the colour (custom properties for a card's coordinated bg/text/accent vs a
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
element count, which silently favours the _wrong_ rule more than once in this codebase — check for
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
