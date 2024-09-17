<?php

declare(strict_types=1);

/**
 * @author Masaru Yamagishi <m.yamagishi90+git@gmail.com>
 * @license MIT
 */

namespace Ifb\Domain\Account;

class AuthenticatedAccount
{
    public function __construct(
        public readonly AccountEntity $entity,
    ) {}
}
