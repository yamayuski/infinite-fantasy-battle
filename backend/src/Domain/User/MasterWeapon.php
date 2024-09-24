<?php

declare(strict_types=1);

/**
 * @author Masaru Yamagishi <m.yamagishi90+git@gmail.com>
 * @license MIT
 */

namespace Ifb\Domain\User;

use Ifb\Domain\Identity\MasterId;

/**
 * Weapon master data
 * @package Ifb\Domain\User
 */
readonly class MasterWeapon
{
    /**
     * @param MasterId<MasterWeapon> $id 武器マスターID
     * @param string $name 武器名称
     * @param string $description フレーバーテキスト
     * @param float $attack_fract 攻撃力(小数部)
     * @param int $attack_exponent 攻撃力(指数部)
     * @param float $critial_rate クリティカル率(%)
     * @param float $critical_multiplier_fract クリティカル時のダメージ倍率(小数部)
     * @param int $critical_multiplier_exponent クリティカル時のダメージ倍率(指数部)
     * @param float $capacity_fract
     * @param int $capacity_exponent
     * @return void
     */
    public function __construct(
        public MasterId $id,
        public string $name,
        public string $description,
        public float $attack_fract,
        public int $attack_exponent,
        public float $critial_rate,
        public float $critical_multiplier_fract,
        public int $critical_multiplier_exponent,
        public float $capacity_fract,
        public int $capacity_exponent,
    ) {}
}
