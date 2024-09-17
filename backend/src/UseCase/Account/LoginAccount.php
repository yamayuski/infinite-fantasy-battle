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
use Ifb\Domain\Account\AccountEntity;
use Ifb\Domain\Account\AccountNotFoundException;
use Ifb\Domain\Account\LoginToken;
use Ifb\Domain\Account\RawPassword;
use Psr\Log\LoggerInterface;

final readonly class LoginAccount
{
    /**
     * @param LoggerInterface $logger
     */
    public function __construct(
        private LoggerInterface $logger,
        private ORMInterface $orm,
    ) {}

    public function __invoke(string $email, RawPassword $token): LoginToken
    {
        return $this->orm
            ->getSource(AccountEntity::class)
            ->getDatabase()
            ->transaction(function () use ($email, $token): LoginToken {
                // @phpstan-ignore argument.templateType
                $repo = $this->orm->getRepository(AccountEntity::class);
                \assert($repo instanceof Repository);
                $account = $repo->forUpdate()->select()->where('email', $email)->fetchOne();
                \assert(\is_null($account) || $account instanceof AccountEntity);
                if (\is_null($account) || !$account->verifyPassword($token->token)) {
                    $this->logger->debug('not found', compact('email', 'account', 'token'));
                    throw new AccountNotFoundException($email);
                }

                $this->logger->info('User logged in', compact('email'));

                if ($token->needsRehash($account)) {
                    $account->updatePassword($token);
                    $this->logger->info('User password rehashed', compact('email'));
                }

                $login_token = LoginToken::generateNew();
                $account->updateToken($login_token);

                $em = new EntityManager($this->orm);
                $em->persist($account);
                $em->run();

                return $login_token;
            });
    }
}
