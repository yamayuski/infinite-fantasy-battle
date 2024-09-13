<?php

declare(strict_types=1);

/**
 * @license MIT
 */

namespace Ifb\Http\Route;

interface RouteResolverInterface
{
    /**
     * Resolve route
     * @param string $method
     * @param string $path
     * @return null|Route null if route not found
     */
    public function resolve(string $method, string $path): ?Route;
}
