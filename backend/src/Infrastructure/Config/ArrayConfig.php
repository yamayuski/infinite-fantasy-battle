<?php

declare(strict_types=1);

/**
 * @author Masaru Yamagishi <m.yamagishi90+git@gmail.com>
 * @license MIT
 */

namespace Ifb\Infrastructure\Config;

final readonly class ArrayConfig implements ConfigInterface
{
    /**
     * @param array<array-key, mixed> $config
     */
    public function __construct(
        private array $config,
    ) {}

    public function getArray(string $key): array
    {
        if (!\array_key_exists($key, $this->config)) {
            throw new ConfigNotFoundException($key);
        }
        $value = $this->config[$key];
        if (!\is_array($value)) {
            throw new InvalidConfigException(\sprintf('"%s" is not array, got "%s"', $key, \gettype($value)));
        }
        return $value;
    }
}
