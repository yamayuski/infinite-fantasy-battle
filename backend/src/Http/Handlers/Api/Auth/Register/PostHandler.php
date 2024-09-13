<?php

declare(strict_types=1);

/**
 * @author Masaru Yamagishi <m.yamagishi90+git@gmail.com>
 * @license MIT
 */

namespace Ifb\Http\Handlers\Api\Auth\Register;

final readonly class PostHandler
{
    public function __invoke(PostInput $input): PostOutput
    {
        return new PostOutput('dummy_token');
    }
}
