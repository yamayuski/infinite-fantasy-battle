<?php

declare(strict_types=1);

/**
 * @license MIT
 */

namespace Ifb;

use JsonSerializable;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @template T of object
     * @param class-string<T> $target
     * @return MockInterface&T
     */
    public static function createMockeryMock(string $target): MockInterface
    {
        /** @var MockInterface&T $t */
        $t = Mockery::mock($target);

        return $t;
    }

    /**
     * Assert Handler result
     * @param array<array-key, mixed> $expected
     * @param mixed $actual
     * @param string $message
     */
    public static function assertHandler(array $expected, mixed $actual, string $message = ''): void
    {
        self::assertInstanceOf(JsonSerializable::class, $actual, 'Output is \JsonSerializable');
        $json = \json_encode($actual);
        self::assertIsString($json);
        self::assertJson($json, 'Json encodable');
        $expected_json = \json_encode($expected);
        self::assertIsString($expected_json);
        self::assertSame($expected_json, $json, $message);
    }
}
