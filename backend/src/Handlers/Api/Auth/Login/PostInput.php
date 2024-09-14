<?php

declare(strict_types=1);

/**
 * @author Masaru Yamagishi <m.yamagishi90+git@gmail.com>
 * @license MIT
 */

namespace Ifb\Handlers\Api\Auth\Login;

use Ifb\Domain\Account\RawLoginToken;
use SensitiveParameter;

final readonly class PostInput
{
    public RawLoginToken $token;

    public function __construct(
        #[SensitiveParameter]
        public string $email,
        #[SensitiveParameter]
        string $token,
    ) {
        $this->token = new RawLoginToken($token);
    }
}
