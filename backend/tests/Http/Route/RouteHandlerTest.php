<?php

declare(strict_types=1);

/**
 * @license MIT
 */

namespace Ifb\Http\Route;

use Ifb\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(RouteResolver::class)]
final class RouteHandlerTest extends TestCase
{
    #[Test]
    public function testHandle(): void
    {
        $this->markTestIncomplete();
    }
}
