# Changelog

All notable changes are documented here. The format is based on [Keep a Changelog][keep_a_changelog] and this project adheres to [Semantic Versioning][semantic_versioning].

## [1.0.1](https://github.com/phalcon/code-quality/releases/tag/v1.0.1) (2026-09-08)

### Fixed

- `Phalcon\CodeQuality\PhpCsFixer\ConfigFactory::create()` now excludes every `_output` directory below the given paths. A project that scans a `tests` directory picked up the tooling caches in `tests/_output` (the PHPStan `tmpDir`), and php-cs-fixer reported those generated files.

## [1.0.0](https://github.com/phalcon/code-quality/releases/tag/v1.0.0) (2026-09-08)

### Added

- `Phalcon\CodeQuality\PhpCsFixer\Rules`, the shared php-cs-fixer rule set. It starts with the `@PSR12` set, and the Phalcon rules after it replace three of the values of that set.
- `Phalcon\CodeQuality\PhpCsFixer\ConfigFactory::create()`, which builds a php-cs-fixer configuration from a list of paths and a cache file.
- The `Phalcon` phpcs standard: PSR-12 without the `PSR2.Methods.MethodDeclaration.Underscore` sniff. `dealerdirect/phpcodesniffer-composer-installer` registers it, so a project refers to it with `<rule ref="Phalcon"/>`.
- A Docker environment. The `PHP_VERSION` variable selects the PHP version, and the default is 8.1.
- Continuous integration for PHP 8.1 to 8.5, and Dependabot for composer, GitHub Actions and Docker.

[keep_a_changelog]: https://keepachangelog.com/en/1.1.0/
[semantic_versioning]: https://semver.org/spec/v2.0.0.html
