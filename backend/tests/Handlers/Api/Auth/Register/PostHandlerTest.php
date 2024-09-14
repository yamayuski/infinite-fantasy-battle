<?php

declare(strict_types=1);

/**
 * @license MIT
 */

namespace Ifb\Handlers\Api\Auth\Register;

use Ifb\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(PostHandler::class)]
#[CoversClass(PostInput::class)]
#[CoversClass(PostOutput::class)]
final class PostHandlerTest extends TestCase
{
    public function testInvoke(): void
    {
        $this->markTestIncomplete();
    }
}
