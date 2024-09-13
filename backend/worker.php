<?php

declare(strict_types=1);

/**
 * @license MIT
 */

require_once __DIR__ . '/vendor/autoload.php';

use Ifb\Http\Handlers\Api\Auth\Register\PostHandler;
use Ifb\Http\Handlers\IndexHandler;
use Ifb\Http\HttpHandler\MiddlewaresHandler;
use Ifb\Http\Middlewares\Cors\CorsMiddleware;
use Ifb\Http\Middlewares\Cors\CorsConfig;
use Ifb\Http\Middlewares\JsonRequestResponseMiddleware;
use Ifb\Http\Route\Route;
use Ifb\Http\Route\RouteHandler;
use Ifb\Http\Route\RouteResolver;
use Ifb\Http\Route\RouteResolverInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Log\LoggerInterface;
use Shibare\Container\Container;
use Shibare\HttpFactory\ResponseFactory;
use Shibare\HttpFactory\StreamFactory;
use Shibare\HttpServer\RoadRunnerHttpDispatcher;
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
    $response_factory = new ResponseFactory();
    $container->bind(ResponseFactoryInterface::class, $response_factory);
    $container->bind(StreamFactoryInterface::class, new StreamFactory());
    $global_middlewares = [
        CorsMiddleware::class,
        JsonRequestResponseMiddleware::class,
    ];
    $routes = [
        new Route('GET', '/', IndexHandler::class, []),
        new Route('POST', '/api/auth/register', PostHandler::class, []),
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

    $global_middlewares_handler = new MiddlewaresHandler(
        $container,
        $global_middlewares,
        new RouteHandler($container),
    );

    $dispatcher = new RoadRunnerHttpDispatcher();
    $dispatcher->serve($logger, $global_middlewares_handler);
})();
