<?php

declare(strict_types=1);

/**
 * @author Masaru Yamagishi <m.yamagishi90+git@gmail.com>
 * @license MIT
 */

namespace Ifb\Domain;

use InvalidArgumentException;
use JsonSerializable;
use Stringable;

/**
 * Represents a large number like 1.23e45 as 1.23 and 45
 * It may lose precision but it is enough for our use case
 * @package Ifb\Domain
 */
class LargeNumber implements JsonSerializable, Stringable
{
    public function __construct(
        private float $fract,
        private int $exponent,
    ) {}

    public static function createFromString(string $value): self
    {
        if (\str_contains($value, 'e')) {
            [$base, $exponent] = \explode('e', \strtolower($value), 2);

            $base = \str_replace('.', '', $base);
            $decimal_positions = 0;

            if (\strpos($value, '.') !== false) {
                [$integer_part, $fractional_part] = \explode('.', \explode('e', $value)[0], 2);
                $decimal_positions = \strlen($fractional_part);
            }

            $exponent = (int)$exponent + $decimal_positions - 1;
            $float_part = '0.' . $base;

            return new self((float) $float_part, (int) $exponent + 1);
        } elseif (\preg_match('/^\d+\.?\d*$/', $value)) {
            if (\strpos($value, '.') !== false) {
                [$integer_part, $fractional_part] = \explode('.', $value, 2);
            } else {
                $integer_part = $value;
                $fractional_part = '';
            }

            $number_without_decimal = $integer_part . $fractional_part;
            $non_zero_digit_position = \strlen($integer_part);
            $float_part = '0.' . $number_without_decimal;

            return new self((float) $float_part, $non_zero_digit_position);
        }

        throw new InvalidArgumentException(\sprintf('Invalid large number format: "%s"', $value));
    }

    public function jsonSerialize(): mixed
    {
        return [
            'fract' => $this->fract,
            'exponent' => $this->exponent,
        ];
    }

    public function __toString(): string
    {
        return \number_format($this->fract, 3) . 'e' . $this->exponent;
    }

    public function getWithUnit(): string
    {
        return \number_format($this->fract, 3) . ' ' . $this->getUnit();
    }

    public function getUnit(): string
    {
        throw new \LogicException('TODO');
    }
}
