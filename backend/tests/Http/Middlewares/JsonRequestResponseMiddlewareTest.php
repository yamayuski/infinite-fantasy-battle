<?php

declare(strict_types=1);

/**
 * @license MIT
 */

namespace Ifb\Http\Middlewares;

use Ifb\TestCase;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Server\RequestHandlerInterface;

#[CoversClass(JsonRequestResponseMiddleware::class)]
final class JsonRequestResponseMiddlewareTest extends TestCase
{
    #[Test]
    public function testAcceptRequired(): void
    {
        $response_factory = self::createMockeryMock(ResponseFactoryInterface::class);
        $stream_factory = self::createMockeryMock(StreamFactoryInterface::class);

        $middleware = new JsonRequestResponseMiddleware($response_factory, $stream_factory);

        $expected = $this->mockFactory(
            $response_factory,
            $stream_factory,
            406,
            '{"message":"Accept header is required"}',
            39,
        );

        $request = self::createMockeryMock(ServerRequestInterface::class);

        $request->shouldReceive('hasHeader')
            ->with('Accept')
            ->andReturn(false);

        $actual = $middleware->process($request, self::createMockeryMock(RequestHandlerInterface::class));

        self::assertSame($expected, $actual);
    }

    #[Test]
    public function testAcceptInvalid(): void
    {
        $response_factory = self::createMockeryMock(ResponseFactoryInterface::class);
        $stream_factory = self::createMockeryMock(StreamFactoryInterface::class);

        $middleware = new JsonRequestResponseMiddleware($response_factory, $stream_factory);

        $expected = $this->mockFactory(
            $response_factory,
            $stream_factory,
            406,
            '{"message":"Accept header must be application/json"}',
            52,
        );

        $request = self::createMockeryMock(ServerRequestInterface::class);

        $request->shouldReceive('hasHeader')
            ->with('Accept')
            ->andReturn(true);

        $request->shouldReceive('getHeaderLine')
            ->with('Accept')
            ->andReturn('text/html');

        $actual = $middleware->process($request, self::createMockeryMock(RequestHandlerInterface::class));

        self::assertSame($expected, $actual);
    }

    #[Test]
    public function testContentTypeInvalid(): void
    {
        $response_factory = self::createMockeryMock(ResponseFactoryInterface::class);
        $stream_factory = self::createMockeryMock(StreamFactoryInterface::class);

        $middleware = new JsonRequestResponseMiddleware($response_factory, $stream_factory);

        $expected = $this->mockFactory(
            $response_factory,
            $stream_factory,
            415,
            '{"message":"Content-Type header must be application/json"}',
            58,
        );

        $request = self::createMockeryMock(ServerRequestInterface::class);

        $request->shouldReceive('hasHeader')
            ->with('Accept')
            ->andReturn(true);

        $request->shouldReceive('getHeaderLine')
            ->with('Accept')
            ->andReturn('application/json; charset=UTF-8');

        $request->shouldReceive('hasHeader')
            ->with('Content-Type')
            ->andReturn(true);

        $request->shouldReceive('getHeaderLine')
            ->with('Content-Type')
            ->andReturn('text/html');

        $actual = $middleware->process($request, self::createMockeryMock(RequestHandlerInterface::class));

        self::assertSame($expected, $actual);
    }

    #[Test]
    public function testValid(): void
    {
        $response_factory = self::createMockeryMock(ResponseFactoryInterface::class);
        $stream_factory = self::createMockeryMock(StreamFactoryInterface::class);

        $middleware = new JsonRequestResponseMiddleware($response_factory, $stream_factory);

        $expected = $this->mockFactory(
            $response_factory,
            $stream_factory,
            400,
            '{"message":"Invalid JSON"}',
            26,
        );

        $request = self::createMockeryMock(ServerRequestInterface::class);

        $request->shouldReceive('hasHeader')
            ->with('Accept')
            ->andReturn(true);

        $request->shouldReceive('getHeaderLine')
            ->with('Accept')
            ->andReturn('application/json; charset=UTF-8');

        $request->shouldReceive('hasHeader')
            ->with('Content-Type')
            ->andReturn(true);

        $request->shouldReceive('getHeaderLine')
            ->with('Content-Type')
            ->andReturn('application/json');

        $request->shouldReceive('getBody')
            ->andReturn($stream_factory = self::createMockeryMock(StreamInterface::class));

        $stream_factory->shouldReceive('getContents')
            ->andReturn('{"key":"value"}');

        $request->shouldReceive('withParsedBody')
            ->with(['key' => 'value'])
            ->andReturn($request2 = self::createMockeryMock(ServerRequestInterface::class));

        $handler = self::createMockeryMock(RequestHandlerInterface::class);

        $handler->shouldReceive('handle')
            ->with($request2)
            ->andReturn($expected);

        $expected->shouldReceive('withHeader')
            ->with('Content-Type', 'application/json; charset=UTF-8')
            ->andReturn($expected2 = self::createMockeryMock(ResponseInterface::class));

        $actual = $middleware->process($request, $handler);

        self::assertSame($expected2, $actual);
    }

    #[Test]
    public function testInvalidJson(): void
    {
        $response_factory = self::createMockeryMock(ResponseFactoryInterface::class);
        $stream_factory = self::createMockeryMock(StreamFactoryInterface::class);

        $middleware = new JsonRequestResponseMiddleware($response_factory, $stream_factory);

        $expected = $this->mockFactory(
            $response_factory,
            $stream_factory,
            400,
            '{"message":"Invalid JSON"}',
            26,
        );

        $request = self::createMockeryMock(ServerRequestInterface::class);

        $request->shouldReceive('hasHeader')
            ->with('Accept')
            ->andReturn(true);

        $request->shouldReceive('getHeaderLine')
            ->with('Accept')
            ->andReturn('application/json; charset=UTF-8');

        $request->shouldReceive('hasHeader')
            ->with('Content-Type')
            ->andReturn(true);

        $request->shouldReceive('getHeaderLine')
            ->with('Content-Type')
            ->andReturn('application/json');

        $request->shouldReceive('getBody')
            ->andReturn($stream_factory = self::createMockeryMock(StreamInterface::class));

        $stream_factory->shouldReceive('getContents')
            ->andReturn('invalid json');

        $actual = $middleware->process($request, self::createMockeryMock(RequestHandlerInterface::class));

        self::assertSame($expected, $actual);
    }

    private function mockFactory(
        MockInterface&ResponseFactoryInterface $response_factory,
        MockInterface&StreamFactoryInterface $stream_factory,
        int $status_code,
        string $body,
        int $content_length,
    ): MockInterface&ResponseInterface {

        $response_factory->shouldReceive('createResponse')
            ->with($status_code)
            ->andReturn($response1 = self::createMockeryMock(ResponseInterface::class));

        $response1->shouldReceive('withHeader')
            ->with('Content-Type', 'application/json')
            ->andReturn($response2 = self::createMockeryMock(ResponseInterface::class));

        $response2->shouldReceive('withHeader')
            ->with('Content-Length', \strval($content_length))
            ->andReturn($response3 = self::createMockeryMock(ResponseInterface::class));

        $stream_factory->shouldReceive('createStream')
            ->with($body)
            ->andReturn($stream = self::createMockeryMock(StreamInterface::class));
        $response3->shouldReceive('withBody')
            ->with($stream)
            ->andReturn($response4 = self::createMockeryMock(ResponseInterface::class));

        return $response4;
    }
}
