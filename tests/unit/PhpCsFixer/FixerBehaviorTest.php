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

use PHPUnit\Framework\TestCase;

/**
 * Runs php-cs-fixer over a fixture and compares the result with a stored file.
 *
 * This test protects the consumer projects from two failures:
 * - A rule name or option that a new php-cs-fixer release removed or renamed.
 *   php-cs-fixer stops with a non-zero exit code and names the rule.
 * - A silent change of behavior. The stored file is the house style, written
 *   out. If a fixer release changes what a rule does, the comparison fails.
 *
 * The stored file is PSR-12 clean, because the shared rules start with the
 * `@PSR12` set. That set must stay the first key of the rules array: the keys
 * after it replace three of its values on purpose. If somebody moves the set,
 * the stored file loses its alignment and its alphabetical order, and this
 * test fails. See Rules::get().
 */
final class FixerBehaviorTest extends TestCase
{
    private string $root;

    private string $workDirectory;

    protected function setUp(): void
    {
        $this->root          = dirname(__DIR__, 3);
        $this->workDirectory = $this->root . '/tests/_output/fixtures';

        $this->removeWorkDirectory();
        mkdir($this->workDirectory, 0o775, true);
    }

    protected function tearDown(): void
    {
        $this->removeWorkDirectory();
    }

    public function testSharedRulesProduceTheStoredOutput(): void
    {
        $target = $this->workDirectory . '/Sample.php';

        copy($this->root . '/tests/fixtures/input/Sample.php', $target);

        [$status, $output] = $this->runFixer();

        $this->assertSame(0, $status, 'php-cs-fixer failed:' . PHP_EOL . $output);
        $this->assertStringEqualsFile(
            $this->root . '/tests/fixtures/expected/Sample.php',
            (string) file_get_contents($target)
        );
    }

    private function removeWorkDirectory(): void
    {
        if (false === is_dir($this->workDirectory)) {
            return;
        }

        foreach ((array) glob($this->workDirectory . '/*') as $file) {
            unlink((string) $file);
        }

        rmdir($this->workDirectory);
    }

    /**
     * @return array{0: int, 1: string}
     */
    private function runFixer(): array
    {
        $command = escapeshellarg($this->root . '/vendor/bin/php-cs-fixer')
            . ' fix'
            . ' --config=' . escapeshellarg($this->root . '/tests/fixtures/config.php')
            . ' --using-cache=no'
            . ' 2>&1';

        $lines  = [];
        $status = 0;

        exec($command, $lines, $status);

        return [$status, implode(PHP_EOL, $lines)];
    }
}
