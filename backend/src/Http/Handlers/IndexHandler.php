<?php

declare(strict_types=1);

/**
 * @author Masaru Yamagishi <akai_inu@live.jp>
 * @license MIT
 */

namespace Ifb\Http\Handlers;

final readonly class IndexHandler
{
    public function __invoke(IndexInput $input): IndexOutput
    {
        return new IndexOutput(true);
    }
}
