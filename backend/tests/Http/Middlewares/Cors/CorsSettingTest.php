<?php

declare(strict_types=1);

/**
 * @license MIT
 */

namespace Ifb\Http\Middlewares\Cors;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(CorsSetting::class)]
final class CorsSettingTest extends TestCase
{
    /**
     * @return iterable<string, mixed>
     */
    public static function getInvalidData(): iterable
    {
        return [
            'empty origin' => [
                '',
                [],
                ['GET'],
                [],
                [],
                ['Origin'],
                false,
                5,
            ],
            'empty methods' => [
                '',
                ['http://example.com'],
                [],
                [],
                [],
                ['Origin'],
                false,
                5,
            ],
            'empty vary' => [
                '',
                ['http://example.com'],
                ['GET'],
                [],
                [],
                [],
                false,
                5,
            ],
            'multiple origins with *' => [
                '',
                ['http://example.com', '*'],
                ['GET'],
                [],
                [],
                ['Origin'],
                false,
                5,
            ],
            'multiple headers with *' => [
                '',
                ['http://example.com'],
                ['GET'],
                ['Content-Type', '*'],
                [],
                ['Origin'],
                false,
                5,
            ],
            'multiple methods with *' => [
                '',
                ['http://example.com'],
                ['GET', '*'],
                [],
                [],
                ['Origin'],
                false,
                5,
            ],
            'allow credentials with * origin' => [
                '',
                ['*'],
                ['GET'],
                [],
                [],
                ['Origin'],
                true,
                5,
            ],
            'allow credentials with * headers' => [
                '',
                ['http://example.com'],
                ['GET'],
                ['*'],
                [],
                ['Origin'],
                true,
                5,
            ],
            'allow credentials with * methods' => [
                '',
                ['http://example.com'],
                ['*'],
                [],
                [],
                ['Origin'],
                true,
                5,
            ],
        ];
    }

    /**
     * @param non-empty-string $server_origin
     * @param non-empty-string[] $allow_origin
     * @param non-empty-string[] $allow_methods
     * @param non-empty-string[] $allow_headers
     * @param non-empty-string[] $expose_headers
     * @param non-empty-string[] $vary
     * @param bool $allow_credentials
     * @param int $max_age
     */
    #[Test]
    #[DataProvider('getInvalidData')]
    public function testInvalidArgument(
        string $server_origin,
        array $allow_origin,
        array $allow_methods,
        array $allow_headers,
        array $expose_headers,
        array $vary,
        bool $allow_credentials,
        int $max_age,
    ): void {
        $this->expectException(InvalidArgumentException::class);

        new CorsSetting(
            $server_origin,
            $allow_origin,
            $allow_methods,
            $allow_headers,
            $expose_headers,
            $vary,
            $allow_credentials,
            $max_age,
        );
    }
}
