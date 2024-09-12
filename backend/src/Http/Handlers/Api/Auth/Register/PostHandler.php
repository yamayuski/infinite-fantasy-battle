<?php

declare(strict_types=1);

/**
 * @author Masaru Yamagishi <akai_inu@live.jp>
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
