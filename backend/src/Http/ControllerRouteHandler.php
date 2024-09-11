<?php

declare(strict_types=1);

/**
 * @author Masaru Yamagishi <akai_inu@live.jp>
 * @license MIT
 */

namespace Ifb\Http;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ControllerRouteHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly ContainerInterface $container,
        private readonly Route $route,
    ) {}

    public function handle(ServerRequestInterface $request): ResponseInterface
    {

    }
}
