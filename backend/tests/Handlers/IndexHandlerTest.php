<?php

declare(strict_types=1);

/**
 * @license MIT
 */

namespace Ifb\Handlers;

use Ifb\HandlerTestCase;
use PHPUnit\Framework\Attributes\Test;

final class IndexHandlerTest extends HandlerTestCase
{
    #[Test]
    public function testInvoke(): void
    {
        $output = $this->handle(new IndexInput(), IndexHandler::class);

        self::assertInstanceOf(IndexOutput::class, $output);
        self::assertTrue($output->ok);
    }
}
