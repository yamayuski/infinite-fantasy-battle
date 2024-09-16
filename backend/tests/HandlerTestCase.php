<?php

declare(strict_types=1);

/**
 * @license MIT
 */

namespace Ifb;

use Cycle\Database\DatabaseManager;
use Cycle\ORM\ORMInterface;
use Ifb\Infrastructure\Config\ArrayConfig;
use LogicException;
use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Shibare\Log\Logger;

abstract class HandlerTestCase extends BaseTestCase
{
    protected ?Kernel $kernel = null;

    #[Before]
    public function bootContainer(): void
    {
        $array = require __DIR__ . '/../config.php';
        if (!\is_array($array)) {
            throw new LogicException('Invalid config.php');
        }
        $config = new ArrayConfig($array);
        $kernel = new Kernel($config);
        $kernel->boot();
        $this->kernel = $kernel;
    }

    /**
     * Seed to database
     * @param non-empty-string $table table name
     * @param array<array-key, mixed> $records record or record list
     * @param ?string $database database name
     * @return void
     */
    public function seed(string $table, array $records, ?string $database = null): void
    {
        $container = $this->kernel?->getContainer();
        if (\is_null($container)) {
            throw new LogicException('Container not set');
        }
        $dbal = $container->get(DatabaseManager::class);
        if ($dbal instanceof DatabaseManager === false) {
            throw new LogicException('DatabaseManager not set');
        }
        $dbal
            ->database($database)
            ->insert($table)
            ->values($records)
            ->run();
    }

    /**
     * Handle input to output
     * @param object $input
     * @param class-string $handler_name
     * @return object
     */
    public function handle(object $input, string $handler_name): object
    {
        $container = $this->kernel?->getContainer();
        if (\is_null($container)) {
            throw new LogicException('Container not set');
        }
        $handler = $container->get($handler_name);
        if (!\is_callable($handler)) {
            throw new LogicException(\sprintf('Handler "%s" is not callable', $handler_name));
        }

        return $handler($input);
    }

    #[After]
    public function clearLoggerSingleton(): void
    {
        Logger::clearInstance();
        $this->kernel = null;
    }
}
