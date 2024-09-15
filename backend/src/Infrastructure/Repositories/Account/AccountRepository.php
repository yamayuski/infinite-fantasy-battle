<?php

declare(strict_types=1);

/**
 * @author Masaru Yamagishi <m.yamagishi90+git@gmail.com>
 * @license MIT
 */

namespace Ifb\Infrastructure\Repositories\Account;

use Ifb\Domain\Account\AccountEntity;
use Ifb\Domain\Account\AccountRepositoryInterface;
use Ifb\Domain\Account\RawLoginToken;
use Ifb\Domain\Identity\Identity;

class AccountRepository implements AccountRepositoryInterface
{
    public function exists(string $email): bool
    {
        return 0 === \strcmp($email, 'dummy@ifb.test');
    }

    public function insert(string $email, RawLoginToken $token): AccountEntity
    {
        /** @var Identity<AccountEntity> $id */
        $id = new Identity('dummy-id');
        $hash = $token->getHash();
        return new AccountEntity($id, $hash);
    }

    public function findOrNull(string $email): ?AccountEntity
    {
        if ($this->exists($email)) {
            /** @var Identity<AccountEntity> $id */
            $id = new Identity('dummy-id');
            $hash = \password_hash('dummy', \PASSWORD_DEFAULT);
            return new AccountEntity($id, $hash);
        }
        return null;
    }

    public function update(AccountEntity $entity): void
    {
        // TODO
    }
}
