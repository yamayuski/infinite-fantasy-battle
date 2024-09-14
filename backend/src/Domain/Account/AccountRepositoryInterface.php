<?php

declare(strict_types=1);

/**
 * @author Masaru Yamagishi <m.yamagishi90+git@gmail.com>
 * @license MIT
 */

namespace Ifb\Domain\Account;

interface AccountRepositoryInterface
{
    /**
     * Exists or not as provided email
     * @param string $email
     * @return bool
     */
    public function exists(string $email): bool;

    /**
     * Insert new account
     * @param string $email
     * @return AccountEntity
     */
    public function insert(string $email, RawLoginToken $token): AccountEntity;

    /**
     * Find by email
     * @param string $email
     * @return null|AccountEntity
     */
    public function findOrNull(string $email): ?AccountEntity;

    /**
     * Update by id
     * @param AccountEntity $entity
     * @return void
     */
    public function update(AccountEntity $entity): void;
}
