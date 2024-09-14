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
     */
    public function getArray(string $key): array;
}
