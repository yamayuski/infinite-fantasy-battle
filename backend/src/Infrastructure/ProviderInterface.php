<?php

declare(strict_types=1);

/**
 * @author Masaru Yamagishi <m.yamagishi90+git@gmail.com>
 * @license MIT
 */

namespace Ifb\Infrastructure;

use Ifb\Infrastructure\Config\ConfigInterface;
use Shibare\Contracts\Container;

interface ProviderInterface
{
    public function provide(Container $container, ConfigInterface $config): void;
}
