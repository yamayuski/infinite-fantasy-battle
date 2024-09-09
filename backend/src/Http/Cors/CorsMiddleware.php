<?php

declare(strict_types=1);

/**
 * @license MIT
 */

namespace Ifb\Http\Cors;

use Ifb\Http\RouteResolverInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
 * @package Ifb\Routing
 */
final class CorsMiddleware implements MiddlewareInterface, RequestHandlerInterface
{
    public function __construct(
        private readonly ResponseFactoryInterface $response_factory,
        private readonly RequestValidator $validator,
        private readonly RouteResolverInterface $route_resolver,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if (\strtoupper($request->getMethod()) !== 'OPTIONS') {
            return $this->response_factory->createResponse(405);
        }

        $request_method = $request->getHeaderLine('Access-Control-Request-Method');
        $route = $this->route_resolver->resolve($request_method, $request->getUri()->getPath());
        if (!$route) {
            return $this->response_factory->createResponse(404);
        }

        $result = $this->validator->validate($request);

        return match ($result) {
            RequestValidationResult::ORIGIN_NOT_FOUND => $this->response_factory->createResponse(400),
            RequestValidationResult::SAME_ORIGIN => $this->response_factory->createResponse(200),
            RequestValidationResult::ORIGIN_NOT_ALLOWED => $this->response_factory->createResponse(403),
            RequestValidationResult::VALID_CROSS_ORIGIN => $this->response_factory->createResponse(204),
        };
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
    }
}
