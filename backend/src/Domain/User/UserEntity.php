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
use Cycle\Annotated\Annotation\Relation\HasMany;
use Ifb\Domain\Account\AccountEntity;
use Ifb\Domain\Identity\Identity;
use Ifb\Domain\LargeNumber;
use JsonSerializable;

#[Entity(role: 'user', table: 'tbl_users')]
class UserEntity implements JsonSerializable
{
    #[BelongsTo(target: AccountEntity::class, innerKey: 'account_id', outerKey: 'id')]
    private ?AccountEntity $account = null;

    /** @var UserWeapon[] $weapons  */
    #[HasMany(target: UserWeapon::class, innerKey: 'account_id', outerKey: 'account_id')]
    private array $weapons = [];

    /** @var UserArmor[] $armors */
    #[HasMany(target: UserArmor::class, innerKey: 'account_id', outerKey: 'account_id')]
    private array $armors = [];

    /** @var UserAccessory[] $accessories */
    #[HasMany(target: UserAccessory::class, innerKey: 'account_id', outerKey: 'account_id')]
    private array $accessories = [];

    /**
     * @param Identity<AccountEntity> $account_id
     * @param string $display_name 他のユーザーにも見える表示名
     * @param LargeNumber $level レベル
     * @param LargeNumber $exp 経験値
     * @param LargeNumber $money 所持金
     * @param LargeNumber $hp 現在の HP
     * @param LargeNumber $max_hp 最大 HP
     * @param int $strength 腕力
     * @param int $vitality 持久力
     * @param int $agility 素早さ
     * @param int $dexterity 器用さ
     */
    public function __construct(
        #[Column(type: 'string', primary: true, typecast: [Identity::class, 'castValue'])]
        private Identity $account_id,
        #[Column(type: 'string')]
        private string $display_name,
        #[Column(type: 'string', typecast: [LargeNumber::class, 'createFromString'])]
        private LargeNumber $level,
        #[Column(type: 'string', typecast: [LargeNumber::class, 'createFromString'])]
        private LargeNumber $exp,
        #[Column(type: 'string', typecast: [LargeNumber::class, 'createFromString'])]
        private LargeNumber $money,
        #[Column(type: 'string', typecast: [LargeNumber::class, 'createFromString'])]
        private LargeNumber $hp,
        #[Column(type: 'string', typecast: [LargeNumber::class, 'createFromString'])]
        private LargeNumber $max_hp,
        #[Column(type: 'integer')]
        private int $strength,
        #[Column(type: 'integer')]
        private int $vitality,
        #[Column(type: 'integer')]
        private int $agility,
        #[Column(type: 'integer')]
        private int $dexterity,
    ) {}

    public function getAccount(): ?AccountEntity
    {
        return $this->account;
    }

    public function setAccount(?AccountEntity $account): void
    {
        $this->account = $account;
    }

    /**
     * @return null|UserWeapon[]
     */
    public function getUserWeapons(): ?array
    {
        return $this->weapons;
    }

    /**
     * @param UserWeapon[] $weapons
     * @return void
     */
    public function setUserWeapons(array $weapons): void
    {
        $this->weapons = $weapons;
    }

    /**
     * @return UserArmor[]
     */
    public function getUserArmors(): array
    {
        return $this->armors;
    }

    /**
     * @param UserArmor[] $armors
     * @return void
     */
    public function setUserArmors(array $armors): void
    {
        $this->armors = $armors;
    }

    /**
     * @return UserAccessory[]
     */
    public function getUserAccessories(): ?array
    {
        return $this->accessories;
    }

    /**
     * @param UserAccessory[] $accessories
     * @return void
     */
    public function setUserAccessories(array $accessories): void
    {
        $this->accessories = $accessories;
    }

    public function getDisplayName(): string
    {
        return $this->display_name;
    }

    public function getLevel(): LargeNumber
    {
        return $this->level;
    }

    public function getExp(): LargeNumber
    {
        return $this->exp;
    }

    public function getMoney(): LargeNumber
    {
        return $this->money;
    }

    public function getHp(): LargeNumber
    {
        return $this->hp;
    }

    public function getMaxHp(): LargeNumber
    {
        return $this->max_hp;
    }

    public function getStrength(): int
    {
        return $this->strength;
    }

    public function getVitality(): int
    {
        return $this->vitality;
    }

    public function getAgility(): int
    {
        return $this->agility;
    }

    public function getDexterity(): int
    {
        return $this->dexterity;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'account_id' => $this->account_id->id,
            'display_name' => $this->display_name,
            'level' => $this->level->toHumanReadableString(),
            'exp' => $this->exp->toHumanReadableString(),
            'money' => $this->money->toHumanReadableString(),
            'hp' => $this->hp->toHumanReadableString(),
            'max_hp' => $this->max_hp->toHumanReadableString(),
            'strength' => $this->strength,
            'vitality' => $this->vitality,
            'agility' => $this->agility,
            'dexterity' => $this->dexterity,
        ];
    }
}
