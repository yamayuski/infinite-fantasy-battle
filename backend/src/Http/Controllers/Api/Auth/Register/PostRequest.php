<?php

declare(strict_types=1);

/**
 * @author Masaru Yamagishi <akai_inu@live.jp>
 * @license MIT
 */

namespace Ifb\Http\Controllers\Api\Auth\Register;

final readonly class PostRequest
{
    public function __construct(
        public string $email,
    ) {}
}
