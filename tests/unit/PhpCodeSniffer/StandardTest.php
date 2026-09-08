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

namespace Phalcon\CodeQuality\Tests\Unit\PhpCodeSniffer;

use PHPUnit\Framework\TestCase;

/**
 * Runs phpcs with the shared `Phalcon` standard over a fixture.
 *
 * The test calls the standard by its name, not by a path. It therefore also
 * proves that the composer installer wrote the standard into the
 * `installed_paths` setting of phpcs. That setting is how a consumer project
 * reaches the standard with `<rule ref="Phalcon"/>`.
 */
final class StandardTest extends TestCase
{
    private string $fixture;

    private string $root;

    protected function setUp(): void
    {
        $this->root    = dirname(__DIR__, 3);
        $this->fixture = $this->root . '/tests/fixtures/phpcs/Violations.php';
    }

    public function testStandardKeepsUnderscoreMethodsAllowed(): void
    {
        $this->assertStringContainsString(
            'public function _underscorePrefix',
            (string) file_get_contents($this->fixture),
            'The fixture must keep a method with an underscore prefix, '
            . 'or this test proves nothing.'
        );

        $this->assertNotContains(
            'PSR2.Methods.MethodDeclaration.Underscore',
            $this->runPhpcs()
        );
    }

    public function testStandardReportsPsr12Violations(): void
    {
        $this->assertContains('PSR12.Files.OpenTag.NotAlone', $this->runPhpcs());
    }

    /**
     * @return array<int, string>
     */
    private function runPhpcs(): array
    {
        $command = escapeshellarg($this->root . '/vendor/bin/phpcs')
            . ' --standard=Phalcon'
            . ' --report=json'
            . ' ' . escapeshellarg($this->fixture)
            . ' 2>/dev/null';

        $lines  = [];
        $status = 0;

        exec($command, $lines, $status);

        $report = json_decode(implode('', $lines), true);

        $this->assertIsArray($report, 'phpcs returned no JSON report');

        $codes = [];
        foreach ($report['files'] as $file) {
            foreach ($file['messages'] as $message) {
                $codes[] = $message['source'];
            }
        }

        return $codes;
    }
}
