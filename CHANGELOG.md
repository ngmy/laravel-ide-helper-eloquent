# Release Notes

## [0.4.0](https://github.com/ngmy/laravel-ide-helper-eloquent/compare/0.3.0...0.4.0) - 2026-02-22

- Fix a bug where the replacement process intended for Relation stubs (`TModel` -> `TRelatedModel`) was also being
  applied to Model stubs.
- Add IDE Helper support for [`mpyw/laravel-local-class-scope`](https://github.com/mpyw/laravel-local-class-scope).

## [0.3.0](https://github.com/ngmy/laravel-ide-helper-eloquent/compare/0.2.0...0.3.0) - 2026-02-07

- Add support for Laravel 12.

## [0.2.0](https://github.com/ngmy/laravel-ide-helper-eloquent/compare/0.1.0...0.2.0) - 2025-11-20

- Add autocompletion support for Eloquent relation method chains (e.g., `$user->posts()->where(...)`).

## 0.1.0 - 2025-01-22

- Initial release.
