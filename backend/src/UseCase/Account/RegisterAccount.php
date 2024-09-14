<?php

declare(strict_types=1);

/**
 * @author Masaru Yamagishi <m.yamagishi90+git@gmail.com>
 * @license MIT
 */

namespace Ifb\UseCase\Account;

use Ifb\Domain\Account\AccountAlreadyExistsException;
use Ifb\Domain\Account\AccountRepositoryInterface;
use Ifb\Domain\Account\RawLoginToken;
use Ifb\Domain\Database\TransactionInterface;
use Psr\Log\LoggerInterface;

final readonly class RegisterAccount
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

    public function __invoke(string $email): RawLoginToken
    {
        return ($this->tx)(function () use ($email): RawLoginToken {
            if ($this->repo->exists($email)) {
                throw new AccountAlreadyExistsException($email);
            }
            $token = RawLoginToken::generateNew();
            $account = $this->repo->insert($email, $token);

            $this->logger->info('New user registered', compact('email'));

            return $token;
        });
    }
}
