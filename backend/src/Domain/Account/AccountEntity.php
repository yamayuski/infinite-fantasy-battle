<?php

declare(strict_types=1);

/**
 * @author Masaru Yamagishi <m.yamagishi90+git@gmail.com>
 * @license MIT
 */

namespace Ifb\Domain\Account;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\HasOne;
use Cycle\Annotated\Annotation\Table\Index;
use Ifb\Domain\Identity\Identity;
use Ifb\Domain\User\UserEntity;
use JsonSerializable;
use SensitiveParameter;

#[Entity(role: 'account', table: 'tbl_accounts')]
#[Index(['email'], unique: true)]
#[Index(['token'], unique: true)]
class AccountEntity implements JsonSerializable
{
    #[HasOne(target: UserEntity::class, innerKey: 'id', outerKey: 'account_id')]
    private ?UserEntity $user = null;

    /**
     * @param Identity<AccountEntity> $id
     * @param string $email
     * @param string $hashed_password
     * @param LoginToken|null $token
     */
    public function __construct(
        #[Column(type: 'string', primary: true, typecast: [Identity::class, 'castValue'])]
        private Identity $id,
        #[Column(type: 'string')]
        public string $email,
        #[Column(type: 'string')]
        #[SensitiveParameter]
        private string $hashed_password,
        #[Column(type: 'string', nullable: true, typecast: [LoginToken::class, 'castValue'])]
        private ?LoginToken $token = null,
    ) {}

    public function getUser(): ?UserEntity
    {
        return $this->user;
    }

    public function setUser(?UserEntity $user): void
    {
        $this->user = $user;
    }

    /**
     * Get ID
     * @return Identity<AccountEntity>
     */
    public function getId(): Identity
    {
        return $this->id;
    }

    public function verifyPassword(
        #[SensitiveParameter]
        string $password,
    ): bool {
        return \password_verify($password, $this->hashed_password);
    }

    public function getHashedPassword(): string
    {
        return $this->hashed_password;
    }

    public function updatePassword(RawPassword $token): void
    {
        $this->hashed_password = $token->getHash();
    }

    public function updateToken(LoginToken $token): void
    {
        $this->token = $token;
    }

    public function getToken(): ?LoginToken
    {
        return $this->token;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
        ];
    }
}
