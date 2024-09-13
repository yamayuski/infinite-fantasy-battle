<?php

declare(strict_types=1);

/**
 * @author Masaru Yamagishi <m.yamagishi90+git@gmail.com>
 * @license MIT
 */

namespace Ifb\Http\Route;

use Ifb\Http\HttpHandler\HttpHandler;
use Ifb\Http\HttpHandler\MiddlewaresHandler;
use Ifb\Http\HttpHandler\NotFoundHandlerInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RouteHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly ContainerInterface $container,
    ) {}

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $route_resolver = $this->container->get(RouteResolverInterface::class);
        \assert($route_resolver instanceof RouteResolverInterface);

        $route = $route_resolver->resolve($request->getMethod(), $request->getUri()->getPath());

        if (\is_null($route)) {
            if ($this->container->has(NotFoundHandlerInterface::class)) {
                $not_found_handler = $this->container->get(NotFoundHandlerInterface::class);
                \assert($not_found_handler instanceof NotFoundHandlerInterface);
                return $not_found_handler->handleNotFound($request);
            }
            $response_factory = $this->container->get(ResponseFactoryInterface::class);
            \assert($response_factory instanceof ResponseFactoryInterface);
            return $response_factory->createResponse(404);
        }

        $http_handler = new HttpHandler($this->container, $route->handler);

        $middlewares_handler = new MiddlewaresHandler(
            $this->container,
            $route->middlewares,
            $http_handler,
        );

        return $middlewares_handler->handle($request);
    }
}
