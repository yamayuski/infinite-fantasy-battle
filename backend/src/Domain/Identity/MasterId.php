<?php

declare(strict_types=1);

/**
 * @author Masaru Yamagishi <m.yamagishi90+git@gmail.com>
 * @license MIT
 */

namespace Ifb\Domain\Identity;

use Stringable;

/**
 * @template TEntity of object
 * @package Ifb\Domain\Identity
 */
readonly class MasterId implements Stringable
{
    public function __construct(
        public string $id,
    ) {}

    public function __toString(): string
    {
        return $this->id;
    }

    /**
     * @param string $value
     * @return self<TEntity>
     */
    public static function castValue(string $value): self
    {
        /** @var MasterId<TEntity> $self */
        $self = new self($value);

        return $self;
    }
}
