# code-quality

Shared code quality configuration for the Phalcon Framework projects.

The rules live here once. A project sets only its own paths, its cache file and
its display preferences. Change a rule in this package and every project gets it
on the next `composer update`.

The package holds two configurations:

| Tool | What this package gives you |
|------|-----------------------------|
| [php-cs-fixer](https://cs.symfony.com/) | `Phalcon\CodeQuality\PhpCsFixer\ConfigFactory` and the shared rule set |
| [phpcs](https://github.com/PHPCSStandards/PHP_CodeSniffer) | The `Phalcon` coding standard |

The two tools are complementary. php-cs-fixer orders the class members and the
import statements and aligns the assignments. phpcs checks the file against
PSR-12. The rule set starts with the `@PSR12` set, so the output of php-cs-fixer
passes phpcs with no findings.

## Requirements

- PHP 8.1 or later, below 9.0

`friendsofphp/php-cs-fixer` and `squizlabs/php_codesniffer` are dependencies of
this package. A project that requires `phalcon/code-quality` receives both
tools, and the whole organization stays on the same tool versions.

## Installation

```bash
composer require --dev phalcon/code-quality
```

The `Phalcon` standard reaches phpcs through
`dealerdirect/phpcodesniffer-composer-installer`. Composer blocks a plugin until
you allow it, so add this to the `composer.json` file of the project:

```json
{
    "config": {
        "allow-plugins": {
            "dealerdirect/phpcodesniffer-composer-installer": true
        }
    }
}
```

Without this entry `composer install` stops with an error, and phpcs reports
`Referenced sniff "Phalcon" does not exist.`

## Use: php-cs-fixer

Keep a `resources/php-cs-fixer.php` file in the project with only the paths and
the cache file:

```php
<?php

declare(strict_types=1);

use Phalcon\CodeQuality\PhpCsFixer\ConfigFactory;

$root = dirname(__DIR__);

return ConfigFactory::create(
    [
        $root . '/src',
        $root . '/tests/unit',
    ],
    $root . '/tests/_output/.php-cs-fixer.cache'
);
```

`ConfigFactory::create()` sets the rules, the parallel configuration and the
risky flag. The rule `declare_strict_types` is risky, which is why the factory
turns the flag on.

No `require` line is necessary. The php-cs-fixer executable loads the autoloader
of the project before it reads the configuration file.

## Use: phpcs

Keep a `resources/phpcs.xml` file in the project with only the paths and the
display arguments:

```xml
<?xml version="1.0"?>
<ruleset name="My Project">
    <arg name="colors"/>
    <arg value="ps"/>

    <rule ref="Phalcon"/>

    <file>../src</file>
    <file>../tests/unit</file>
</ruleset>
```

The `Phalcon` standard is PSR-12 without the
`PSR2.Methods.MethodDeclaration.Underscore` sniff, because Phalcon has methods
with an underscore prefix.

Display arguments such as `colors`, `p` and `s` stay in the project file. The
progress output corrupts a JSON report, so the shared standard must not set it.

## The rules

`src/PhpCsFixer/Rules.php` holds the full rule set with a comment on each rule
that needs one. The short version:

- Import statements: alphabetical.
- Class members: by visibility (public, then protected, then private), and
  alphabetical inside each group.
- One blank line between constants, properties and methods.
- The `=` of consecutive assignments and the `=>` of consecutive array elements
  line up in a column.
- `declare(strict_types=1)` in every file.
- PHPDoc tags that only repeat the native type go away.

**`@PSR12` must stay the first key of the array.** php-cs-fixer reads the rules
in order and a later key replaces an earlier one. The set carries its own
`binary_operator_spaces`, `ordered_imports` and `ordered_class_elements` values,
and the Phalcon rules after it replace those three on purpose. Move the set down
and it removes the alignment and the alphabetical order without an error. The
test suite catches this.

## Development

The project has a Docker environment. Select the PHP version with the
`PHP_VERSION` variable. The default is 8.1.

```bash
docker compose up -d --build                   # PHP 8.1, the default
PHP_VERSION=8.4 docker compose up -d --build   # PHP 8.4
```

The container is `code-quality-<version>`, for example `code-quality-8.1`. One
container runs at a time. A different `PHP_VERSION` replaces the container that
runs now, because both use the same compose service.

Each version has its own image tag, `code-quality:<version>`, so a change of
`PHP_VERSION` always starts the PHP version that the container name says. Give
`--build` only when you change the Dockerfile.

```bash
docker exec -w /srv code-quality-8.1 composer install
docker exec -w /srv code-quality-8.1 composer check
```

### Composer scripts

The names follow [pds/composer-script-names](https://github.com/php-pds/composer-script-names).

| Script | What it does |
|--------|--------------|
| `composer check` | `cs-check`, then `test` |
| `composer cs-check` | php-cs-fixer and phpcs, report only |
| `composer cs-fix` | php-cs-fixer and phpcbf, applies the changes |
| `composer test` | phpunit |

### Tests

The test suite runs both tools over a fixture and compares the result with a
stored file. This catches a rule that a new tool release renames or removes, and
a rule that quietly changes its behavior. Edit `src/PhpCsFixer/Rules.php` and you
must regenerate `tests/fixtures/expected/Sample.php`, which keeps every rule
change visible in the diff.

The directory layout follows [pds/skeleton](https://github.com/php-pds/skeleton).

## License

BSD 3-Clause. See the [LICENSE](LICENSE) file.
