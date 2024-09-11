<?php

declare(strict_types=1);

/**
 * @author Masaru Yamagishi <akai_inu@live.jp>
 * @license MIT
 */

namespace Ifb\Http\Controllers\Api\Auth\Register;

final readonly class PostController
{
    public function __invoke(PostRequest $request): PostResponse
    {
        return new PostResponse('dummy_token');
    }
}
