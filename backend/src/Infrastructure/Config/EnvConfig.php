<?php

declare(strict_types=1);

/**
 * @author Masaru Yamagishi <m.yamagishi90+git@gmail.com>
 * @license MIT
 */

namespace Ifb\Infrastructure\Config;

class EnvConfig implements ConfigInterface
{
    private function getValue(string $key): mixed
    {
        return \getenv($key);
    }

    /**
     * Get JSON format value as array
     * @param string $key
     * @return array<array-key, mixed>
     * @throws InvalidConfigException
     * @throws ConfigNotFoundException
     */
    public function getArray(string $key): array
    {
        $value = $this->getValue($key);
        if (!\is_string($value)) {
            throw new ConfigNotFoundException($key);
        }
        $decoded = @\json_decode($value, true);
        if (!\is_array($decoded)) {
            throw new InvalidConfigException(\sprintf('"%s" is invalid JSON format', $key));
        }
        return $decoded;
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
        $value = $this->getString($key);
        if ($value !== 'true' && $value !== 'false') {
            throw new InvalidConfigException(\sprintf('"%s" is not boolean, got "%s"', $key, $value));
        }
        return (bool) $value;
    }

    public function getInteger(string $key): int
    {
        $value = $this->getString($key);
        if (!\is_numeric($value)) {
            throw new InvalidConfigException(\sprintf('"%s" is not integer, got "%s"', $key, $value));
        }
        return (int) $value;
    }
}
