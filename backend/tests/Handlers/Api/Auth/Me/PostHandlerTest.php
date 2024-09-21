<?php

declare(strict_types=1);

/**
 * @license MIT
 */

namespace Ifb\Handlers\Api\Auth\Me;

use Ifb\HandlerTestCase;
use JsonSerializable;
use PHPUnit\Framework\Attributes\Test;

final class PostHandlerTest extends HandlerTestCase
{
    #[Test]
    public function testInvoke(): void
    {
        $input = new PostInput();

        $account = $this->actAsUser($input);

        $output = $this->handle($input, PostHandler::class);

        self::assertInstanceOf(JsonSerializable::class, $output);
        self::assertSame(\sprintf('{"email":"%s"}', $account->email), \json_encode($output));
    }
}
