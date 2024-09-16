<?php

declare(strict_types=1);

/**
 * @author Masaru Yamagishi <m.yamagishi90+git@gmail.com>
 * @license MIT
 */

namespace Ifb\Infrastructure\Testing;

use JsonSerializable;
use PHPUnit\Framework\Assert;

class AssertableOutput
{
    public function __construct(
        private readonly object $output,
    ) {}

    public function assertJson(): void
    {
        Assert::assertInstanceOf(JsonSerializable::class, $this->output, 'Output is JsonSerializable');
        $json = \json_encode($this->output, \JSON_UNESCAPED_UNICODE);
        Assert::assertIsString($json);
        Assert::assertJson($json, 'Output is valid json');
    }
}
