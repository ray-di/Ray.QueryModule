# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [0.11.1] - 2026-03-03

### Added
- PHP 8.5 support

### Removed
- `koriym/param-reader` dependency — replaced with native `ReflectionParameter::getAttributes()`

## [0.11.0] - 2026-03-01

### Added
- PHP 8.2, 8.3, and 8.4 support

### Changed
- **Require PHP ^8.2** — dropped PHP 8.1 (EOL December 2024)
- Replaced `doctrine/annotations` with native PHP 8 Attributes for all annotation classes
- Upgraded `vimeo/psalm` to v6
- Tightened `ray/di` constraint to `^2.20` (parameter-level `#[Named]` requires 2.20+)
- Upgraded `aura/sql` constraint to `^5.0.3 | ^6.0`

### Removed
- `doctrine/annotations` dependency

## [0.10.0] - 2024-12-18

### Added
- PHP 8.4 support
- Fix SQL with comments can be executed ([#33](https://github.com/ray-di/Ray.QueryModule/pull/33))

### Changed
- Refactored type annotations for query result handling
