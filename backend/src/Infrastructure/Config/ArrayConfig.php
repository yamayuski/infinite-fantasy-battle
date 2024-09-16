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

    private function getValue(string $key): mixed
    {
        if (!\array_key_exists($key, $this->config)) {
            throw new ConfigNotFoundException($key);
        }
        return $this->config[$key];
    }

    public function getArray(string $key): array
    {
        $value = $this->getValue($key);
        if (!\is_array($value)) {
            throw new InvalidConfigException(\sprintf('"%s" is not array, got "%s"', $key, \gettype($value)));
        }
        return $value;
    }

    public function getString(string $key): string
    {
        $value = $this->getValue($key);
        if (!\is_string($value)) {
            throw new InvalidConfigException(\sprintf('"%s" is not string, got "%s"', $key, \gettype($value)));
        }
        return $value;
    }

    public function getStringArray(string $key): array
    {
        $value = $this->getArray($key);
        $result = [];
        foreach ($value as $v) {
            if (!\is_string($v)) {
                throw new InvalidConfigException(\sprintf('"%s" is not string, got "%s"', $key, \gettype($v)));
            }
            $result[] = $v;
        }
        return $result;
    }

    public function getNonEmptyString(string $key): string
    {
        $value = $this->getString($key);
        if ($value === '') {
            throw new InvalidConfigException(\sprintf('"%s" is empty', $key));
        }
        return $value;
    }

    public function getNonEmptyStringArray(string $key): array
    {
        $value = $this->getStringArray($key);
        $result = [];
        foreach ($value as $v) {
            if ($v === '') {
                throw new InvalidConfigException(\sprintf('"%s" has empty value', $key));
            }
            $result[] = $v;
        }
        return $result;
    }

    public function getBoolean(string $key): bool
    {
        $value = $this->getValue($key);
        if (!\is_bool($value)) {
            throw new InvalidConfigException(\sprintf('"%s" is not boolean, got "%s"', $key, \gettype($value)));
        }
        return $value;
    }

    public function getInteger(string $key): int
    {
        $value = $this->getValue($key);
        if (!\is_int($value)) {
            throw new InvalidConfigException(\sprintf('"%s" is not integer, got "%s"', $key, \gettype($value)));
        }
        return $value;
    }
}
