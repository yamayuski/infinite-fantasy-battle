<?php

declare(strict_types=1);

/**
 * @author Masaru Yamagishi <m.yamagishi90+git@gmail.com>
 * @license MIT
 */

namespace Ifb;

use Ifb\Infrastructure\Config\ConfigInterface;
use Ifb\Infrastructure\ProviderInterface;
use LogicException;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Shibare\Container\Container;
use Shibare\HttpServer\HttpHandler\MiddlewaresHandler;
use Shibare\HttpServer\RoadRunnerHttpDispatcher;
use Shibare\HttpServer\Route\RouteHandler;

class Kernel
{
    private ?ContainerInterface $container = null;

    public function __construct(public readonly ConfigInterface $config) {}

    public function boot(): void
    {
        $container = new Container();
        $container->bind(ContainerInterface::class, $container);
        $providers = $this->config->getNonEmptyStringArray('providers');
        /** @var class-string $provider */
        foreach ($providers as $provider) {
            $class = $container->get($provider);
            if ($class instanceof ProviderInterface === false) {
                throw new RuntimeException(\sprintf('"%s" is not ProviderInterface', $provider));
            }
            $class->provide($container, $this->config);
        }
        $this->container = $container;
    }

    public function serveRoadRunner(): void
    {
        if (\is_null($this->container)) {
            throw new LogicException('call boot before serveRoadRunner');
        }
        $global_middlewares = $this->config->getNonEmptyStringArray('http.middlewares');
        $global_middlewares_handler = new MiddlewaresHandler($this->container);
        // @phpstan-ignore argument.type
        $global_middlewares_handler->setMiddlewares($global_middlewares);
        $global_middlewares_handler->setHandler(new RouteHandler($this->container));

        $logger = $this->container->get(LoggerInterface::class);
        \assert($logger instanceof LoggerInterface);
        (new RoadRunnerHttpDispatcher())->serve($logger, $global_middlewares_handler);
    }

    public function getContainer(): ?ContainerInterface
    {
        return $this->container;
    }
}
