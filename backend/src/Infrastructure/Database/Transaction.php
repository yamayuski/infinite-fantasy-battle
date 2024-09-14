<?php

declare(strict_types=1);

/**
 * @author Masaru Yamagishi <m.yamagishi90+git@gmail.com>
 * @license MIT
 */

namespace Ifb\Infrastructure\Database;

use Ifb\Domain\Database\TransactionInterface;

/**
 * @template T of mixed
 * @implements TransactionInterface<T>
 * @package Ifb\Infrastructure\Database
 */
class Transaction implements TransactionInterface
{
    public function __construct(
    ) {}

    public function __invoke(callable $callback): mixed
    {
        return $callback();
    }
}
