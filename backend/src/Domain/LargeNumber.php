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

    /**
     * Create a LargeNumber from a string
     * NOTICE: Negative number is not supported
     * example: '1.23e45', '123.45e-67', '123.45'
     * @param string $value
     * @return LargeNumber
     * @throws InvalidArgumentException
     */
    public static function createFromString(string $value): self
    {
        if (\preg_match('/^(?P<int_part>[\d,]+)\.?(?P<fract_part>\d+)?e?(?P<exponent_part>\d*)?$/', $value, $matches)) {
            // 1,234(.56e7)
            $int_part = $matches['int_part'];
            $fract_part = \array_key_exists('fract_part', $matches) ? $matches['fract_part'] : '';
            $exponent_part = \array_key_exists('exponent_part', $matches) ? $matches['exponent_part'] : '';

            $int_part = \trim(\ltrim(\str_replace(',', '', $int_part), '0'));
            $fract_part = \trim(\rtrim($fract_part, '0'));
            $exponent_part = (int) \trim(\ltrim($exponent_part, '0'));

            $float = '0.' . $int_part . $fract_part;
            $int_part_length = \strlen($int_part);
            if ($int_part === '' && $fract_part > 0) {
                $float = '0.' . \ltrim($fract_part, '0');
                $int_part_length = -\strlen((string)$fract_part);
            }

            return new self(
                (float) $float,
                $int_part_length + $exponent_part,
            );
        } elseif (\str_starts_with($value, '-')) {
            throw new InvalidArgumentException(\sprintf('Negative number is not supported: "%s"', $value));
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
