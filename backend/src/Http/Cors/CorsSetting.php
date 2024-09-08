<?php

declare(strict_types=1);

/**
 * @license MIT
 */

namespace Ifb\Http\Cors;

/**
 * @package Ifb\Http\Cors
 */
final class CorsSetting
{
    /**
     * Constructor
     * @param string $server_origin
     * @param string[] $allow_origin
     * @param string[] $allow_methods
     * @param string[] $allow_headers
     * @param string[] $expose_headers
     * @param string[] $vary
     * @param bool $allow_credentials
     * @param int $max_age
     * @return void
     */
    public function __construct(
        public readonly string $server_origin,
        public readonly array $allow_origin = [],
        public readonly array $allow_methods = [],
        public readonly array $allow_headers = [],
        public readonly array $expose_headers = [],
        public readonly array $vary = ['Origin'],
        public readonly bool $allow_credentials = false,
        public readonly int $max_age = 5,
    ) {
    }
}
