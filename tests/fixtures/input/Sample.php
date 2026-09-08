<?php

namespace Phalcon\CodeQuality\Tests\Fixture;

use RuntimeException;
use LogicException;
use ArrayObject;

class Sample
{
    private function build(): ArrayObject
    {
        $items = [
            'alpha' => 1,
            'longer_key' => 2,
        ];

        return new ArrayObject($items);
    }

    public const BETA = 'beta';

    protected string $label = 'sample';

    public const ALPHA = 'alpha';

    public function run(): string
    {
        $name = 'phalcon';
        $description = 'code quality';

        if ('' === $name) {
            throw new RuntimeException('empty');
        }

        return $name . ' ' . $description;
    }
}
