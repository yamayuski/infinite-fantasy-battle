<?php

declare(strict_types=1);

/**
 * @license MIT
 */

namespace Ifb\Handlers;

use Ifb\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(IndexHandler::class)]
#[CoversClass(IndexInput::class)]
#[CoversClass(IndexOutput::class)]
final class IndexHandlerTest extends TestCase
{
    #[Test]
    public function testInvoke(): void
    {
        $input = new IndexInput();
        $handler = new IndexHandler();
        $output = $handler($input);

        self::assertHandler(['ok' => true], $output);
    }
}
