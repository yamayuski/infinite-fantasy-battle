<?php

declare(strict_types=1);

/**
 * @author Masaru Yamagishi <m.yamagishi90+git@gmail.com>
 * @license MIT
 */

namespace Ifb\Domain\Account;

use Ifb\Domain\Identity\Identity;
use SensitiveParameter;

class AccountEntity
{
    /**
     * @param Identity<AccountEntity> $id
     */
    public function __construct(
        public readonly Identity $id,
        #[SensitiveParameter]
        private string $hashed_token,
    ) {}

    public function verifyPassword(
        #[SensitiveParameter]
        string $password,
    ): bool {
        return \password_verify($password, $this->hashed_token);
    }

    public function getHashedPassword(): string
    {
        return $this->hashed_token;
    }

    public function updatePassword(RawLoginToken $token): void
    {
        $this->hashed_token = $token->getHash();
    }
}
