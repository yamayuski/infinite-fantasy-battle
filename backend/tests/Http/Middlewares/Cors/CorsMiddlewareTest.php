<?php

declare(strict_types=1);

/**
 * @license MIT
 */

namespace Ifb\Http\Middlewares\Cors;

use Ifb\Http\Route\Route;
use Ifb\Http\Route\RouteResolverInterface;
use Ifb\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

#[CoversClass(CorsMiddleware::class)]
#[UsesClass(CorsConfig::class)]
#[UsesClass(Route::class)]
final class CorsMiddlewareTest extends TestCase
{
    #[Test]
    public function testNotFound(): void
    {
        $response_factory = self::createMockeryMock(ResponseFactoryInterface::class);
        $validator = self::createMockeryMock(RequestValidator::class);
        $route_resolver = self::createMockeryMock(RouteResolverInterface::class);
        $config = new CorsConfig(
            server_origin: 'https://api.ifb.test',
            allow_origin: ['https://ifb.test'],
            allow_methods: ['GET', 'POST'],
            allow_headers: ['Content-Type', 'Accept'],
            expose_headers: [],
        );

        $middleware = new CorsMiddleware($response_factory, $validator, $route_resolver, $config);

        $request = self::createMockeryMock(ServerRequestInterface::class);
        $request->shouldReceive('getMethod')
            ->once()
            ->withNoArgs()
            ->andReturn('OPTIONS');
        $request->shouldReceive('hasHeader')
            ->once()
            ->with('Access-Control-Request-Method')
            ->andReturn(true);
        $request->shouldReceive('getHeaderLine')
            ->once()
            ->with('Access-Control-Request-Method')
            ->andReturn('POST');
        $request->shouldReceive('getUri->getPath')
            ->once()
            ->andReturn('/');

        $route_resolver->shouldReceive('resolve')
            ->with('POST', '/')
            ->andReturnNull();

        $response_factory->shouldReceive('createResponse')
            ->once()
            ->with(404)
            ->andReturn($expected = self::createMockeryMock(ResponseInterface::class));

        $actual = $middleware->process($request, self::createMockeryMock(RequestHandlerInterface::class));

        self::assertSame($expected, $actual);
    }

    #[Test]
    public function testNotFoundDirect(): void
    {
        $response_factory = self::createMockeryMock(ResponseFactoryInterface::class);
        $validator = self::createMockeryMock(RequestValidator::class);
        $route_resolver = self::createMockeryMock(RouteResolverInterface::class);
        $config = new CorsConfig(
            server_origin: 'https://api.ifb.test',
            allow_origin: ['https://ifb.test'],
            allow_methods: ['GET', 'POST'],
            allow_headers: ['Content-Type', 'Accept'],
            expose_headers: [],
        );

        $middleware = new CorsMiddleware($response_factory, $validator, $route_resolver, $config);

        $request = self::createMockeryMock(ServerRequestInterface::class);
        $request->shouldReceive('getMethod')
            ->twice()
            ->withNoArgs()
            ->andReturn('POST');
        $request->shouldReceive('getUri->getPath')
            ->once()
            ->andReturn('/');

        $route_resolver->shouldReceive('resolve')
            ->with('POST', '/')
            ->andReturnNull();

        $response_factory->shouldReceive('createResponse')
            ->once()
            ->with(404)
            ->andReturn($expected = self::createMockeryMock(ResponseInterface::class));

        $actual = $middleware->process($request, self::createMockeryMock(RequestHandlerInterface::class));

        self::assertSame($expected, $actual);
    }

    /**
     * @return array<string, array{0: RequestValidationResult, 1: int}>
     */
    public static function getInvalidResult(): array
    {
        return [
            'Origin not found' => [RequestValidationResult::ORIGIN_NOT_FOUND, 400],
            'Origin not allowed' => [RequestValidationResult::ORIGIN_NOT_ALLOWED, 400],
            'Method not allowed' => [RequestValidationResult::METHOD_NOT_ALLOWED, 405],
            'Headers not allowed' => [RequestValidationResult::HEADERS_NOT_ALLOWED, 400],
        ];
    }

