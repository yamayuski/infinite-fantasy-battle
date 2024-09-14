<?php

declare(strict_types=1);

/**
 * @author Masaru Yamagishi <m.yamagishi90+git@gmail.com>
 * @license MIT
 */

namespace Ifb\Domain\Identity;

/**
 * @template TEntity of object
 * @package Ifb\Domain\Identity
 */
readonly class Identity
{
    public function __construct(
        public readonly string $id,
    ) {}
}
