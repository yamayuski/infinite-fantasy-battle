<?php

declare(strict_types=1);

/**
 * @author Masaru Yamagishi <m.yamagishi90+git@gmail.com>
 * @license MIT
 */

namespace Ifb\Providers;

use Ifb\Infrastructure\Config\ConfigInterface;
use Ifb\Infrastructure\Config\InvalidConfigException;
use Ifb\Infrastructure\Logger\NullWriter;
use Ifb\Infrastructure\ProviderInterface;
use Psr\Log\LoggerInterface;
use Shibare\Contracts\ContainerInterface;
use Shibare\Log\Formatters\JsonLineFormatter;
use Shibare\Log\Logger;
use Shibare\Log\Writers\StderrWriter;
use Shibare\Log\Writers\StdoutWriter;

class LoggerProvider implements ProviderInterface
{
    public function provide(ContainerInterface $container, ConfigInterface $config): void
    {
        $formatter = match ($config->getNonEmptyString('logger.formatter')) {
            'jsonline' => new JsonLineFormatter(),
            default => throw new InvalidConfigException('Invalid logger.formatter'),
        };
        $writer = match($config->getNonEmptyString('logger.writer')) {
            'stderr' => new StderrWriter($formatter),
            'stdout' => new StdoutWriter($formatter),
            'null' => new NullWriter(),
            default => throw new InvalidConfigException('Invalid logger.writer'),
        };
        $logger = new Logger([$writer]);
        Logger::setInstance($logger);
        $container->bind(LoggerInterface::class, $logger);
    }
}
