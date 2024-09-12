<?php

declare(strict_types=1);

/**
 * @author Masaru Yamagishi <akai_inu@live.jp>
 * @license MIT
 */

namespace Ifb\Http\Handlers\Api\Auth\Register;

final readonly class PostInput
{
    public function __construct(
        public string $email,
    ) {}
}
