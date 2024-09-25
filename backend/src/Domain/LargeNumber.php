<?php

declare(strict_types=1);

/**
 * @author Masaru Yamagishi <m.yamagishi90+git@gmail.com>
 * @license MIT
 */

namespace Ifb\Domain;

use InvalidArgumentException;
use Stringable;

/**
 * Represents a large number like 1.23e45 as 1.23 and 45
 * It may lose precision but it is enough for our use case
 * @package Ifb\Domain
 */
class LargeNumber implements Stringable
{
    private function __construct(
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
                $int_part_length = -\strlen((string) $fract_part);
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

    public function __toString(): string
    {
        return \number_format($this->fract, 3) . 'e' . $this->exponent;
    }

    /**
     * Convert to a human readable string
     * like: 999999 -> 999,999.000, 1000000 -> 1.000 a, 1000000000 -> 1.000 b
     * @return string
     */
    public function toHumanReadableString(): string
    {
        if ($this->exponent < 7) {
            return \number_format($this->fract * 10 ** $this->exponent, 3);
        }

        $number = $this->fract * (10 ** ($this->exponent % 3));
        $unitIndex = \intdiv($this->exponent, 3);

        $unit = $this->generateUnitFromIndex($unitIndex - 1);

        return sprintf('%.3f %s', $number, $unit);
    }

    protected function generateUnitFromIndex(int $index): string
    {
        $alphabets = \range('a', 'z');
        $unit = '';

        while ($index > 0) {
            $index--;
            $unit = $alphabets[$index % 26] . $unit;
            $index = \intdiv($index, 26);
        }

        return $unit;
    }
}
