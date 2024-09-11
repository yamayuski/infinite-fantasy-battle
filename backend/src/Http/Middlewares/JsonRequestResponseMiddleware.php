<?php

declare(strict_types=1);

/**
 * @license MIT
 */

namespace Ifb\Http\Middlewares;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Accepts only application/json and returns only application/json
 * @package Ifb\Http\Middlewares
 */
final readonly class JsonRequestResponseMiddleware implements MiddlewareInterface
{
    public function __construct(
        private ResponseFactoryInterface $response_factory,
        private StreamFactoryInterface $stream_factory,
    ) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!$request->hasHeader('Accept')) {
            return $this->createResponse(406, '{"message":"Accept header is required"}');
        }
        $accept_header = $request->getHeaderLine('Accept');
        if (!\str_starts_with($accept_header, 'application/json')) {
            return $this->createResponse(406, '{"message":"Accept header must be application/json"}');
        }
        if ($request->hasHeader('Content-Type')) {
            $content_type = $request->getHeaderLine('Content-Type');
            if (!\str_starts_with($content_type, 'application/json')) {
                return $this->createResponse(415, '{"message":"Content-Type must be application/json"}');
            }
            $parsed_body = \json_decode($request->getBody()->getContents(), true, \JSON_THROW_ON_ERROR);
            if (!\is_array($parsed_body)) {
                return $this->createResponse(400, '{"message":"Invalid JSON"}');
            }
            $request = $request->withParsedBody($parsed_body);
        }

        $response = $handler->handle($request);

        return $response
            ->withHeader('Content-Type', 'application/json; charset=UTF-8');
    }

    private function createResponse(int $status_code, string $body): ResponseInterface
    {
        return $this->response_factory->createResponse($status_code)
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('Content-Length', \strval(\strlen($body)))
            ->withBody($this->stream_factory->createStream($body));
    }
}
