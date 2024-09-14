<?php

declare(strict_types=1);

/**
 * @author Masaru Yamagishi <m.yamagishi90+git@gmail.com>
 * @license MIT
 */

namespace Ifb\Infrastructure\Config;

class EnvConfig implements ConfigInterface
{
    /**
     * Get JSON format value as array
     * @param string $key
     * @return array<array-key, mixed>
     * @throws InvalidConfigException
     * @throws ConfigNotFoundException
     */
    public function getArray(string $key): array
    {
        $value = \getenv($key);
        if (!\is_string($value)) {
            throw new ConfigNotFoundException($key);
        }
        $decoded = @\json_decode($value, true);
        if (!\is_array($decoded)) {
            throw new InvalidConfigException(\sprintf('"%s" is invalid JSON format', $key));
        }
        return $decoded;
    }
}
