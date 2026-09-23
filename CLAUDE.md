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
- **Wireframe/colour system pass, done**: cut the primary palette from 6 colours down to 3
  (`--primary-color-1` accent, `--primary-color-3` dark, `--secondary-color-1` light -
  `_variables.scss`) and dropped `--primary-color-2/4/5/6`, the 3 `--gradient-color-*` stops, and
  `--secondary-color-2`. `$panel-colours` (`abstracts/_mixins.scss`) is now White/Orange/Navy only
  (dropped Blue) and the `background_colour`/`button_colour` ACF choice lists (both `group-
  618080750e91b.json` fields, plus content-grid's and cards' per-item colour fields in
  `group-5b9930d7812e5.json`) were trimmed to match - `gradient` and `grey-light` are gone as
  choices too, `_default.scss`'s `.gradient`/`.grey-light` section rules deleted along with them.
  Fixed up every consumer of a removed variable (nav mega-menu gradients simplified to flat white,
  slider nav buttons, feature-grid's row divider, a couple of pre-existing broken
  `rgba(var(--secondary-color-2), ...)`/`hsla(...)` calls in `_modal.scss`/`_blocks.scss` that were
  no-ops even before this pass) and every block that hardcoded a `grey-light`/`gradient` class or
  default (`sticky`, `latest-blogs`, `cards`, `stats`, plus the two `bin/wp/seed-*.php` scripts).
  Removed `border-radius` everywhere it was purely decorative corner-rounding (~35 declarations
  across `src/sass/**` and per-block SCSS) - kept the ones that form a shape rather than soften a
  corner (`50%` circles: avatars, dots, slider controls; `999rem`/`9999px` pills: buttons, the
  underline-active mixin), per your call when asked. `npm run build` is clean.
- **Seed alternating background colours, done**: `bin/wp/seed-all-blocks.php`'s 8 blocks with a
  `background_colour` field (separator, stats, cards, feature_grid, content_grid, accordion,
  buttons, contact) now round-robin white/orange/navy in source order instead of all defaulting to
  white (contact used to be hardcoded navy) - no two adjacent ones repeat the same colour. Only
  this file - `seed-content.php`'s two `background_colour` rows weren't in scope (it's the
  idempotent starter-content script, not the "see every block" testing tool this task was about).
