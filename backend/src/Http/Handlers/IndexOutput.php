<?php

declare(strict_types=1);

/**
 * @author Masaru Yamagishi <akai_inu@live.jp>
 * @license MIT
 */

namespace Ifb\Http\Handlers;

use JsonSerializable;

final readonly class IndexOutput implements JsonSerializable
{
    public function __construct(public bool $ok)
    {
    }

    public function jsonSerialize(): mixed
    {
        return [
            'ok' => $this->ok,
        ];
    }
}
