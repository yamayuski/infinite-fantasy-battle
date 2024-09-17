<?php

declare(strict_types=1);

/**
 * @author Masaru Yamagishi <m.yamagishi90+git@gmail.com>
 * @license MIT
 */

namespace Ifb\Handlers\Api\Auth\Register;

use Ifb\UseCase\Account\RegisterAccount;

final readonly class PostHandler
{
    public function __construct(
        private RegisterAccount $usecase,
    ) {}

    public function __invoke(PostInput $input): PostOutput
    {
        $password = ($this->usecase)($input->email);
        return new PostOutput($password->password);
    }
}
