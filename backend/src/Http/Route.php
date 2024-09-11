<?php

declare(strict_types=1);

/**
 * @author Masaru Yamagishi <akai_inu@live.jp>
 * @license MIT
 */

namespace Ifb\Http;

use JsonSerializable;

/**
 * Route information
 */
final class Route implements JsonSerializable
{
    /** @var array<string, string> $path_attributes */
    private array $path_attributes = [];

    /**
     * Constructor
     * @param string $method
     * @param string $path
     * @param class-string $handler
     * @param array<int, class-string> $middlewares
     */
    public function __construct(
        public readonly string $method,
        public readonly string $path,
        public readonly string $handler,
        public readonly array $middlewares = [],
    ) {}

    /**
     * Set path attributes
     * @param string $key
     * @param string $value
     * @return void
     */
    public function setPathAttribute(string $key, string $value): void
    {
        $this->path_attributes[$key] = $value;
    }

    /**
     * Get path attribute
     * @param string $key
     * @return ?string
     */
    public function getPathAttribute(string $key): ?string
    {
        if (\array_key_exists($key, $this->path_attributes)) {
            return $this->path_attributes[$key];
        }
        return null;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'method' => $this->method,
            'path' => $this->path,
            'handler' => $this->handler,
            'middlewares' => $this->middlewares,
            'path_attributes' => $this->path_attributes,
        ];
    }
}
