# Changelog — ReactWoo Reviews

All notable changes to this project are documented in this file.

## [1.1.0] - 2026-07-17

### Added
- Product surface rename to **ReactWoo Reviews** (`reactwoo-reviews` catalog slug).
- `GRP_Updater` client for `POST /api/v5/updates/check` (free-slug path).
- GitHub Actions `publish-update.yml` → R2 + ReactWoo updates API.
- `scripts/package_zip.py` and `npm run package:zip`.
- Gutenberg `blocks/*/block.json` metadata registration.
- Cursor release rules (`.cursor/rules/release.mdc`, `git-push-windows.mdc`).

### Changed
- License API `pluginSlug` / free activation plugin id → `reactwoo-reviews` (legacy `goorev` still accepted server-side).
- Elementor: `elementor/widgets/register`, `register()`, `register_controls()`, `get_style_depends()`, `eicon-star` category icon.
- Structured `grp_debug_log()` levels (`debug|info|warning|error`).

### Fixed
- Deduplicated Elementor widget registration path.
- Editor carousel viewport clipping strengthened for Gutenberg preview.

### Compatibility
- Requires WordPress 5.8+, PHP 7.4+, Elementor 3.5+ for widgets.
- Keeps `GRP_*` classes, `grp_*` options, text domain `google-reviews-plugin`, blocks `google-reviews/*`, Elementor widgets `grp-*`.

## [1.0.0] - Initial

- First public release as Google Reviews Plugin / GooRev.
