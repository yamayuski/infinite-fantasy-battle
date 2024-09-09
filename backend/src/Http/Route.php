<?php

declare(strict_types=1);

/**
 * @author Masaru Yamagishi <akai_inu@live.jp>
 * @license Apache-2.0
 */

namespace Ifb\Http;

use Psr\Http\Server\RequestHandlerInterface;

/**
 * Route information
 */
final class Route
{
    /** @var array<string, string> $path_attributes */
    private array $path_attributes = [];

    /**
     * Constructor
     * @param string $method
     * @param string $path
     * @param RequestHandlerInterface $handler
     * @param array<int, \Psr\Http\Server\MiddlewareInterface> $middlewares
     */
    public function __construct(
        public readonly string $method,
        public readonly string $path,
        public readonly RequestHandlerInterface $handler,
        public readonly array $middlewares = [],
    ) {}

    /**
     * Set path attributes
     * @param string $key
     * @param string $value
     * @return void
     */
    public function setPathAttributes(string $key, string $value): void
    {
        $this->path_attributes[$key] = $value;
    }

    /**
     * Get path attributes
     * @return array<string, string>
     */
    public function getPathAttributes(): array
    {
        return $this->path_attributes;
    }
}
