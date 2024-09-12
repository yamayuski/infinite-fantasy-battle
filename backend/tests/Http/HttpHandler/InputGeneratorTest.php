<?php

declare(strict_types=1);

/**
 * @license MIT
 */

namespace Ifb\Http\HttpHandler;

use Ifb\Http\HttpHandler\Stub\StubInput;
use Ifb\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Psr\Http\Message\ServerRequestInterface;

#[CoversClass(InputGenerator::class)]
final class InputGeneratorTest extends TestCase
{
    #[Test]
    public function testStdClass(): void
    {
        $class_name = \stdClass::class;

        $input_generator = new InputGenerator($class_name);

        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getParsedBody')->willReturn([]);

        $actual = $input_generator->generateInput($request);

        self::assertInstanceOf($class_name, $actual);
    }

    #[Test]
    public function testValid(): void
    {
        $class_name = StubInput::class;

        $input_generator = new InputGenerator($class_name);

        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getParsedBody')->willReturn([
            'name' => 'John Doe',
            'age' => 20,
            'height' => 180.0,
            'is_student' => true,
            'school' => 'University',
            'hobbies' => ['reading', 'programming'],
            // 'extra' => 'extra',
        ]);

        $actual = $input_generator->generateInput($request);

        self::assertInstanceOf($class_name, $actual);
        self::assertSame('John Doe', $actual->name);
        self::assertSame(20, $actual->age);
        self::assertSame(180.0, $actual->height);
        self::assertTrue($actual->is_student);
        self::assertSame('University', $actual->school);
        self::assertSame(['reading', 'programming'], $actual->hobbies);
        self::assertNull($actual->extra);
    }
}
