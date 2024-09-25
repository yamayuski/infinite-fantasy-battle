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
 * Armor master data
 * @package Ifb\Domain\User
 */
#[Entity(role: 'master_armor', table: 'mst_armors')]
class MasterArmor implements JsonSerializable
{
    /**
     * @param MasterId<MasterArmor> $id 防具マスターID
     * @param string $name 防具名称
     * @param string $description フレーバーテキスト
     * @param LargeNumber $defense 基礎防御力
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
        private LargeNumber $defense,
        #[Column(type: 'double')]
        private float $xp_bonus_rate,
        #[Column(type: 'string', typecast: [LargeNumber::class, 'createFromString'])]
        private LargeNumber $capacity,
    ) {}

    /**
     * @return MasterId<MasterArmor>
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

    public function getDefense(): LargeNumber
    {
        return $this->defense;
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
            'defense' => $this->defense->toHumanReadableString(),
            'xp_bonus_rate' => $this->xp_bonus_rate,
            'capacity' => $this->capacity->toHumanReadableString(),
        ];
    }
}
