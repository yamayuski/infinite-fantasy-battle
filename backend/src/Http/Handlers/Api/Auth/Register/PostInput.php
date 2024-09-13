<?php

declare(strict_types=1);

/**
 * @author Masaru Yamagishi <m.yamagishi90+git@gmail.com>
 * @license MIT
 */

namespace Ifb\Http\Handlers\Api\Auth\Register;

final readonly class PostInput
{
    public function __construct(
        public string $email,
    ) {}
}
