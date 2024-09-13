<?php

declare(strict_types=1);

/**
 * @license MIT
 */

namespace Ifb\Http\HttpHandler;

use Ifb\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

#[CoversClass(MiddlewaresHandler::class)]
final class MiddlewaresHandlerTest extends TestCase
{
    #[Test]
    public function testHandle(): void
    {
        $container = $this->createMockeryMock(ContainerInterface::class);
        $middlewares = [
            self::class,
        ];
        $handler = $this->createMockeryMock(RequestHandlerInterface::class);
        $handler->shouldReceive('handle')
            ->once()
            ->andReturn($response = $this->createMockeryMock(ResponseInterface::class));

        $container->shouldReceive('get')
            ->with(self::class)
            ->once()
            ->andReturn(new class () implements MiddlewareInterface {
                public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
                {
                    return $handler->handle($request);
                }
            });

        $middlewares_handler = new MiddlewaresHandler($container, $middlewares, $handler);

        $request = $this->createMockeryMock(ServerRequestInterface::class);

        $actual = $middlewares_handler->handle($request);

        self::assertSame($response, $actual);
    }

    #[Test]
    public function testInvalidMiddleware(): void
    {
        $container = $this->createMockeryMock(ContainerInterface::class);
        $middlewares = ['invalid_middleware'];
        $handler = $this->createMockeryMock(RequestHandlerInterface::class);

        $this->expectException(InvalidHandlerDefinitionException::class);
        $this->expectExceptionMessage('Middleware must implement MiddlewareInterface: invalid_middleware');

        $container->shouldReceive('get')
            ->with('invalid_middleware')
            ->once()
            ->andReturnNull();

        /** @phpstan-ignore argument.type */
        (new MiddlewaresHandler($container, $middlewares, $handler))->handle($this->createMockeryMock(ServerRequestInterface::class));
    }
}
