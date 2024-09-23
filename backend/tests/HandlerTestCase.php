<?php

declare(strict_types=1);

/**
 * @license MIT
 */

namespace Ifb;

use Cycle\Database\DatabaseManager;
use Ifb\Domain\Account\AccountEntity;
use Ifb\Domain\Account\LoginToken;
use Ifb\Domain\Identity\Identity;
use Ifb\Infrastructure\Config\ArrayConfig;
use LogicException;
use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Psr\Http\Message\ServerRequestInterface;
use Ramsey\Uuid\Uuid;
use Shibare\Contracts\HttpServer\ServerRequestAwareInterface;
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
     * Login as user
     * @param object $input
     * @param null|AccountEntity $entity
     * @return AccountEntity
     */
    public function actAsUser(object $input, ?AccountEntity $entity = null): AccountEntity
    {
        if ($input instanceof ServerRequestAwareInterface === false) {
            throw new LogicException('Input class does not implements ServerRequestAwareInterface');
        }
        if (\is_null($entity)) {
            /** @var Identity<AccountEntity> $id */
            $id = new Identity(Uuid::uuid4());
            $entity = new AccountEntity(
                $id,
                'test@ifb.test',
                \password_hash('testtest', \PASSWORD_DEFAULT),
                new LoginToken('testtoken'),
            );
        }
        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects(self::once())
            ->method('getAttribute')
            ->with(AccountEntity::class)
            ->willReturn($entity);

        $input->setServerRequest($request);

        $this->seed('accounts', [
            'id' => $entity->getId()->__toString(),
            'email' => $entity->email,
            'hashed_password' => $entity->getHashedPassword(),
            'token' => $entity->getToken()?->token,
        ]);

        return $entity;
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
