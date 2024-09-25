<?php

declare(strict_types=1);

/**
 * @author Masaru Yamagishi <m.yamagishi90+git@gmail.com>
 * @license MIT
 */

namespace Ifb\Domain\User;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
use Ifb\Domain\Identity\Identity;
use Ifb\Domain\LargeNumber;
use JsonSerializable;

#[Entity(role: 'user_weapon', table: 'tbl_user_weapons')]
class UserWeapon implements JsonSerializable
{
    #[BelongsTo(target: MasterWeapon::class, innerKey: 'master_id', outerKey: 'id')]
    private ?MasterWeapon $master = null;

    #[BelongsTo(target: UserEntity::class, innerKey: 'account_id', outerKey: 'id')]
    private ?UserEntity $user = null;

    /**
     * @param Identity<UserWeapon> $id
     * @param LargeNumber $xp
     */
    public function __construct(
        #[Column(type: 'string', primary: true, typecast: [Identity::class, 'castValue'])]
        private Identity $id,
        #[Column(type: 'string', typecast: [LargeNumber::class, 'createFromString'])]
        private LargeNumber $xp,
    ) {}

    public function getMaster(): ?MasterWeapon
    {
        return $this->master;
    }

    public function setMaster(?MasterWeapon $master): void
    {
        $this->master = $master;
    }

    public function getUser(): ?UserEntity
    {
        return $this->user;
    }

    public function setUser(?UserEntity $user): void
    {
        $this->user = $user;
    }

    /**
     * @return Identity<UserWeapon>
     */
    public function getId(): Identity
    {
        return $this->id;
    }

    public function getXp(): LargeNumber
    {
        return $this->xp;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'master' => $this->master,
            'user' => $this->user,
            'xp' => $this->xp->toHumanReadableString(),
        ];
    }
}
