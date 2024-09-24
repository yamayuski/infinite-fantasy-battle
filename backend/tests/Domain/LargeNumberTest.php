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
            '1' => ['1', '1e0', '1'],
            '10' => ['10', '1e1', '10'],
        ];
    }

    #[Test]
    #[DataProvider('getCreateFromString')]
    public function testCreateFromString(string $input, string $expected_string, string $expected_unit_string): void
    {
        $actual = LargeNumber::createFromString($input);

        self::assertSame($expected_string, $actual->__toString());
        // self::assertSame($expected_unit_string, $actual->getWithUnit());
    }
}
