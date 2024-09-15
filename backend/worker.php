<?php

declare(strict_types=1);

/**
 * @license MIT
 */

require_once __DIR__ . '/vendor/autoload.php';

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Log\LoggerInterface;
use Shibare\Container\Container;
use Shibare\Contracts\HttpServer\RouteResolverInterface;
use Shibare\HttpFactory\ResponseFactory;
use Shibare\HttpFactory\StreamFactory;
use Shibare\HttpServer\HttpHandler\MiddlewaresHandler;
use Shibare\HttpServer\Middlewares\Cors\CorsConfig;
use Shibare\HttpServer\RoadRunnerHttpDispatcher;
use Shibare\HttpServer\Route\Route;
use Shibare\HttpServer\Route\RouteHandler;
use Shibare\HttpServer\Route\RouteResolver;
use Shibare\Log\Formatters\JsonLineFormatter;
use Shibare\Log\Logger;
use Shibare\Log\Writers\StderrWriter;

(static function (): void {
    $container = new Container();
    $container->bind(ContainerInterface::class, $container);
    $logger = new Logger([
        new StderrWriter(new JsonLineFormatter()),
    ]);
    Logger::setInstance($logger);
    $container->bind(LoggerInterface::class, $logger);
    $container->bind(ResponseFactoryInterface::class, new ResponseFactory());
    $container->bind(StreamFactoryInterface::class, new StreamFactory());
    $global_middlewares = [
        \Shibare\HttpServer\Middlewares\Cors\CorsMiddleware::class,
        \Shibare\HttpServer\Middlewares\JsonRequestResponseMiddleware::class,
    ];
    $routes = [
        new Route('GET', '/', \Ifb\Handlers\IndexHandler::class, []),
        new Route('POST', '/api/auth/register', \Ifb\Handlers\Api\Auth\Register\PostHandler::class, []),
        new Route('POST', '/api/auth/login', \Ifb\Handlers\Api\Auth\Login\PostHandler::class, []),
    ];
    $route_resolver = new RouteResolver($routes);
    $container->bind(RouteResolverInterface::class, $route_resolver);
    $container->bind(CorsConfig::class, new CorsConfig(
        server_origin: 'https://api.ifb.test',
        allow_origin: ['https://ifb.test'],
        allow_methods: ['GET', 'POST'],
        allow_headers: ['Content-Type', 'Accept'],
        expose_headers: [],
        vary: ['Origin'],
        allow_credentials: true,
        max_age: 5,
    ));

    $global_middlewares_handler = new MiddlewaresHandler($container);
    $global_middlewares_handler->setMiddlewares($global_middlewares);
    $global_middlewares_handler->setHandler(new RouteHandler($container));

    $dispatcher = new RoadRunnerHttpDispatcher();
    $dispatcher->serve($logger, $global_middlewares_handler);
})();
