<?php

declare(strict_types=1);

/**
 * @license MIT
 */

namespace Ifb\Handlers\Api\Auth\Login;

use Ifb\HandlerTestCase;
use PHPUnit\Framework\Attributes\Test;

final class PostHandlerTest extends HandlerTestCase
{
    #[Test]
    public function testInvoke(): void
    {
        $this->seed('accounts', [
            'id' => '9b5fef32-1109-491b-b90e-aca9668ab90e',
            'email' => 'example@ifb.test',
            'hashed_token' => \password_hash('testtest', \PASSWORD_DEFAULT),
        ]);

        $output = $this->handle(new PostInput('example@ifb.test', 'testtest'), PostHandler::class);

        self::assertInstanceOf(PostOutput::class, $output);
    }
}
