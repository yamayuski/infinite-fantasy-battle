<?php

declare(strict_types=1);

/**
 * @license MIT
 */

namespace Ifb\Handlers\Api\Auth\Register;

use Ifb\HandlerTestCase;
use JsonSerializable;
use PHPUnit\Framework\Attributes\Test;

final class PostHandlerTest extends HandlerTestCase
{
    #[Test]
    public function testInvoke(): void
    {
        $output = $this->handle(new PostInput('test@example.com'), PostHandler::class);

        self::assertInstanceOf(JsonSerializable::class, $output);
    }
}
