<?php

declare(strict_types=1);

/**
 * @license MIT
 */

return [
    'providers' => [
        Ifb\Providers\LoggerProvider::class,
        Ifb\Providers\HttpProvider::class,
        Ifb\Providers\DatabaseProvider::class,
    ],
    'logger.writer' => \getenv('LOGGER_WRITER') ?? 'stderr',
    'logger.formatter' => 'jsonline',
    'databases.default' => 'default',
    'databases.databases' => [
        'default' => [
            'connection' => 'sqlite',
        ],
    ],
    'databases.connections' => [
        'sqlite' => [
            'driver' => 'sqlite',
            'connection' => \getenv('SQLITE_CONNECTION') ?? 'memory',
            'database' => \getenv('SQLITE_DATABASE') ?? '',
            'timezone' => 'Asia/Tokyo',
        ],
    ],
    'http.routes' => [
        ['method' => 'GET', 'path' => '/', 'handler' => \Ifb\Handlers\IndexHandler::class, 'middlewares' => []],
        ['method' => 'POST', 'path' => '/api/auth/register', 'handler' => \Ifb\Handlers\Api\Auth\Register\PostHandler::class, 'middlewares' => []],
        ['method' => 'POST', 'path' => '/api/auth/login', 'handler' => \Ifb\Handlers\Api\Auth\Login\PostHandler::class, 'middlewares' => []],
        ['method' => 'POST', 'path' => '/api/auth/me', 'handler' => \Ifb\Handlers\Api\Auth\Me\PostHandler::class, 'middlewares' => [\Ifb\Http\Middlewares\AuthenticateMiddleware::class]],
    ],
    'http.middlewares' => [
        \Shibare\HttpServer\Middlewares\Cors\CorsMiddleware::class,
        \Shibare\HttpServer\Middlewares\JsonRequestResponseMiddleware::class,
    ],
    'http.cors.server_origin' => 'https://ifb.test',
    'http.cors.allow_origin' => ['https://ifb.test'],
    'http.cors.allow_methods' => ['GET', 'POST'],
    'http.cors.allow_headers' => ['Content-Type', 'Accept'],
    'http.cors.expose_headers' => [],
    'http.cors.vary' => ['Origin'],
    'http.cors.allow_credentials' => true,
    'http.cors.max_age' => 5,
];
