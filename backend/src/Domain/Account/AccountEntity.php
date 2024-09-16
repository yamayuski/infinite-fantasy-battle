<?php

declare(strict_types=1);

/**
 * @author Masaru Yamagishi <m.yamagishi90+git@gmail.com>
 * @license MIT
 */

namespace Ifb\Domain\Account;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Table\Index;
use Ifb\Domain\Identity\Identity;
use SensitiveParameter;

#[Entity(role: 'account', table: 'accounts')]
#[Index(['email'], unique: true)]
class AccountEntity
{
    /**
     * @param Identity<AccountEntity> $id
     */
    public function __construct(
        #[Column(type: 'string', primary: true, typecast: [Identity::class, 'castValue'])]
        public readonly Identity $id,
        // @phpstan-ignore property.onlyWritten
        #[Column(type: 'string')]
        private string $email,
        #[Column(type: 'string')]
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
