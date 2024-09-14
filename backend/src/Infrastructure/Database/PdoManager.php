<?php

declare(strict_types=1);

/**
 * @author Masaru Yamagishi <m.yamagishi90+git@gmail.com>
 * @license MIT
 */

namespace Ifb\Infrastructure\Database;

use PDO;

class PdoManager
{
    public function __construct(
        private readonly DatabaseConfig $config,
    ) {}

    public function connect(?string $db_name = null): PDO
    {
        if (\is_null($db_name)) {
            $db_name = $this->config->getDefaultDatabaseName();
        }
        $driver = $this->config->getDriverName($db_name);
        $dsn = $this->config->getDsn($db_name);
        $username = $this->config->getUsername($db_name);
        $password = $this->config->getPassword($db_name);

        return new PDO(
            \sprintf('%s:%s', $driver, $dsn),
            $username,
            $password,
            // TODO: options
        );
    }
}
