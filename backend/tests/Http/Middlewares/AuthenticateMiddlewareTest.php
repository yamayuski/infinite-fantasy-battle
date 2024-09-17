<?php

declare(strict_types=1);

/**
 * @license MIT
 */

namespace Ifb\Http\Middlewares;

use Cycle\ORM\ORMInterface;
use Cycle\ORM\RepositoryInterface;
use Ifb\Domain\Account\AccountEntity;
use Ifb\TestCase;
use LogicException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Server\RequestHandlerInterface;

#[CoversClass(AuthenticateMiddleware::class)]
final class AuthenticateMiddlewareTest extends TestCase
{
    #[Test]
    public function testNoAuthorization(): void
    {
        $middleware = new AuthenticateMiddleware(
            $response_factory = $this->createMock(ResponseFactoryInterface::class),
            $stream_factory = $this->createMock(StreamFactoryInterface::class),
            $this->createStub(ORMInterface::class),
        );

        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects(self::once())
            ->method('getHeader')
            ->with('Authorization')
            ->willReturn([]);

        $expected = $this->createUnauthorized(
            $response_factory,
            $stream_factory,
        );

        $actual = $middleware->process(
            $request,
            $this->createStub(RequestHandlerInterface::class),
        );

        self::assertSame($expected, $actual);
    }

    #[Test]
    public function testNoBearerToken(): void
    {
        $middleware = new AuthenticateMiddleware(
            $response_factory = $this->createMock(ResponseFactoryInterface::class),
            $stream_factory = $this->createMock(StreamFactoryInterface::class),
            $this->createStub(ORMInterface::class),
        );

        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects(self::once())
            ->method('getHeader')
            ->with('Authorization')
            ->willReturn(['Basic auth']);

        $expected = $this->createUnauthorized(
            $response_factory,
            $stream_factory,
        );

        $actual = $middleware->process(
            $request,
            $this->createStub(RequestHandlerInterface::class),
        );

        self::assertSame($expected, $actual);
    }

    #[Test]
    public function testAccountNotFound(): void
    {
        $middleware = new AuthenticateMiddleware(
            $response_factory = $this->createMock(ResponseFactoryInterface::class),
            $stream_factory = $this->createMock(StreamFactoryInterface::class),
            $orm = $this->createMock(ORMInterface::class),
        );

        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects(self::once())
            ->method('getHeader')
            ->with('Authorization')
            ->willReturn(['Bearer token']);

        $orm->expects(self::once())
            ->method('getRepository')
            ->with(AccountEntity::class)
            ->willReturn($repo = $this->createMock(RepositoryInterface::class));
        $repo->expects(self::once())
            ->method('findOne')
            ->with(['token' => 'token'])
            ->willReturn(null);

        $expected = $this->createUnauthorized(
            $response_factory,
            $stream_factory,
        );

        $actual = $middleware->process(
            $request,
            $this->createStub(RequestHandlerInterface::class),
        );

        self::assertSame($expected, $actual);
    }

    #[Test]
    public function testInvalidAccount(): void
    {
        $middleware = new AuthenticateMiddleware(
            $this->createMock(ResponseFactoryInterface::class),
            $this->createMock(StreamFactoryInterface::class),
            $orm = $this->createMock(ORMInterface::class),
        );

        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects(self::once())
            ->method('getHeader')
            ->with('Authorization')
            ->willReturn(['Bearer token']);

        $orm->expects(self::once())
            ->method('getRepository')
            ->with(AccountEntity::class)
            ->willReturn($repo = $this->createMock(RepositoryInterface::class));
        $repo->expects(self::once())
            ->method('findOne')
            ->with(['token' => 'token'])
            ->willReturn(new \stdClass);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Unexpected AccountEntity, got stdClass');

        $middleware->process(
            $request,
            $this->createStub(RequestHandlerInterface::class),
        );
    }

    #[Test]
    public function testValidAccount(): void
    {
        $middleware = new AuthenticateMiddleware(
            $this->createMock(ResponseFactoryInterface::class),
            $this->createMock(StreamFactoryInterface::class),
            $orm = $this->createMock(ORMInterface::class),
        );

        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects(self::once())
            ->method('getHeader')
            ->with('Authorization')
            ->willReturn(['Bearer token']);

        $request->expects(self::once())
            ->method('withAttribute')
            ->with(AccountEntity::class)
            ->willReturn($request2 = $this->createStub(ServerRequestInterface::class));

        $orm->expects(self::once())
            ->method('getRepository')
            ->with(AccountEntity::class)
            ->willReturn($repo = $this->createMock(RepositoryInterface::class));
        $repo->expects(self::once())
            ->method('findOne')
            ->with(['token' => 'token'])
            ->willReturn($this->createStub(AccountEntity::class));

        $handler = $this->createMock(RequestHandlerInterface::class);

        $handler->expects(self::once())
            ->method('handle')
            ->with($request2)
            ->willReturn($response = $this->createStub(ResponseInterface::class));

        $actual = $middleware->process(
            $request,
            $handler,
        );

        self::assertSame($response, $actual);
    }

    private function createUnauthorized(
        ResponseFactoryInterface&MockObject $response_factory,
        StreamFactoryInterface&MockObject $stream_factory,
    ): ResponseInterface {
        $response_factory->expects(self::once())
            ->method('createResponse')
            ->with(401)
            ->willReturn($response1 = $this->createMock(ResponseInterface::class));

        $response1->expects(self::once())
            ->method('withHeader')
            ->with('Content-Length', '26')
            ->willReturn($response2 = $this->createMock(ResponseInterface::class));

        $stream_factory->expects(self::once())
            ->method('createStream')
            ->with('{"message":"Unauthorized"}')
            ->willReturn($stream = $this->createStub(StreamInterface::class));
        $response2->expects(self::once())
            ->method('withBody')
            ->with($stream)
            ->willReturn($response3 = $this->createMock(ResponseInterface::class));

        return $response3;
    }
}
