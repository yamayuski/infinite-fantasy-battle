<?php

declare(strict_types=1);

/**
 * @license MIT
 */

return [
    'databases.default' => 'default',
    'databases.databases' => [
        'default' => [
            'connection' => 'sqlite',
        ],
    ],
    'databases.connections' => [
        'sqlite' => [
            'driver' => 'sqlite',
            'connection' => 'memory',
            'timezone' => 'Asia/Tokyo',
        ],
    ],
    'http.routes' => [
        ['method' => 'GET', 'path' => '/', 'handler' => \Ifb\Handlers\IndexHandler::class, 'middlewares' => []],
        ['method' => 'POST', 'path' => '/api/auth/register', 'handler' => \Ifb\Handlers\Api\Auth\Register\PostHandler::class, 'middlewares' => []],
        ['method' => 'POST', 'path' => '/api/auth/login', 'handler' => \Ifb\Handlers\Api\Auth\Login\PostHandler::class, 'middlewares' => []],
    ],
    'http.middlewares' => [
        \Shibare\HttpServer\Middlewares\Cors\CorsMiddleware::class,
        \Shibare\HttpServer\Middlewares\JsonRequestResponseMiddleware::class,
    ],
    'http.cors.server_origin' => 'https://api.ifb.test',
    'http.cors.allow_origin' => ['https://ifb.test'],
    'http.cors.allow_methods' => ['GET', 'POST'],
    'http.cors.allow_headers' => ['Content-Type', 'Accept'],
    'http.cors.expose_headers' => [],
    'http.cors.vary' => ['Origin'],
    'http.cors.allow_credentials' => true,
    'http.cors.max_age' => 5,
];
