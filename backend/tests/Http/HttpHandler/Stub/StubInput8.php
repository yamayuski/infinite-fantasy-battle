<?php

declare(strict_types=1);

/**
 * @license MIT
 */

namespace Ifb\Http\HttpHandler\Stub;

final class StubInput8
{
    /**
     * @param int $int
     * @param float $float
     * @param bool $bool
     * @param string $string
     * @param array<array-key, mixed> $array
     * @param object $object
     * @param mixed $mixed
     * @return void
     */
    public function __construct(
        public int $int,
        public float $float,
        public bool $bool,
        public string $string,
        public array $array,
        public object $object,
        public mixed $mixed,
    ) {}
}
