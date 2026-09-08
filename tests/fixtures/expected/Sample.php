<?php

declare(strict_types=1);

namespace Phalcon\CodeQuality\Tests\Fixture;

use ArrayObject;
use RuntimeException;

class Sample
{
    public const ALPHA = 'alpha';

    public const BETA = 'beta';

    protected string $label = 'sample';

    public function run(): string
    {
        $name        = 'phalcon';
        $description = 'code quality';

        if ('' === $name) {
            throw new RuntimeException('empty');
        }

        return $name . ' ' . $description;
    }

    private function build(): ArrayObject
    {
        $items = [
            'alpha'      => 1,
            'longer_key' => 2,
        ];

        return new ArrayObject($items);
    }
}
