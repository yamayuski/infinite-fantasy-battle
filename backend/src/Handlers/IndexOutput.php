<?php

declare(strict_types=1);

/**
 * @author Masaru Yamagishi <m.yamagishi90+git@gmail.com>
 * @license MIT
 */

namespace Ifb\Handlers;

use JsonSerializable;

final readonly class IndexOutput implements JsonSerializable
{
    public function __construct(public bool $ok) {}

    public function jsonSerialize(): mixed
    {
        return [
            'ok' => $this->ok,
        ];
    }
}
