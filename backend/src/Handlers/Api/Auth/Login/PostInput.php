<?php

declare(strict_types=1);

/**
 * @author Masaru Yamagishi <m.yamagishi90+git@gmail.com>
 * @license MIT
 */

namespace Ifb\Handlers\Api\Auth\Login;

use Ifb\Domain\Account\RawPassword;
use SensitiveParameter;

final readonly class PostInput
{
    public RawPassword $password;

    public function __construct(
        #[SensitiveParameter]
        public string $email,
        #[SensitiveParameter]
        string $password,
    ) {
        $this->password = new RawPassword($password);
    }
}
