<?php

declare(strict_types=1);

/**
 * @license MIT
 */

require_once __DIR__ . '/vendor/autoload.php';

use Ifb\Http\Controllers\IndexController;
use Ifb\Http\Cors\CorsMiddleware;
use Ifb\Http\Cors\CorsSetting;
use Ifb\Http\ExceptionHandlerMiddleware;
use Ifb\Http\Route;
use Ifb\Http\RouteResolver;
use Ifb\Http\RouteResolverInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Shibare\Container\Container;
use Shibare\HttpFactory\ResponseFactory;
use Shibare\HttpServer\RoadRunnerHttpDispatcher;
use Shibare\HttpServer\ServerRequestRunner;
use Shibare\Log\Formatters\JsonLineFormatter;
use Shibare\Log\Logger;
use Shibare\Log\Writers\StderrWriter;

(static function (): void {
    $dispatcher = new RoadRunnerHttpDispatcher();
    $logger = new Logger([
        new StderrWriter(new JsonLineFormatter()),
    ]);
    Logger::setInstance($logger);
    $container = new Container();
    $container->bind(LoggerInterface::class, $logger);
    $response_factory = new ResponseFactory();
    $container->bind(ResponseFactoryInterface::class, $response_factory);
    $global_middlewares = [
        ExceptionHandlerMiddleware::class,
        CorsMiddleware::class,
    ];
    $routes = [
        new Route('GET', '/', IndexController::class, []),
    ];
    $route_resolver = new RouteResolver($routes);
    $container->bind(RouteResolverInterface::class, $route_resolver);
    $container->bind(CorsSetting::class, new CorsSetting(
        server_origin: 'https://api.ifb.test',
        allow_origin: ['https://ifb.test'],
        allow_methods: ['GET', 'POST'],
        allow_headers: ['Content-Type'],
        expose_headers: [],
        vary: ['Origin'],
        allow_credentials: true,
        max_age: 5,
    ));

    $not_found_handler = new class ($response_factory) implements RequestHandlerInterface {
        public function __construct(
            private readonly ResponseFactoryInterface $response_factory,
        ) {}

        public function handle(ServerRequestInterface $request): ResponseInterface
        {
            return $this->response_factory->createResponse(404);
        }
    };

    $handler = new class ($container, $route_resolver, $not_found_handler) implements RequestHandlerInterface {
        public function __construct(
            private readonly ContainerInterface $container,
            private readonly RouteResolverInterface $route_resolver,
            private readonly RequestHandlerInterface $not_found_handler,
        ) {}

        public function handle(ServerRequestInterface $request): ResponseInterface
        {
            $route = $this->route_resolver->resolve(
                $request->getMethod(),
                $request->getUri()->getPath(),
            );

            if ($route === null) {
                return $this->not_found_handler->handle($request);
            }

            $middleware_instances = \array_map(fn (string $middleware): MiddlewareInterface => $this->container->get($middleware), $route->middlewares);

            return (new ServerRequestRunner($middleware_instances, $this->container->get($route->handler)))->handle($request);
        }
    };

    $global_middlewares_handler = new class ($container, $global_middlewares, $handler) implements RequestHandlerInterface {
        /**
         * Constructor
         * @param ContainerInterface $container
         * @param class-string[] $global_middlewares
         * @return void
         */
        public function __construct(
            private readonly ContainerInterface $container,
            private readonly array $global_middlewares,
            private readonly RequestHandlerInterface $handler,
        ) {}

        public function handle(ServerRequestInterface $request): ResponseInterface
        {
            $middleware_instances = \array_map(fn (string $middleware): MiddlewareInterface => $this->container->get($middleware), $this->global_middlewares);

            return (new ServerRequestRunner($middleware_instances, $this->handler))->handle($request);
        }
    };

    $dispatcher->serve($logger, $global_middlewares_handler);
})();
