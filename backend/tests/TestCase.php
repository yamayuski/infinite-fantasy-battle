<?php

declare(strict_types=1);

/**
 * @license MIT
 */

namespace Ifb;

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
}
