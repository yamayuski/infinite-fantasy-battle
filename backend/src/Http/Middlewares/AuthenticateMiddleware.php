<?php

declare(strict_types=1);

/**
 * @license MIT
 */

namespace Ifb\Http\Middlewares;

use Cycle\ORM\ORMInterface;
use Ifb\Domain\Account\AccountEntity;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

readonly class AuthenticateMiddleware implements MiddlewareInterface
{
    public function __construct(
        private ResponseFactoryInterface $response_factory,
        private StreamFactoryInterface $stream_factory,
        private ORMInterface $orm,
    ) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $authorization = $request->getHeader('Authorization');
        if (\count($authorization) !== 1 || !\str_starts_with($authorization[0], 'Bearer ')) {
            return $this->handleUnauthorized();
        }
        $token = \substr($authorization[0], \strlen('Bearer '));

        // @phpstan-ignore argument.templateType
        $entity = $this->orm->getRepository(AccountEntity::class)->findOne(compact('token'));

        if (\is_null($entity)) {
            return $this->handleUnauthorized();
        }
        if ($entity instanceof AccountEntity === false) {
            throw new \LogicException('Unexpected AccountEntity, got ' . \get_debug_type($entity));
        }

        return $handler->handle($request->withAttribute(AccountEntity::class, $entity));
    }

    private function handleUnauthorized(): ResponseInterface
    {
        $body = '{"message":"Unauthorized"}';
        return $this->response_factory->createResponse(401)
            ->withHeader('Content-Length', \strval(\strlen($body)))
            ->withBody($this->stream_factory->createStream($body));
    }
}
