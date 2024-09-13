<?php

declare(strict_types=1);

/**
 * @author Masaru Yamagishi <m.yamagishi90+git@gmail.com>
 * @license MIT
 */

namespace Ifb\Http\HttpHandler;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Shibare\HttpServer\ServerRequestRunner;

class MiddlewaresHandler implements RequestHandlerInterface
{
    /**
     * @param ContainerInterface $container
     * @param class-string[] $middlewares
     * @param RequestHandlerInterface $handler
     * @return void
     */
    public function __construct(
        private readonly ContainerInterface $container,
        private readonly array $middlewares,
        private readonly RequestHandlerInterface $handler,
    ) {}

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $middleware_instances = [];
        foreach ($this->middlewares as $middleware) {
            $instance = $this->container->get($middleware);
            if ($instance instanceof MiddlewareInterface === false) {
                throw new InvalidHandlerDefinitionException('Middleware must implement MiddlewareInterface: ' . $middleware);
            }
            $middleware_instances[] = $instance;
        }

        return (new ServerRequestRunner($middleware_instances, $this->handler))->handle($request);
    }
}
