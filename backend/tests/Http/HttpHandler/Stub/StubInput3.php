<?php

declare(strict_types=1);

/**
 * @license MIT
 */

namespace Ifb\Http\HttpHandler\Stub;

final class StubInput3
{
    /**
     * @param ?string $extra
     * @return void
     */
    public function __construct(
        public ?string $extra,
    ) {}
}
