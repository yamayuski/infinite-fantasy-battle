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
 * Accessory master data
 * @package Ifb\Domain\User
 */
#[Entity(role: 'master_accessory', table: 'mst_accessories')]
class MasterAccessory implements JsonSerializable
{
    /**
     * @param MasterId<MasterAccessory> $id アクセサリーマスターID
     * @param string $name アクセサリー名称
     * @param string $description フレーバーテキスト
     * @param LargeNumber $attack 基礎攻撃力
     * @param LargeNumber $defense 基礎防御力
     * @param float $critial_rate 基礎クリティカル率(%)
     * @param LargeNumber $critical_multiplier 基礎クリティカル時のダメージ倍率(%)
     * @param float $xp_bonus_rate XP ボーナス率(%)
     * @param LargeNumber $capacity 潜在価値
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
        #[Column(type: 'string', typecast: [LargeNumber::class, 'createFromString'])]
        private LargeNumber $defense,
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
     * @return MasterId<MasterAccessory>
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

    public function getDefense(): LargeNumber
    {
        return $this->defense;
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
            'defense' => $this->defense->toHumanReadableString(),
            'critial_rate' => $this->critial_rate,
            'critical_multiplier' => $this->critical_multiplier->toHumanReadableString(),
            'xp_bonus_rate' => $this->xp_bonus_rate,
            'capacity' => $this->capacity->toHumanReadableString(),
        ];
    }
}
