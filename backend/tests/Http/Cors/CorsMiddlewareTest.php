<?php

declare(strict_types=1);

/**
 * @license MIT
 */

namespace Ifb\Http\Cors;

use Ifb\Http\RouteResolverInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

#[CoversClass(CorsMiddleware::class)]
#[UsesClass(CorsSetting::class)]
final class CorsMiddlewareTest extends TestCase
{
    #[Test]
    public function testProcessNotFound(): void
    {
        $request = self::createStub(ServerRequestInterface::class);
        $request->method('getMethod')->willReturn('OPTIONS');
        $request->method('hasHeader')->willReturn(true);
        $request->method('getHeaderLine')->willReturn('GET');

        $route_resolver = self::createStub(RouteResolverInterface::class);
        $route_resolver->method('resolve')->willReturn(null);

        $response_factory = self::createStub(ResponseFactoryInterface::class);
        $response_factory->method('createResponse')->willReturn($expected = self::createStub(ResponseInterface::class));

        $middleware = new CorsMiddleware(
            $response_factory,
            self::createStub(RequestValidator::class),
            $route_resolver,
            new CorsSetting('https://a.com', ['http://example.com']),
        );

        $actual = $middleware->process($request, self::createStub(RequestHandlerInterface::class));

        self::assertSame($expected, $actual);
    }
}
