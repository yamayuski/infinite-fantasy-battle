<?php

declare(strict_types=1);

/**
 * @author Masaru Yamagishi <akai_inu@live.jp>
 * @license MIT
 */

namespace Ifb\Http\ValidatedRouter;

use Psr\Http\Message\ServerRequestInterface;

interface ValidatedRequestInterface
{
    public function getServerRequest(): ServerRequestInterface;

    public function setServerRequest(ServerRequestInterface $server_request): void;

    public function getInteger(string $key): int;

    public function getFloat(string $key): float;

    public function getString(string $key): string;

    public function getBoolean(string $key): bool;

    /**
     * Get array value
     * @param string $key
     * @return array<array-key, mixed>
     */
    public function getArray(string $key): array;

    /**
     * Get integer array value
     * @param string $key
     * @return int[]
     */
    public function getIntegerArray(string $key): array;

    /**
     * Get float array value
     * @param string $key
     * @return float[]
     */
    public function getFloatArray(string $key): array;

    /**
     * Get string array value
     * @param string $key
     * @return string[]
     */
    public function getStringArray(string $key): array;

    /**
     * Get boolean array value
     * @param string $key
     * @return bool[]
     */
    public function getBooleanArray(string $key): array;
}
