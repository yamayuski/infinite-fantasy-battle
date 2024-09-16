<?php

declare(strict_types=1);

/**
 * @author Masaru Yamagishi <m.yamagishi90+git@gmail.com>
 * @license MIT
 */

namespace Ifb\Providers;

use Ifb\Infrastructure\Config\ConfigInterface;
use Ifb\Infrastructure\ProviderInterface;
use Psr\Log\LoggerInterface;
use Shibare\Contracts\Container;
use Shibare\Log\Formatters\JsonLineFormatter;
use Shibare\Log\Logger;
use Shibare\Log\Writers\StderrWriter;

class LoggerProvider implements ProviderInterface
{
    public function provide(Container $container, ConfigInterface $config): void
    {
        $logger = new Logger([
            new StderrWriter(new JsonLineFormatter()),
        ]);
        Logger::setInstance($logger);
        $container->bind(LoggerInterface::class, $logger);
    }
}
