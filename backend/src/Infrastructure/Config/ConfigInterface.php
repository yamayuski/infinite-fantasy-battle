<?php

declare(strict_types=1);

/**
 * @author Masaru Yamagishi <m.yamagishi90+git@gmail.com>
 * @license MIT
 */

namespace Ifb\Infrastructure\Config;

interface ConfigInterface
{
    /**
     * Get value as array
     * @param string $key
     * @return array<array-key, mixed>
     * @throws InvalidConfigException
     * @throws ConfigNotFoundException
     */
    public function getArray(string $key): array;

    /**
     * Get value as string
     * @param string $key
     * @return string
     * @throws InvalidConfigException
     * @throws ConfigNotFoundException
     */
    public function getString(string $key): string;

    /**
     * Get value as string[]
     * @param string $key
     * @return string[]
     * @throws InvalidConfigException
     * @throws ConfigNotFoundException
     */
    public function getStringArray(string $key): array;

    /**
     * Get value as non-empty-string
     * @param string $key
     * @return non-empty-string
     * @throws InvalidConfigException
     * @throws ConfigNotFoundException
     */
    public function getNonEmptyString(string $key): string;

    /**
     * Get value as non-empty-string[]
     * @param string $key
     * @return list<non-empty-string>
     */
    public function getNonEmptyStringArray(string $key): array;

    /**
     * Get value as boolean
     * @param string $key
     * @return bool
     * @throws InvalidConfigException
     * @throws ConfigNotFoundException
     */
    public function getBoolean(string $key): bool;

    /**
     * Get value as integer
     * @param string $key
     * @return int
     * @throws InvalidConfigException
     * @throws ConfigNotFoundException
     */
    public function getInteger(string $key): int;
}
