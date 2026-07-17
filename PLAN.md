# ReactWoo Reviews — Development Plan

## Project Overview
**Project Name:** ReactWoo Reviews (formerly Google Reviews Plugin / GooRev)  
**Version:** 1.1.0  
**Catalog slug:** `reactwoo-reviews`  
**License:** GPL v2 or later  
**Developer:** ReactWoo Ltd  

## Productization status (2026-07)

- [x] Product-surface rename (folder/main PHP/display/catalog slug)
- [x] R2 publish workflow + `package:zip`
- [x] In-plugin updater (`GRP_Updater`)
- [x] License aliases `goorev` → `reactwoo-reviews`
- [x] Elementor 3.5+/4.x registration APIs
- [x] Gutenberg `block.json` metadata
- [x] Structured logging (`grp_debug_log` levels)
- [x] PHPUnit stubs + updater tests
- [x] `.pot` scaffold, CHANGELOG, AGENTS.md, readme Stable tag
- [x] Free updates via `UPDATES_FREE_SLUGS`

## Development Phases

### Phase 1: Core Foundation ✅
- [x] Plugin structure and file organization
- [x] Main plugin class and activation/deactivation hooks
- [x] Basic admin interface structure
- [x] Google My Business API integration setup
- [x] Database schema and data models

### Phase 2: API Integration & Data Management ✅
- [x] OAuth 2.0 authentication flow
- [x] Business Profile API client implementation
- [x] Review data synchronization system
- [x] Caching mechanism for API responses
- [x] Error handling improvements for disabled APIs and insufficient scopes
- [x] Structured logging system

### Phase 3: Display System ✅
- [x] Review display templates and styles
- [x] Shortcode implementation with parameters
- [x] WordPress widget development
- [x] Responsive CSS framework
- [x] Custom CSS support
- [x] Grid / Grid Carousel layouts
- [x] Theme variants (light/dark/auto)

### Phase 4: Page Builder Integration ✅
- [x] Elementor widget development (modern register APIs)
- [x] Gutenberg block implementation (`block.json`)
- [x] Widget/block configuration interfaces
- [x] Elementor 3.5+/4.x compatibility pass

### Phase 5: Admin Interface ✅
- [x] Settings page with API configuration
- [x] Reviews management dashboard
- [x] Style customization interface
- [x] Help documentation system
- [x] Analytics surfaces for Pro/Enterprise (addon-gated)

### Phase 6: Testing & Optimization ✅
- [x] PHPUnit bootstrap + updater tests
- [x] Security review of license/update clients (nonces/caps preserved)
- [x] Caching and query patterns reviewed

### Phase 7: Documentation & Release ✅
- [x] User documentation (readme.txt)
- [x] Developer documentation (AGENTS.md, CHANGELOG)
- [x] Translation scaffold (`.pot`)
- [x] WordPress.org readiness (readme headers)
- [x] R2 release pipeline

## Free vs Pro

Pro/Enterprise remain gated by `GRP_License` and `goorev-*` packages. Plugin updates are free for all installs (`reactwoo-reviews` on `UPDATES_FREE_SLUGS`).

## Install / upgrade

Install folder must be `reactwoo-reviews/` with main file `reactwoo-reviews.php`. Migrating from `GooRev/`: deactivate old plugin, replace folder, activate ReactWoo Reviews. Options, blocks, and Elementor widget names are unchanged.
