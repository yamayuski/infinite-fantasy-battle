<?php

declare(strict_types=1);

/**
 * @author Masaru Yamagishi <m.yamagishi90+git@gmail.com>
 * @license MIT
 */

namespace Ifb\UseCase\Account;

use Ifb\Domain\Account\AccountNotFoundException;
use Ifb\Domain\Account\AccountRepositoryInterface;
use Ifb\Domain\Account\RawLoginToken;
use Ifb\Domain\Database\TransactionInterface;
use Psr\Log\LoggerInterface;

final readonly class LoginAccount
{
    /**
     * @param TransactionInterface<RawLoginToken> $tx
     * @param LoggerInterface $logger
     * @param AccountRepositoryInterface $repo
     */
    public function __construct(
        private TransactionInterface $tx,
        private LoggerInterface $logger,
        private AccountRepositoryInterface $repo,
    ) {}

    public function __invoke(string $email, RawLoginToken $token): RawLoginToken
    {
        return ($this->tx)(function () use ($email, $token): RawLoginToken {
            $account = $this->repo->findOrNull($email);
            if (\is_null($account) || !$account->verifyPassword($token->token)) {
                throw new AccountNotFoundException($email);
            }

            $this->logger->info('User logged in', compact('email'));

            if ($token->needsRehash($account)) {
                $new_token = RawLoginToken::generateNew();
                $account->updatePassword($new_token);
                $this->repo->update($account);
                $this->logger->info('User password was updated', compact('email'));
                return $new_token;
            }

            return $token;
        });
    }
}
