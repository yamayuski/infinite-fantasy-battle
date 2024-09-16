<?php

declare(strict_types=1);

/**
 * @author Masaru Yamagishi <m.yamagishi90+git@gmail.com>
 * @license MIT
 */

namespace Ifb\Providers;

use Ifb\Infrastructure\Config\ConfigInterface;
use Ifb\Infrastructure\ProviderInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use RuntimeException;
use Shibare\Contracts\Container;
use Shibare\Contracts\HttpServer\RouteResolverInterface;
use Shibare\HttpFactory\RequestFactory;
use Shibare\HttpFactory\ResponseFactory;
use Shibare\HttpFactory\ServerRequestFactory;
use Shibare\HttpFactory\StreamFactory;
use Shibare\HttpFactory\UploadedFileFactory;
use Shibare\HttpFactory\UriFactory;
use Shibare\HttpServer\Middlewares\Cors\CorsConfig;
use Shibare\HttpServer\Route\Route;
use Shibare\HttpServer\Route\RouteResolver;

class HttpProvider implements ProviderInterface
{
    public function provide(Container $container, ConfigInterface $config): void
    {
        $container->bind(RequestFactoryInterface::class, RequestFactory::class);
        $container->bind(ResponseFactoryInterface::class, ResponseFactory::class);
        $container->bind(ServerRequestFactory::class, ServerRequestFactory::class);
        $container->bind(StreamFactoryInterface::class, StreamFactory::class);
        $container->bind(UploadedFileFactoryInterface::class, UploadedFileFactory::class);
        $container->bind(UriFactoryInterface::class, UriFactory::class);

        $routes_raw = $config->getArray('http.routes');
        $routes = [];
        foreach ($routes_raw as $route) {
            if (!\is_array($route) || !\array_key_exists('method', $route) || !\array_key_exists('path', $route) || !\array_key_exists('handler', $route) || !\array_key_exists('middlewares', $route)) {
                throw new RuntimeException('Invalid route config');
            }
            \assert(\is_string($route['method']));
            \assert(\is_string($route['path']));
            \assert(\is_string($route['handler']) && \class_exists($route['handler']));
            $routes[] = new Route($route['method'], $route['path'], $route['handler'], $route['middlewares']);
        }
        $route_resolver = new RouteResolver($routes);
        $container->bind(RouteResolverInterface::class, $route_resolver);

        $cors = new CorsConfig(
            server_origin: $config->getNonEmptyString('http.cors.server_origin'),
            allow_origin: $config->getNonEmptyStringArray('http.cors.allow_origin'),
            allow_methods: $config->getNonEmptyStringArray('http.cors.allow_methods'),
            allow_headers: $config->getNonEmptyStringArray('http.cors.allow_headers'),
            expose_headers: $config->getNonEmptyStringArray('http.cors.expose_headers'),
            vary: $config->getNonEmptyStringArray('http.cors.vary'),
            allow_credentials: $config->getBoolean('http.cors.allow_credentials'),
            max_age: $config->getInteger('http.cors.max_age'),
        );
        $container->bind(CorsConfig::class, $cors);
    }
}
