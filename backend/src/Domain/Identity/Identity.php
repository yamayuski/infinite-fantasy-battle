<?php

declare(strict_types=1);

/**
 * @author Masaru Yamagishi <m.yamagishi90+git@gmail.com>
 * @license MIT
 */

namespace Ifb\Domain\Identity;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Stringable;

/**
 * @template TEntity of object
 * @package Ifb\Domain\Identity
 */
readonly class Identity implements Stringable
{
    public function __construct(
        public readonly UuidInterface $id,
    ) {}

    public function __toString(): string
    {
        return $this->id->__toString();
    }

    /**
     * @return self<TEntity>
     */
    public static function create(): self
    {
        /** @var Identity<TEntity> $self */
        $self = new self(Uuid::uuid7());

        return $self;
    }

    /**
     * @param string $value
     * @return self<TEntity>
     */
    public static function castValue(string $value): self
    {
        /** @var Identity<TEntity> $self */
        $self = new self(Uuid::fromString($value));

        return $self;
    }
}
