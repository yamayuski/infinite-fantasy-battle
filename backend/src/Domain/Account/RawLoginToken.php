<?php

declare(strict_types=1);

/**
 * @author Masaru Yamagishi <m.yamagishi90+git@gmail.com>
 * @license MIT
 */

namespace Ifb\Domain\Account;

use RuntimeException;
use SensitiveParameter;

final readonly class RawLoginToken
{
    public function __construct(
        #[SensitiveParameter]
        public string $token,
    ) {}

    public static function generateNew(int $length = 32): self
    {
        if ($length < 16) {
            throw new RuntimeException(\sprintf('Length too small, got %d', $length));
        }
        $token = \bin2hex(\random_bytes($length));
        if (\strlen($token) > 72) {
            throw new RuntimeException(\sprintf('Length too long, got %d', $length));
        }
        return new self($token);
    }

    public function getHash(): string
    {
        return \password_hash($this->token, \PASSWORD_DEFAULT);
    }

    public function needsRehash(AccountEntity $entity): bool
    {
        return \password_needs_rehash($entity->getHashedPassword(), \PASSWORD_DEFAULT);
    }
}
