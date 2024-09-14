<?php

declare(strict_types=1);

/**
 * @author Masaru Yamagishi <m.yamagishi90+git@gmail.com>
 * @license MIT
 */

namespace Ifb\Domain\Account;

use JsonSerializable;
use RuntimeException;
use SensitiveParameter;

class AccountNotFoundException extends RuntimeException implements JsonSerializable
{
    public function __construct(
        #[SensitiveParameter]
        public readonly string $email,
    ) {
        parent::__construct('Account not found');
    }

    public function jsonSerialize(): mixed
    {
        return [
            'message' => 'Account not found',
            'args' => [
                'email' => $this->email,
            ],
        ];
    }
}
