<?php

declare(strict_types=1);

/**
 * @author Masaru Yamagishi <m.yamagishi90+git@gmail.com>
 * @license MIT
 */

namespace Ifb\Domain\Account;

use LogicException;
use RuntimeException;
use SensitiveParameter;
use Stringable;

final readonly class LoginToken implements Stringable
{
    public function __construct(
        #[SensitiveParameter]
        public string $token,
    ) {}

    public static function generateNew(int $length = 64): self
    {
        if ($length < 1) {
            throw new LogicException(\sprintf('Length too small, got %d', $length));
        }
        $token = \bin2hex(\random_bytes($length));
        if (\strlen($token) > 255) {
            throw new RuntimeException(\sprintf('Length too long, got %d', $length));
        }
        return new self($token);
    }

    public static function castValue(?string $value): ?self
    {
        if (\is_null($value)) {
            return null;
        }
        return new self($value);
    }

    public function __toString(): string
    {
        return $this->token;
    }
}
