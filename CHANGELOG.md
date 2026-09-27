# Changelog

All notable changes to this boilerplate are documented here. Format follows
[Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and versioning follows
[Semantic Versioning](https://semver.org/) — see the [README](README.md#versioning) for what a
version bump here actually means for a project already built from this template.

## [Unreleased]

## [1.0.0] - 2026-09-27

Initial public release — generalised from a client project into a reusable boilerplate.

### Added

- ACF Pro flexible-content block system with per-block folders, automatic routing, and per-block
  Vite code-splitting (JS + SCSS built and enqueued only where a block is actually used).
- Three swappable header styles (standard mega-menu, centered, minimal off-canvas).
- Shared BEM + panel-colour convention (White / Light Grey / Orange / Blue) across cards,
  content-grid, buttons, and section backgrounds.
- GSAP-based scroll/entrance animation system (`data-scroll-animation`, `data-lottie`).
- Example content types (Portfolio, Team, Testimonials, News) and idempotent seed scripts
  (`npm run wp:seed`, `npm run wp:seed:blocks`) for spinning up starter content.
- Dockerised local stack (WordPress + MySQL + phpMyAdmin + wp-cli).
- Full lint/format/test tooling: ESLint, Stylelint (with SCSS + BEM plugins), Prettier, PHPCS/PHPCBF
  (WPCS), PHPStan, PHPUnit — wired to actually run, not just configured.
- Formatting-only pre-commit hook (Husky + lint-staged); a GitHub Actions CI workflow runs the
  full eslint/stylelint/phpcs/phpstan/phpunit/build suite on push and PR instead, so local commits
  stay fast.
- GitHub Actions deploy workflows (FTP/SFTP, disabled by default until configured per project).

### Known gaps

Tracked in [CLAUDE.md](CLAUDE.md#known-gaps) rather than duplicated here, since they change more
often than a changelog entry should.

[Unreleased]: https://github.com/unknownsock/wp-boilerplate/compare/v1.0.0...HEAD
[1.0.0]: https://github.com/unknownsock/wp-boilerplate/releases/tag/v1.0.0
