<?php

declare(strict_types=1);

/**
 * @author Masaru Yamagishi <m.yamagishi90+git@gmail.com>
 * @license MIT
 */

namespace Ifb\Handlers\Api\Auth\Register;

use JsonSerializable;

final readonly class PostOutput implements JsonSerializable
{
    public function __construct(
        public string $password,
    ) {}

    public function jsonSerialize(): mixed
    {
        return [
            'password' => $this->password,
        ];
    }
}
