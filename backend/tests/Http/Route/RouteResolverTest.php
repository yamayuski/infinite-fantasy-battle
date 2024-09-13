<?php

declare(strict_types=1);

/**
 * @license MIT
 */

namespace Ifb\Http\Route;

use Ifb\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversClass(RouteResolver::class)]
#[UsesClass(Route::class)]
final class RouteResolverTest extends TestCase
{
    /**
     * @return array<string, array{0: Route[], 1: string, 2: string, 3: Route|null}>
     */
    public static function getRouteData(): array
    {
        $routes = [
            $route1 = new Route('GET', '/', self::class),
            $route2 = new Route('POST', '/', self::class),
            $route3 = new Route('OPTIONS', '*', self::class),
            // TODO
            // $route4 = new Route('GET', '/{id}', self::class),
            // $route5 = new Route('GET', '/{id}/edit', self::class),
            // $route6 = new Route('GET', '/{id}/edit/{name}', self::class),
        ];

        return [
            'GET /' => [$routes, 'GET', '/', $route1],
            'POST /' => [$routes, 'POST', '/', $route2],
            'OPTIONS *' => [$routes, 'OPTIONS', '*', $route3],
            // 'GET /123' => [$routes, 'GET', '/123', $route4],
            // 'GET /123/edit' => [$routes, 'GET', '/123/edit', $route5],
            // 'GET /123/edit/abc' => [$routes, 'GET', '/123/edit/abc', $route6],
            'POST /123' => [$routes, 'POST', '/123', null],
        ];
    }

    /**
     * @param Route[] $routes
     * @param string $method
     * @param string $path
     * @param null|Route $expected
     */
    #[Test]
    #[DataProvider('getRouteData')]
    public function testResolve(array $routes, string $method, string $path, ?Route $expected): void
    {
        $resolver = new RouteResolver($routes);

        $actual = $resolver->resolve($method, $path);

        self::assertSame($expected, $actual);
    }
}
