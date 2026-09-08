<?php

/**
 * This file is part of the Phalcon Framework.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Phalcon\CodeQuality\PhpCsFixer;

use PhpCsFixer\Config;
use PhpCsFixer\ConfigInterface;
use PhpCsFixer\Finder;
use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;

/**
 * Builds a php-cs-fixer configuration from the shared rules.
 *
 * A project keeps a `resources/php-cs-fixer.php` file with only this:
 *
 *     use Phalcon\CodeQuality\PhpCsFixer\ConfigFactory;
 *
 *     $root = dirname(__DIR__);
 *
 *     return ConfigFactory::create(
 *         [
 *             $root . '/src',
 *             $root . '/tests/unit',
 *         ],
 *         $root . '/tests/_output/.php-cs-fixer.cache'
 *     );
 */
final class ConfigFactory
{
    /**
     * @param array<int, string> $paths     Directories to scan.
     * @param string             $cacheFile Where php-cs-fixer keeps its cache.
     */
    public static function create(array $paths, string $cacheFile): ConfigInterface
    {
        return (new Config())
            ->setParallelConfig(ParallelConfigFactory::detect())
            // declare_strict_types is a risky rule.
            ->setRiskyAllowed(true)
            ->setUsingCache(true)
            ->setCacheFile($cacheFile)
            ->setRules(Rules::get())
            ->setFinder(Finder::create()->in($paths));
    }
}
