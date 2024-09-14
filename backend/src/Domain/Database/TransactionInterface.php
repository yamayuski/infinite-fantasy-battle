<?php

declare(strict_types=1);

/**
 * @author Masaru Yamagishi <m.yamagishi90+git@gmail.com>
 * @license MIT
 */

namespace Ifb\Domain\Database;

/**
 * @template T of mixed
 * @package Ifb\Domain\Database
 */
interface TransactionInterface
{
    /**
     * Execute database transaction within callback
     * @param callable $callback
     * @phpstan-param callable(mixed[] ...$args): T $callback
     * @return mixed
     * @phpstan-return T
     */
    public function __invoke(callable $callback): mixed;
}
