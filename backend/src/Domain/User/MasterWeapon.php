<?php

declare(strict_types=1);

/**
 * @author Masaru Yamagishi <m.yamagishi90+git@gmail.com>
 * @license MIT
 */

namespace Ifb\Domain\User;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Ifb\Domain\Identity\MasterId;
use Ifb\Domain\LargeNumber;
use JsonSerializable;

/**
 * Weapon master data
 * @package Ifb\Domain\User
 */
#[Entity(role: 'master_weapon', table: 'mst_weapons')]
class MasterWeapon implements JsonSerializable
{
    /**
     * @param MasterId<MasterWeapon> $id 武器マスターID
     * @param string $name 武器名称
     * @param string $description フレーバーテキスト
     * @param LargeNumber $attack 基礎攻撃力
     * @param float $critial_rate 基礎クリティカル率(%)
     * @param LargeNumber $critical_multiplier 基礎クリティカル時のダメージ倍率(%)
     * @param float $xp_bonus_rate XP ボーナス率(%)
     * @param LargeNumber $capacity 絶対価値
     */
    public function __construct(
        #[Column(type: 'string', primary: true, typecast: [MasterId::class, 'castValue'])]
        private MasterId $id,
        #[Column(type: 'string')]
        private string $name,
        #[Column(type: 'string')]
        private string $description,
        #[Column(type: 'string', typecast: [LargeNumber::class, 'createFromString'])]
        private LargeNumber $attack,
        #[Column(type: 'double')]
        private float $critial_rate,
        #[Column(type: 'string', typecast: [LargeNumber::class, 'createFromString'])]
        private LargeNumber $critical_multiplier,
        #[Column(type: 'double')]
        private float $xp_bonus_rate,
        #[Column(type: 'string', typecast: [LargeNumber::class, 'createFromString'])]
        private LargeNumber $capacity,
    ) {}

    /**
     * @return MasterId<MasterWeapon>
     */
    public function getId(): MasterId
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getAttack(): LargeNumber
    {
        return $this->attack;
    }

    public function getCritialRate(): float
    {
        return $this->critial_rate;
    }

    public function getCriticalMultiplier(): LargeNumber
    {
        return $this->critical_multiplier;
    }

    public function getXpBonusRate(): float
    {
        return $this->xp_bonus_rate;
    }

    public function getCapacity(): LargeNumber
    {
        return $this->capacity;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id->id,
            'name' => $this->name,
            'description' => $this->description,
            'attack' => $this->attack->toHumanReadableString(),
            'critial_rate' => $this->critial_rate,
            'critical_multiplier' => $this->critical_multiplier->toHumanReadableString(),
            'xp_bonus_rate' => $this->xp_bonus_rate,
            'capacity' => $this->capacity->toHumanReadableString(),
        ];
    }
}
