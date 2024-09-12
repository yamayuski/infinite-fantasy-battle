<?php

declare(strict_types=1);

/**
 * @author Masaru Yamagishi <akai_inu@live.jp>
 * @license MIT
 */

namespace Ifb\Http\Handlers\Api\Auth\Register;

use JsonSerializable;

final readonly class PostOutput implements JsonSerializable
{
    public function __construct(
        public string $token,
    ) {}

    public function jsonSerialize(): mixed
    {
        return [
            'token' => $this->token,
        ];
    }
}
