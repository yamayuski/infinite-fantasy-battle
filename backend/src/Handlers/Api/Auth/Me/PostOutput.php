<?php

declare(strict_types=1);

/**
 * @author Masaru Yamagishi <m.yamagishi90+git@gmail.com>
 * @license MIT
 */

namespace Ifb\Handlers\Api\Auth\Me;

use JsonSerializable;

final readonly class PostOutput implements JsonSerializable
{
    public function __construct(
        public string $email,
    ) {}

    public function jsonSerialize(): mixed
    {
        return [
            'email' => $this->email,
        ];
    }
}
