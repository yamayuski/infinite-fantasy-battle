<?php

declare(strict_types=1);

/**
 * @license MIT
 */

namespace Ifb\Http\HttpHandler;

use Ifb\TestCase;
use JsonSerializable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use stdClass;

#[CoversClass(HttpHandler::class)]
#[UsesClass(InputGenerator::class)]
#[UsesClass(OutputConverter::class)]
final class HttpHandlerTest extends TestCase
{
    #[Test]
    public function testHandle(): void
    {
        $container = $this->createMockeryMock(ContainerInterface::class);
        $handler_name = stdClass::class;

        $container->shouldReceive('has')
            ->with($handler_name)
            ->andReturn(true);

        $handler = static fn (stdClass $input): JsonSerializable => new class () implements JsonSerializable {
            /**
             * @return array<array-key, mixed>
             */
            public function jsonSerialize(): array
            {
                return [
                    'hello' => 'world',
                ];
            }
        };

        $container->shouldReceive('get')
            ->with($handler_name)
            ->andReturn($handler);

        $input_generator = $this->createMockeryMock(InputGenerator::class);
        $input_generator->shouldReceive('generateInput')
            ->andReturn(new stdClass());

        $output_converter = $this->createMockeryMock(OutputConverter::class);
        $output_converter->shouldReceive('convert')
            ->andReturn('{"hello":"world"}');

        $response_factory = $this->createMockeryMock(ResponseFactoryInterface::class);
        $response_factory->shouldReceive('createResponse')
            ->andReturn($response = $this->createMockeryMock(ResponseInterface::class));
        $stream_factory = $this->createMockeryMock(StreamFactoryInterface::class);
        $stream_factory->shouldReceive('createStream')
            ->once()
            ->with('{"hello":"world"}')
            ->andReturn($stream = $this->createMockeryMock(StreamInterface::class));
        $response->shouldReceive('withBody')
            ->once()
            ->with($stream)
            ->andReturn($response2 = $this->createMockeryMock(ResponseInterface::class));
        $response2->shouldReceive('withHeader')
            ->once()
            ->with('Content-Length', '17')
            ->andReturn($response3 = $this->createMockeryMock(ResponseInterface::class));
        $container->shouldReceive('get')
            ->with(ResponseFactoryInterface::class)
            ->andReturn($response_factory);
        $container->shouldReceive('get')
            ->with(StreamFactoryInterface::class)
            ->andReturn($stream_factory);

        $http_handler = new HttpHandler($container, $handler_name);
        $actual = $http_handler->handle($this->createMockeryMock(ServerRequestInterface::class));

        self::assertSame($response3, $actual);
    }

    #[Test]
    public function testHandlerNotFound(): void
    {
        $container = $this->createMockeryMock(ContainerInterface::class);
        $handler_name = stdClass::class;

        $container->shouldReceive('has')
            ->with($handler_name)
            ->andReturn(false);

        $this->expectException(InvalidHandlerDefinitionException::class);
        $this->expectExceptionMessage("Handler $handler_name not found in container");

        $http_handler = new HttpHandler($container, $handler_name);
        $http_handler->handle($this->createMockeryMock(ServerRequestInterface::class));
    }

    #[Test]
    public function testHandlerIsNotCallable(): void
    {
        $container = $this->createMockeryMock(ContainerInterface::class);
        $handler_name = stdClass::class;

        $container->shouldReceive('has')
            ->with($handler_name)
            ->andReturn(true);

        $container->shouldReceive('get')
            ->with($handler_name)
            ->andReturn(new stdClass());

        $this->expectException(InvalidHandlerDefinitionException::class);
        $this->expectExceptionMessage("Handler $handler_name must be callable or implement __invoke method");

        $http_handler = new HttpHandler($container, $handler_name);
        $http_handler->handle($this->createMockeryMock(ServerRequestInterface::class));
    }
}
