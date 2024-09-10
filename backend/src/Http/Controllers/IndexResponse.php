<?php

declare(strict_types=1);

/**
 * @author Masaru Yamagishi <akai_inu@live.jp>
 * @license Apache-2.0
 */

namespace Ifb\Http\Controllers;

use JsonSerializable;

final readonly class IndexResponse implements JsonSerializable
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
