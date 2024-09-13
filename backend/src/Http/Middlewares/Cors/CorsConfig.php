<?php

declare(strict_types=1);

/**
 * @license MIT
 */

namespace Ifb\Http\Middlewares\Cors;

use InvalidArgumentException;

/**
 * @package Ifb\Http\Middlewares\Cors
 */
final class CorsConfig
{
    /**
     * Constructor
     * @param non-empty-string $server_origin
     * @param non-empty-string[] $allow_origin
     * @param non-empty-string[] $allow_methods
     * @param non-empty-string[] $allow_headers
     * @param non-empty-string[] $expose_headers
     * @param non-empty-string[] $vary
     * @param bool $allow_credentials
     * @param int $max_age
     * @return void
     */
    public function __construct(
        public readonly string $server_origin,
        public readonly array $allow_origin,
        public readonly array $allow_methods = ['GET', 'POST'],
        public readonly array $allow_headers = [],
        public readonly array $expose_headers = [],
        public readonly array $vary = ['Origin'],
        public readonly bool $allow_credentials = false,
        public readonly int $max_age = 5,
    ) {
        if (\count($this->allow_origin) === 0) {
            throw new InvalidArgumentException('Allow origin cannot be empty');
        }
        if (\count($this->allow_methods) === 0) {
            throw new InvalidArgumentException('Allow methods cannot be empty');
        }
        if (\count($this->vary) === 0) {
            throw new InvalidArgumentException('Vary cannot be empty');
        }
        if (\count($this->allow_origin) > 1 && \in_array('*', $this->allow_origin, true)) {
            throw new InvalidArgumentException('Allow origin cannot contain "*" when multiple origins are provided');
        }
        if (\count($this->allow_headers) > 1 && \in_array('*', $this->allow_headers, true)) {
            throw new InvalidArgumentException('Allow headers cannot contain "*" when multiple headers are provided');
        }
        if (\count($this->allow_methods) > 1 && \in_array('*', $this->allow_methods, true)) {
            throw new InvalidArgumentException('Allow methods cannot contain "*" when multiple methods are provided');
        }
        if ($this->allow_credentials) {
            if (\count($this->allow_origin) === 1 && $this->allow_origin[0] === '*') {
                throw new InvalidArgumentException('Allow origin cannot be "*" when allow credentials is true');
            }
            if (\count($this->allow_headers) === 1 && $this->allow_headers[0] === '*') {
                throw new InvalidArgumentException('Allow headers cannot be "*" when allow credentials is true');
            }
            if (\count($this->allow_methods) === 1 && $this->allow_methods[0] === '*') {
                throw new InvalidArgumentException('Allow methods cannot be "*" when allow credentials is true');
            }
        }
    }
}
