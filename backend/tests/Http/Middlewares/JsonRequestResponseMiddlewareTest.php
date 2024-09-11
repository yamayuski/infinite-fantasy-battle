<?php

declare(strict_types=1);

/**
 * @license MIT
 */

namespace Ifb\Http\Middlewares;

use Mockery;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Server\RequestHandlerInterface;

#[CoversClass(JsonRequestResponseMiddleware::class)]
final class JsonRequestResponseMiddlewareTest extends TestCase
{
    use \Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

    #[Test]
    public function testAcceptRequired(): void
    {
        $response_factory = Mockery::mock(ResponseFactoryInterface::class);
        $stream_factory = Mockery::mock(StreamFactoryInterface::class);
        $middleware = new JsonRequestResponseMiddleware($response_factory, $stream_factory);
    }
}