    #[Test]
    #[DataProvider('getInvalidResult')]
    public function testInvalidResult(RequestValidationResult $result, int $status_code): void
    {
        $response_factory = self::createMockeryMock(ResponseFactoryInterface::class);
        $validator = self::createMockeryMock(RequestValidator::class);
        $route_resolver = self::createMockeryMock(RouteResolverInterface::class);
        $config = new CorsConfig(
            server_origin: 'https://api.ifb.test',
            allow_origin: ['https://ifb.test'],
            allow_methods: ['GET', 'POST'],
            allow_headers: ['Content-Type', 'Accept'],
            expose_headers: [],
        );

        $middleware = new CorsMiddleware($response_factory, $validator, $route_resolver, $config);

        $request = self::createMockeryMock(ServerRequestInterface::class);
        $request->shouldReceive('getMethod')
            ->twice()
            ->withNoArgs()
            ->andReturn('POST');
        $request->shouldReceive('getUri->getPath')
            ->once()
            ->andReturn('/');

        $route_resolver->shouldReceive('resolve')
            ->with('POST', '/')
            ->andReturn(new Route('POST', '/', self::class, []));

        $validator->shouldReceive('validate')
            ->once()
            ->with($request)
            ->andReturn($result);

        $response_factory->shouldReceive('createResponse')
            ->once()
            ->with($status_code)
            ->andReturn($expected = self::createMockeryMock(ResponseInterface::class));

        $actual = $middleware->process($request, self::createMockeryMock(RequestHandlerInterface::class));

        self::assertSame($expected, $actual);
    }

    #[Test]
    public function testSameOrigin(): void
    {
        $response_factory = self::createMockeryMock(ResponseFactoryInterface::class);
        $validator = self::createMockeryMock(RequestValidator::class);
        $route_resolver = self::createMockeryMock(RouteResolverInterface::class);
        $config = new CorsConfig(
            server_origin: 'https://api.ifb.test',
            allow_origin: ['https://ifb.test'],
            allow_methods: ['GET', 'POST'],
            allow_headers: ['Content-Type', 'Accept'],
            expose_headers: [],
        );

        $middleware = new CorsMiddleware($response_factory, $validator, $route_resolver, $config);

        $request = self::createMockeryMock(ServerRequestInterface::class);
        $request->shouldReceive('getMethod')
            ->twice()
            ->withNoArgs()
            ->andReturn('POST');
        $request->shouldReceive('getUri->getPath')
            ->once()
            ->andReturn('/');

        $route_resolver->shouldReceive('resolve')
            ->with('POST', '/')
            ->andReturn(new Route('POST', '/', self::class, []));

        $validator->shouldReceive('validate')
            ->once()
            ->with($request)
            ->andReturn(RequestValidationResult::SAME_ORIGIN);

        $handler = self::createMockeryMock(RequestHandlerInterface::class);
        $handler->shouldReceive('handle')
            ->once()
            ->with($request)
            ->andReturn($expected = self::createMockeryMock(ResponseInterface::class));

        $actual = $middleware->process($request, $handler);

        self::assertSame($expected, $actual);
    }