- **Off-palette pink hex, done** - turned out to be bigger than just `_contact.scss`/`_modal.scss`
  (`#fb62b2`/`#FB62B2`/`#DF0E7B` were scattered across 6 files, not 2). Fixed all of them:
  `.b-contact__form`'s panel is now white (`--secondary-color-1`) and the contact modal's `__panel`
  stays orange (`--primary-color-1`, already on-palette) with its label/input override swapped from
  pink to white - preserves the original "light panel / dark-accent panel, inverted" two-tone design
  intent from the comments, just with on-palette colours instead of a stray pink. Inline SVG
  `fill`/`stroke` attributes in live-DOM markup (social icons in `component-social-sticky.php` +
  `component-socials.php`, the mega-menu dropdown arrow in `class-custom-menu-walker.php`) now use
  `var(--primary-color-1)` directly. Data-URI-encoded SVGs (`_forms.scss`'s checkbox tick,
  `_testimonials.scss`'s quote-mark decoration) can't reference a CSS custom property, so those got
  the literal `--primary-color-1` hex (`#e35b30`) hardcoded instead, with a comment noting they need
  to be kept in sync by hand if the accent colour ever changes.
- **More placeholder content, done**: `seed-all-blocks.php` now seeds 3 example Team Member posts
  (was 1, varied roles/bio modes) and 6 example blog posts (`post` type, real WP posts so both the
  `listing` block's query mode and `latest_blogs` have enough to show) via a new
  `seed_example_posts()` helper (title-deduped, safe to rerun).
- **Mega-menu CTA card, done**: `seed-content.php` now sets the `group_68b9a001megacta` fields
  (tag/title/text/link) on the "About" item in Main Menu, so the standard header's mega-menu has at
  least one CTA card configured to check. No image set (seed-content.php has no media-import
  plumbing the way seed-all-blocks.php does) - optional field, left blank.
- **GSAP anim system, lightly exercised**: added `data-scroll-animation="slideUp"` (staggered by
  index) to each card in `block-cards.php`, and `data-scroll-animation="fadeIn"` (staggered) to each
  item in `block-stats.php` and `block-team.php`, so the system isn't 100% inert any more. Not
  browser-tested this session (no confirmed running Docker stack) - check these three blocks in the
  browser before relying on the effect; the rest of the block templates still have no
  `data-scroll-animation` attributes at all.

**Newly found, not fixed (bigger than expected, flagged rather than guessed at):**
- **`client_logo` gap turned out to be part of a much bigger hole**: `single-portfolio.php` calls
  `get_field()` for `content_mode_toggle`, `above_gallery_text`, `gallery_layout`, `gallery`,
  `before_image`, `after_image`, `below_before_after_text`, `related_articles`, and `custom_title` -
  **none of these have an ACF field group defining them anywhere in `acf-json/`** (confirmed via
  grep - `content_mode_toggle` doesn't appear in any JSON file). `includes/theme-settings.php`
  still has a `content_mode_toggle`-driven field-hiding hook (~line 352) referencing this same
  missing group. This means the entire portfolio single template's field UI is currently
  unavailable in wp-admin on a fresh site - a much bigger pre-existing gap than the single missing
  `client_logo` field originally flagged. Didn't attempt to reconstruct the field group blind -
  needs your call on what that group should actually contain before anyone builds it.
- **Undefined `--base-color-*` custom properties**: `src/sass/components/_forms.scss` references
  `var(--base-color-1)` through `-5` (and `--base-color-4-60`) about 31 times (`.custom-dropdown`,
  `.custom-file-input-wrapper`, `.custom-radio-group`, `.wpcf7-response-output`, the plain
  `input[type=text]`/`textarea` rules) - **none of these are defined anywhere in
  `_variables.scss`**, so all of that styling currently resolves to nothing/inherited, not the
  colours the rules imply. Predates this session; not touched, since it's a separate rename/mapping
  decision (what should `--base-color-1..5` even become in the new 3-colour system?), not a
  border-radius/palette-reduction task.

- **Broad SCSS/JS comment audit, done**: asked twice this session ("too many comments... AI-like").
  Swept `src/sass/**` and `src/js/**` (21 files touched), ~60+ individual removals/trims - deleted
  commented-out dead code (`_mixins.scss`'s `underline()` mixin, swiper pagination styles, one-line
  `// property: value;` leftovers across `_navigation.scss`/`_hero.scss`/`_layout.scss`/
  `_blocks.scss`/`_card.scss`/`_modal.scss`/`_single.scss`/`_header.scss`), trimmed multi-sentence
  WHY comments to one line (`_card.scss`, `_mixins.scss`, `_buttons.scss`, `_navigation.scss`), and
  removed WHAT-only comments restating the next line (heaviest in `layout.js`'s `modal()` function
  and `forms.js`'s `customforms_select()`). Left alone: genuinely non-obvious WHY content (browser
  quirks, specificity ties, JS timing bugs, hover-intent debounce logic in `navigation.js`), and
  `_reset.scss` (a normalize.css-style file where every comment documents a real cross-browser
  quirk). Verified after the fact - re-grepped `:not(#\#)` (still the same 6 files: `_default.scss`,
  `_header.scss`, `_navigation.scss`, `_modal.scss`, `_card.scss`, `_mixins.scss`), and the
  `@layer theme` rationale comment at the top of `_default.scss` is untouched byte-for-byte. `npm
  run build` clean.
- **CI / testing**: discussed but not built (explicitly deferred this session). Offered and the
  user was interested in: (1) a GitHub Actions workflow running `composer qa` + eslint + stylelint
  + build on push/PR, with the local pre-commit hook loosened to formatting-only; (2) a Cypress
  suite (already a devDependency, zero spec files currently) for horizontal-scroll detection,
  broken-internal-link crawling, and console-error assertions across the main templates.
- The two ACF-field-with-no-JSON-definition gaps (`field_617e9fb9b4fbc`, used by map/accordion's
  "Block Width" clone) are cosmetic/minor - only worth fixing if you're already touching those
  specific blocks. (The `--sidebar-width` variable this bullet used to also list has since been
  confirmed already gone - no longer referenced anywhere in the codebase.)
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
single source of truth for the White/Orange/Navy colour choice offered on cards, content-grid
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

`--primary-color-1` (accent), `--primary-color-3` (dark), and `--secondary-color-1` (light) — a
deliberately-capped 3-colour palette, see this file's own session log above for the reduction from
6 — are defined as **CSS** custom properties on `:root` in
[abstracts/_variables.scss](src/sass/abstracts/_variables.scss) (not Sass `$variables`) —
deliberately, so they resolve at runtime and could in principle be themed/overridden per-scope.
Values are currently eyeballed from a rebrand screenshot, not final brand hex — expect these to
change.

### WordPress plumbing specifics

- `themes/boilerplate/includes/` autoloads under the `Boilerplate\` PSR-4 namespace (composer.json) —
  rename this per project if you want a project-specific namespace; it's a one-line composer.json
  edit, nothing else references it by name.
- `wp-config.php`/`.htaccess`/`uploads.ini` are bind-mounted from `config/` (docker-compose.yml),
  not baked into the theme or the WordPress image.
- `bin/wp/*.php` are one-off content/seed scripts run via `wp eval-file` against the repo root
  (mounted read-only into the `wp-cli` container at `/workspace`).
- `bin/db/{backup,import,restore}.js` (Node) handle DB dump/restore — `npm run db:backup` etc.
