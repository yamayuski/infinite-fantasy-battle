<?php

declare(strict_types=1);

/**
 * @license MIT
 */

namespace Ifb\Http\Cors;

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
    /**
     *
     * @param string[] $allow_origin
     * @param string[] $allow_methods
     * @param string[] $allow_headers
     * @param string[] $expose_headers
     * @param string[] $vary
     * @param bool $allow_credentials
     * @param int $max_age
     * @return void
     */
    public function __construct(
        private readonly ResponseFactoryInterface $response_factory,
        private readonly RequestValidator $validator,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
    }
}
