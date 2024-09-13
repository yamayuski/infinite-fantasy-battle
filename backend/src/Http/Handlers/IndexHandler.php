<?php

declare(strict_types=1);

/**
 * @author Masaru Yamagishi <m.yamagishi90+git@gmail.com>
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
