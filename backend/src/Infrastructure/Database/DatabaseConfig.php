<?php

declare(strict_types=1);

/**
 * @author Masaru Yamagishi <m.yamagishi90+git@gmail.com>
 * @license MIT
 */

namespace Ifb\Infrastructure\Database;

use Ifb\Infrastructure\Config\ConfigInterface;
use Ifb\Infrastructure\Config\ConfigNotFoundException;
use Ifb\Infrastructure\Config\InvalidConfigException;

readonly class DatabaseConfig
{
    /** @var array<array-key, mixed> $databases */
    public array $databases;

    public function __construct(
        ConfigInterface $config,
    ) {
        $this->databases = $config->getArray('DATABASES');
    }

    /**
     * @param string $db_name
     * @return array<array-key, mixed>
     * @throws ConfigNotFoundException
     */
    protected function getDatabase(string $db_name): array
    {
        foreach ($this->databases as $index => $database) {
            if (!\is_array($database) || !\array_key_exists('name', $database)) {
                throw new ConfigNotFoundException(\sprintf('DATABASES[%d].name', $index));
            }
            $name = $database['name'];
            if (\is_string($name) && \strcmp($db_name, $name) === 0) {
                return $database;
            }
        }
        throw new ConfigNotFoundException(\sprintf('DATABASES[].name = %s', $db_name));
    }

    protected function getParameter(string $db_name, string $key): ?string
    {
        $database = $this->getDatabase($db_name);

        if (!\array_key_exists($key, $database)) {
            return null;
        }
        if (!\is_string($database[$key])) {
            return null;
        }
        return $database[$key];
    }

    protected function getParameterOrFail(string $db_name, string $key): string
    {
        $value = $this->getParameter($db_name, $key);

        if (\is_null($value)) {
            throw new ConfigNotFoundException(\sprintf('DATABASES[%s][%s]', $db_name, $key));
        }
        return $value;
    }

    public function getDefaultDatabaseName(): string
    {
        if (!\array_key_exists('default', $this->databases)) {
            throw new ConfigNotFoundException('DATABASES[default]');
        }
        $db_name = $this->databases['default'];
        if (!\is_string($db_name)) {
            throw new InvalidConfigException(\sprintf('DATABASES[default] expects string, got %s', \gettype($db_name)));
        }
        return $db_name;
    }

    public function getDriverName(string $db_name): string
    {
        return $this->getParameterOrFail($db_name, 'driver');
    }

    public function getDsn(string $db_name): string
    {
        return $this->getParameterOrFail($db_name, 'dsn');
    }

    public function getUsername(string $db_name): ?string
    {
        return $this->getParameter($db_name, 'username');
    }

    public function getPassword(string $db_name): ?string
    {
        return $this->getParameter($db_name, 'password');
    }

    public function getOptions(string $db_name): ?string
    {
        return $this->getParameter($db_name, 'options');
    }
}
