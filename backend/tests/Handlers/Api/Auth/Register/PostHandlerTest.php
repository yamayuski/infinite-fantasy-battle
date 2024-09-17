<?php

declare(strict_types=1);

/**
 * @license MIT
 */

namespace Ifb\Handlers\Api\Auth\Register;

use Ifb\Domain\Account\AccountAlreadyExistsException;
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

    #[Test]
    public function testInvokeAccountAlreadyExists(): void
    {
        $this->seed('accounts', [
            'id' => '9b5fef32-1109-491b-b90e-aca9668ab90e',
            'email' => 'test@ifb.test',
            'hashed_password' => \password_hash('testtest', \PASSWORD_DEFAULT),
        ]);

        $this->expectException(AccountAlreadyExistsException::class);

        $this->handle(new PostInput('test@ifb.test'), PostHandler::class);
    }
}
