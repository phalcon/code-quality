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

namespace Phalcon\CodeQuality\Tests\Unit\PhpCsFixer;

use Phalcon\CodeQuality\PhpCsFixer\ConfigFactory;
use Phalcon\CodeQuality\PhpCsFixer\Rules;
use PHPUnit\Framework\TestCase;

final class ConfigFactoryTest extends TestCase
{
    public function testCreateAppliesTheSharedRules(): void
    {
        $cacheFile = '/srv/tests/_output/.php-cs-fixer.cache';

        $config = ConfigFactory::create([__DIR__], $cacheFile);

        $this->assertSame(Rules::get(), $config->getRules());
        $this->assertTrue($config->getRiskyAllowed());
        $this->assertTrue($config->getUsingCache());
        $this->assertSame($cacheFile, $config->getCacheFile());
    }

    public function testCreateScansTheGivenPaths(): void
    {
        $source = dirname(__DIR__, 3) . '/src';

        $config = ConfigFactory::create([$source], '/srv/tests/_output/.php-cs-fixer.cache');

        $files = [];
        foreach ($config->getFinder() as $file) {
            $files[] = $file->getFilename();
        }

        $this->assertContains('ConfigFactory.php', $files);
        $this->assertContains('Rules.php', $files);
    }
}
