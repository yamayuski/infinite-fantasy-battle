<?php

declare(strict_types=1);

/**
 * @license MIT
 */

namespace Ifb\Domain;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(LargeNumber::class)]
final class LargeNumberTest extends TestCase
{
    /**
     * @return array<array-key, array<int, string>>
     */
    public static function getCreateFromString(): array
    {
        return [
            '0' => ['0', '0.000e0'],
            '0.0' => ['0.0', '0.000e0'],
            '0.0e0' => ['0.0e0', '0.000e0'],
            '1' => ['1', '0.100e1'],
            '10' => ['10', '0.100e2'],
            '100' => ['100', '0.100e3'],
            '1,000' => ['1,000', '0.100e4'],
            '10,000' => ['10,000', '0.100e5'],
            '100,000' => ['100,000', '0.100e6'],
            '1,000,000' => ['1,000,000', '0.100e7'],
            '10,000,000' => ['10,000,000', '0.100e8'],
            '100,000,000' => ['100,000,000', '0.100e9'],
            '1,000,000,000' => ['1,000,000,000', '0.100e10'],
            '10,000,000,000' => ['10,000,000,000', '0.100e11'],
            '100,000,000,000' => ['100,000,000,000', '0.100e12'],
            '1,000,000,000,000' => ['1,000,000,000,000', '0.100e13'],
            '10,000,000,000,000' => ['10,000,000,000,000', '0.100e14'],

            '1.5' => ['1.5', '0.150e1'],
            '10.5' => ['10.5', '0.105e2'],
            '100.5' => ['100.5', '0.101e3'],
            '1,000.5' => ['1,000.5', '0.100e4'],
            '10,000.5' => ['10,000.5', '0.100e5'],
            '100,000.5' => ['100,000.5', '0.100e6'],
            '1,000,000.55555' => ['1,000,000.55555', '0.100e7'],

            '0.00100' => ['0.00100', '0.100e-3'],

            '1e1' => ['1e1', '0.100e2'],
            '1e2' => ['1e2', '0.100e3'],
            '1e3' => ['1e3', '0.100e4'],
            '1e4' => ['1e4', '0.100e5'],
            '1e5' => ['1e5', '0.100e6'],

            '1.5e1' => ['1.5e1', '0.150e2'],

            '1,000.555e5' => ['1,005.555e5', '0.101e9'],
            '100,000,000,000,000,000,000.000000e100' => ['100,000,000,000,000,000,000.000000e100', '0.100e121'],

            "1.0\n" => ["1.0\n", '0.100e1'],
        ];
    }

    #[Test]
    #[DataProvider('getCreateFromString')]
    public function testCreateFromString(string $input, string $expected_string): void
    {
        $actual = LargeNumber::createFromString($input);

        self::assertSame($expected_string, (string) $actual);
    }

    /**
     * @return array<array-key, array<int, string>>
     */
    public static function getNegativeNumber(): array
    {
        return [
            'negative' => ['-1'],
            'negative with exponent' => ['-1e1'],
            'negative with fract' => ['-1.0'],
            'negative with fract and exponent' => ['-1.0e1'],
        ];
    }

    #[Test]
    #[DataProvider('getNegativeNumber')]
    public function testCreateFromStringWithNegativeNumber(string $input): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(\sprintf('Negative number is not supported: "%s"', $input));

        LargeNumber::createFromString($input);
    }

    /**
     * @return array<array-key, array<int, string>>
     */
    public static function getInvalidNumber(): array
    {
        return [
            'empty' => [''],
            'invalid' => ['invalid'],
            'invalid with number' => ['invalid1'],
            'invalid with exponent' => ['1einvalid'],
            'invalid with fract' => ['1.invalid'],
            'invalid with fract and exponent' => ['1.invalide1'],
        ];
    }

    #[Test]
    #[DataProvider('getInvalidNumber')]
    public function testCreateFromStringWithInvalidNumber(string $input): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(\sprintf('Invalid large number format: "%s"', $input));

        LargeNumber::createFromString($input);
    }

    /**
     * @return array<array-key, array<int, string>>
     */
    public static function getToHumanReadableString(): array
    {
        return [
            '0' => ['0', '0.000'],
            '10' => ['10', '10.000'],
            '100' => ['100', '100.000'],
            '1,000' => ['1,000', '1,000.000'],
            '10,000' => ['10,000', '10,000.000'],
            '100,000' => ['100,000', '100,000.000'],

            '999,999' => ['999,999', '999,999.000'],
            '1,000,000' => ['1,000,000', '1.000 a'],
            '1,100,000' => ['1,100,000', '1.100 a'],
            '5,250,000' => ['5,250,000', '5.250 a'],
            '10,000,000' => ['10,000,000', '10.000 a'],
            '100,000,000' => ['100,000,000', '0.100 b'],
            '1,000,000,000' => ['1,000,000,000', '1.000 b'],
            '1,100,000,000' => ['1,100,000,000', '1.100 b'],
            '1,000,000,000,000' => ['1,000,000,000,000', '1.000 c'],
            '1e15' => ['1e15', '1.000 d'],
            '1e18' => ['1e18', '1.000 e'],
            '1e81' => ['1e81', '1.000 z'],
            '1e84' => ['1e84', '1.000 aa'],
            '1e87' => ['1e87', '1.000 ab'],
        ];
    }

    #[Test]
    #[DataProvider('getToHumanReadableString')]
    public function testToHumanReadableString(string $input, string $expected): void
    {
        $actual = LargeNumber::createFromString($input);

        self::assertSame($expected, $actual->toHumanReadableString());
    }
}
