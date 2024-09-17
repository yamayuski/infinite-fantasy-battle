<?php

declare(strict_types=1);

/**
 * @author Masaru Yamagishi <m.yamagishi90+git@gmail.com>
 * @license MIT
 */

namespace Ifb\Handlers\Api\Auth\Login;

use Ifb\UseCase\Account\LoginAccount;

final readonly class PostHandler
{
    public function __construct(
        private LoginAccount $usecase,
    ) {}

    public function __invoke(PostInput $input): PostOutput
    {
        $new_token = ($this->usecase)($input->email, $input->password);
        return new PostOutput($new_token->token);
    }
}
