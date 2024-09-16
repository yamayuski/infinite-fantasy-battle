<?php

declare(strict_types=1);

/**
 * @author Masaru Yamagishi <m.yamagishi90+git@gmail.com>
 * @license MIT
 */

namespace Ifb\UseCase\Account;

use Cycle\ORM\EntityManager;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\Select\Repository;
use Ifb\Domain\Account\AccountAlreadyExistsException;
use Ifb\Domain\Account\AccountEntity;
use Ifb\Domain\Account\RawLoginToken;
use Ifb\Domain\Identity\Identity;
use Psr\Log\LoggerInterface;

final readonly class RegisterAccount
{
    /**
     * @param LoggerInterface $logger
     * @param ORMInterface $orm
     */
    public function __construct(
        private LoggerInterface $logger,
        private ORMInterface $orm,
    ) {}

    public function __invoke(string $email): RawLoginToken
    {
        return $this->orm
            ->getSource(AccountEntity::class)
            ->getDatabase()
            ->transaction(function () use ($email): RawLoginToken {
                // @phpstan-ignore argument.templateType
                $repo = $this->orm->getRepository(AccountEntity::class);
                \assert($repo instanceof Repository);
                $result = $repo->select()->where('email', $email)->fetchOne();
                if (!\is_null($result)) {
                    throw new AccountAlreadyExistsException($email);
                }
                $token = RawLoginToken::generateNew();
                /** @var Identity<AccountEntity> $id */
                $id = Identity::create();
                $account = new AccountEntity(
                    $id,
                    $email,
                    $token->getHash(),
                );
                $em = new EntityManager($this->orm);
                $em->persist($account);
                $em->run();

                $this->logger->info('New user has registered', compact('email'));

                return $token;
            });
    }
}