    #[Test]
    public function testCrossOriginPreflightRequest(): void
    {
        $response_factory = self::createMockeryMock(ResponseFactoryInterface::class);
        $validator = self::createMockeryMock(RequestValidator::class);
        $route_resolver = self::createMockeryMock(RouteResolverInterface::class);
        $config = new CorsConfig(
            server_origin: 'https://api.ifb.test',
            allow_origin: ['https://ifb.test'],
            allow_methods: ['GET', 'POST'],
            allow_headers: ['Content-Type', 'Accept'],
            expose_headers: ['Content-Type'],
            allow_credentials: true,
        );

        $middleware = new CorsMiddleware($response_factory, $validator, $route_resolver, $config);

        $request = self::createMockeryMock(ServerRequestInterface::class);
        $request->shouldReceive('getMethod')
            ->times(2)
            ->withNoArgs()
            ->andReturn('OPTIONS');
        $request->shouldReceive('hasHeader')
            ->times(2)
            ->with('Access-Control-Request-Method')
            ->andReturn(true);
        $request->shouldReceive('getHeaderLine')
            ->once()
            ->with('Access-Control-Request-Method')
            ->andReturn('POST');
        $request->shouldReceive('getUri->getPath')
            ->once()
            ->andReturn('/');

        $route_resolver->shouldReceive('resolve')
            ->with('POST', '/')
            ->andReturn(new Route('POST', '/', self::class, []));

        $validator->shouldReceive('validate')
            ->once()
            ->with($request)
            ->andReturn(RequestValidationResult::VALID_CROSS_ORIGIN);

        $response_factory->shouldReceive('createResponse')
            ->once()
            ->with(204)
            ->andReturn($response1 = self::createMockeryMock(ResponseInterface::class));

        $response1->shouldReceive('withHeader')
            ->once()
            ->with('Access-Control-Allow-Origin', ['https://ifb.test'])
            ->andReturn($response2 = self::createMockeryMock(ResponseInterface::class));

        $response2->shouldReceive('withHeader')
            ->once()
            ->with('Vary', ['Origin'])
            ->andReturn($response3 = self::createMockeryMock(ResponseInterface::class));

        $response3->shouldReceive('withHeader')
            ->once()
            ->with('Access-Control-Allow-Methods', ['GET', 'POST'])
            ->andReturn($response4 = self::createMockeryMock(ResponseInterface::class));

        $response4->shouldReceive('withHeader')
            ->once()
            ->with('Access-Control-Allow-Headers', ['Content-Type', 'Accept'])
            ->andReturn($response5 = self::createMockeryMock(ResponseInterface::class));

        $response5->shouldReceive('withHeader')
            ->once()
            ->with('Access-Control-Max-Age', '5')
            ->andReturn($response6 = self::createMockeryMock(ResponseInterface::class));

        $response6->shouldReceive('withHeader')
            ->once()
            ->with('Access-Control-Expose-Headers', ['Content-Type'])
            ->andReturn($response7 = self::createMockeryMock(ResponseInterface::class));

        $response7->shouldReceive('withHeader')
            ->once()
            ->with('Access-Control-Allow-Credentials', 'true')
            ->andReturn($expected = self::createMockeryMock(ResponseInterface::class));

        $handler = self::createMockeryMock(RequestHandlerInterface::class);
        $handler->shouldReceive('handle')
            ->never();

        $actual = $middleware->process($request, $handler);

        self::assertSame($expected, $actual);
    }

    #[Test]
    public function testCrossOriginActualRequest(): void
    {
        $response_factory = self::createMockeryMock(ResponseFactoryInterface::class);
        $validator = self::createMockeryMock(RequestValidator::class);
        $route_resolver = self::createMockeryMock(RouteResolverInterface::class);
        $config = new CorsConfig(
            server_origin: 'https://api.ifb.test',
            allow_origin: ['https://ifb.test'],
            allow_methods: ['GET', 'POST'],
            allow_headers: ['Content-Type', 'Accept'],
            expose_headers: ['Content-Type'],
            allow_credentials: true,
        );

        $middleware = new CorsMiddleware($response_factory, $validator, $route_resolver, $config);

        $request = self::createMockeryMock(ServerRequestInterface::class);
        $request->shouldReceive('getMethod')
            ->times(3)
            ->withNoArgs()
            ->andReturn('POST');
        $request->shouldReceive('getUri->getPath')
            ->once()
            ->andReturn('/');

        $route_resolver->shouldReceive('resolve')
            ->with('POST', '/')
            ->andReturn(new Route('POST', '/', self::class, []));

        $validator->shouldReceive('validate')
            ->once()
            ->with($request)
            ->andReturn(RequestValidationResult::VALID_CROSS_ORIGIN);

        $handler = self::createMockeryMock(RequestHandlerInterface::class);
            $handler->shouldReceive('handle')
                ->once()
                ->with($request)
                ->andReturn($response1 = self::createMockeryMock(ResponseInterface::class));

        $response1->shouldReceive('withHeader')
            ->once()
            ->with('Access-Control-Allow-Origin', ['https://ifb.test'])
            ->andReturn($response2 = self::createMockeryMock(ResponseInterface::class));

        $response2->shouldReceive('withHeader')
            ->once()
            ->with('Vary', ['Origin'])
            ->andReturn($expected = self::createMockeryMock(ResponseInterface::class));

        $actual = $middleware->process($request, $handler);

        self::assertSame($expected, $actual);
    }
}
