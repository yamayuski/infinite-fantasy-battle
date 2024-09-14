<?php

declare(strict_types=1);

/**
 * @author Masaru Yamagishi <m.yamagishi90+git@gmail.com>
 * @license MIT
 */

namespace Ifb\Infrastructure\Config;

use LogicException;

class ConfigNotFoundException extends LogicException
{
    public function __construct(string $key)
    {
        parent::__construct(\sprintf('Config "%s" not found or invalid', $key));
    }
}
