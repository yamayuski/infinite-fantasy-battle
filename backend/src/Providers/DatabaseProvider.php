<?php

declare(strict_types=1);

/**
 * @author Masaru Yamagishi <m.yamagishi90+git@gmail.com>
 * @license MIT
 */

namespace Ifb\Providers;

use Cycle\Annotated\Locator\TokenizerEntityLocator;
use Cycle\Database\Config\DatabaseConfig;
use Cycle\Database\Config\SQLite\FileConnectionConfig;
use Cycle\Database\Config\SQLite\MemoryConnectionConfig;
use Cycle\Database\Config\SQLiteDriverConfig;
use Cycle\Database\DatabaseManager;
use Cycle\ORM\Factory;
use Cycle\ORM\ORM;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\Schema;
use Cycle\Schema\Compiler;
use Cycle\Schema\Registry;
use Ifb\Infrastructure\Config\ConfigInterface;
use Ifb\Infrastructure\Config\ConfigNotFoundException;
use Ifb\Infrastructure\Config\InvalidConfigException;
use Ifb\Infrastructure\ProviderInterface;
use Psr\Log\LoggerInterface;
use Shibare\Contracts\Container;
use Spiral\Tokenizer\ClassLocator;
use Symfony\Component\Finder\Finder;

class DatabaseProvider implements ProviderInterface
{
    public function provide(Container $container, ConfigInterface $config): void
    {
        $dbal = new DatabaseManager(
            new DatabaseConfig([
                'default' => $config->getNonEmptyString('databases.default'),
                'databases' => $config->getArray('databases.databases'),
                'connections' => $this->buildConnections($config),
            ]),
        );
        $dbal->setLogger($container->get(LoggerInterface::class));
        $container->bind(DatabaseManager::class, $dbal);
        $entity_locator = new TokenizerEntityLocator(new ClassLocator((new Finder())->files()->in(__DIR__ . '/../')));
        $schema = (new Compiler())->compile(
            new Registry($dbal),
            [
                new \Cycle\Schema\Generator\ResetTables(),
                new \Cycle\Annotated\Entities($entity_locator),
                new \Cycle\Annotated\MergeColumns(),
                new \Cycle\Schema\Generator\GenerateRelations(),
                new \Cycle\Schema\Generator\GenerateModifiers(),
                new \Cycle\Schema\Generator\ValidateEntities(),
                new \Cycle\Schema\Generator\RenderTables(),
                new \Cycle\Schema\Generator\RenderRelations(),
                new \Cycle\Schema\Generator\RenderModifiers(),
                new \Cycle\Annotated\MergeIndexes(),
                new \Cycle\Schema\Generator\SyncTables(),
                new \Cycle\Schema\Generator\GenerateTypecast(),
            ],
        );
        $orm = new ORM(new Factory($dbal), new Schema($schema));
        $container->bind(ORMInterface::class, $orm);
    }

    /**
     * @param ConfigInterface $config
     * @return array<string, mixed>
     * @throws InvalidConfigException
     * @throws ConfigNotFoundException
     */
    private function buildConnections(ConfigInterface $config): array
    {
        $connections = [];
        $value = $config->getArray('databases.connections');
        foreach ($value as $name => $v) {
            if (!\is_array($v)) {
                throw new InvalidConfigException('databases.connections is not array');
            }
            if (!\array_key_exists('driver', $v) || !\array_key_exists('connection', $v)) {
                throw new InvalidConfigException('Invalid databases.connections config');
            }
            if (!\is_string($v['driver']) || !\is_string($v['connection'])) {
                throw new InvalidConfigException('Invalid databases.connections config');
            }
            $connections[$name] = match ($v['driver']) {
                'sqlite' => new SQLiteDriverConfig(
                    connection: match ($v['connection']) {
                        'memory' => new MemoryConnectionConfig(),
                        'file' => new FileConnectionConfig($v['database'] ?? ''),
                        default => throw new InvalidConfigException('Invalid databases.connections config'),
                    },
                    timezone: $v['timezone'] ?? 'UTC',
                ),
                default => throw new InvalidConfigException('Invalid databases.connections config'),
            };
        }
        return $connections;
    }
}
