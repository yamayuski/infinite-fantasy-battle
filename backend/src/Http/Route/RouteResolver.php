<?php

declare(strict_types=1);

/**
 * @license MIT
 */

namespace Ifb\Http\Route;

final class RouteResolver implements RouteResolverInterface
{
    /** @var array<string, array<string, Route>> $routes */
    public readonly array $routes;

    /**
     * Constructor
     * @param Route[] $route_list
     */
    public function __construct(
        array $route_list,
    ) {
        $routes = [];
        foreach ($route_list as $route) {
            $routes[$route->path][$route->method] = $route;
        }
        $this->routes = $routes;
    }

    public function resolve(string $method, string $path): ?Route
    {
        $method = \strtoupper($method);

        // exact match
        if (\array_key_exists($path, $this->routes) && \array_key_exists($method, $this->routes[$path])) {
            return $this->routes[$path][$method];
        }

        // placeholder match
        foreach ($this->routes as $route_path => $routes) {
            // TODO: fix
            if (\array_key_exists($method, $routes)) {
                $route = $routes[$method];
                $result = $this->resolvePathPlaceholder($route, $route_path);
                if ($result) {
                    return $route;
                }
            }
        }

        // wildcard match
        if (\array_key_exists('*', $this->routes) && \array_key_exists($method, $this->routes['*'])) {
            return $this->routes['*'][$method];
        }

        return null;
    }

    private function resolvePathPlaceholder(Route $route, string $actual_path): bool
    {
        $count = 0;
        // Replace placeholder to named capture group
        $regexp_pattern = \preg_replace('#\{(\w+)\}#', '(?P<$1>[^\/]+)', $route->path, count: $count);
        if ($count === 0) {
            return false; // no placeholder
        }

        $matches = [];
        if (\preg_match('#^' . $regexp_pattern . '$#', $actual_path, $matches)) {
            /** @var array<string, string> */
            $result = \array_filter($matches, fn(string|int $key): bool => !\is_int($key), \ARRAY_FILTER_USE_KEY);

            foreach ($result as $key => $value) {
                $route->setPathAttribute($key, $value);
            }
            return true;
        }

        return false;
    }
}
