<?php

declare(strict_types=1);

/**
 * @license MIT
 */

namespace Ifb\Http\HttpHandler;

use Ifb\Http\HttpHandler\Stub\StubInput;
use Ifb\Http\HttpHandler\Stub\StubInput2;
use Ifb\Http\HttpHandler\Stub\StubInput3;
use Ifb\Http\HttpHandler\Stub\StubInput4;
use Ifb\Http\HttpHandler\Stub\StubInput5;
use Ifb\Http\HttpHandler\Stub\StubInput6;
use Ifb\Http\HttpHandler\Stub\StubInput7;
use Ifb\Http\HttpHandler\Stub\StubInput8;
use Ifb\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
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

    #[Test]
    public function testClassNotFound(): void
    {
        $this->expectException(InvalidHandlerDefinitionException::class);
        $this->expectExceptionMessage('Input class not found InvalidClass');

        /** @phpstan-ignore argument.type */
        new InputGenerator('InvalidClass');
    }

    #[Test]
    public function testStubInput2(): void
    {
        $class_name = StubInput2::class;

        $input_generator = new InputGenerator($class_name);

        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getParsedBody')->willReturn([]);

        $actual = $input_generator->generateInput($request);

        self::assertInstanceOf($class_name, $actual);
    }

    #[Test]
    public function testStubInput3Object(): void
    {
        $class_name = StubInput3::class;

        $input_generator = new InputGenerator($class_name);

        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getParsedBody')->willReturn((object) []);

        $actual = $input_generator->generateInput($request);

        self::assertInstanceOf($class_name, $actual);
    }

    #[Test]
    public function testStubInput3(): void
    {
        $class_name = StubInput3::class;

        $input_generator = new InputGenerator($class_name);

        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getParsedBody')->willReturn([]);

        $actual = $input_generator->generateInput($request);

        self::assertInstanceOf($class_name, $actual);
    }

    #[Test]
    public function testParameterNotFound(): void
    {
        $class_name = StubInput4::class;

        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getParsedBody')->willReturn([]);

        $this->expectException(InvalidHandlerDefinitionException::class);
        $this->expectExceptionMessage('Parameter not found name');

        $input_generator = new InputGenerator($class_name);
        $input_generator->generateInput($request);
    }

    #[Test]
    public function testUnionParameterNotSupported(): void
    {
        $class_name = StubInput5::class;

        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getParsedBody')->willReturn(['name_or_age' => 'John Doe']);

        $this->expectException(InvalidHandlerDefinitionException::class);
        $this->expectExceptionMessage('Union type not supported');

        $input_generator = new InputGenerator($class_name);
        $input_generator->generateInput($request);
    }

    #[Test]
    public function testIntersectionTypeNotSupported(): void
    {
        $class_name = StubInput6::class;

        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getParsedBody')->willReturn(['intersection_type' => 'John Doe']);

        $this->expectException(InvalidHandlerDefinitionException::class);
        $this->expectExceptionMessage('Intersection type not supported');

        $input_generator = new InputGenerator($class_name);
        $input_generator->generateInput($request);
    }

    #[Test]
    public function testNoType(): void
    {
        $class_name = StubInput7::class;

        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getParsedBody')->willReturn(['mixed_type' => 'John Doe']);

        $input_generator = new InputGenerator($class_name);
        $actual = $input_generator->generateInput($request);

        self::assertInstanceOf($class_name, $actual);
        self::assertSame('John Doe', $actual->mixed_type);
    }

    /**
     * @return array<string, mixed>
     */
    public static function getValidationFailedData(): array
    {
        return [
            'int'    => ['Invalid int value: string', 'false', 1.5, 'true', 'str', ['a', 'b'], (object)['array'], null],
            'float'  => ['Invalid float value: string', 1, 'false', 'true', 'str', ['a', 'b'], (object)['array'], null],
            'bool'   => ['Invalid bool value: string', 1, 1.5, 'a', 'str', ['a', 'b'], (object)['array'], null],
            'string' => ['Invalid string value: array', 1, 1.5, true, [], ['a', 'b'], (object)['array'], null],
            'array'  => ['Invalid array value: string', 1, 1.5, true, 'str', 'a', (object)['array'], null],
            'object' => ['Invalid object value: string', 1, 1.5, true, 'str', ['a', 'b'], 'a', null],
        ];
    }

    #[Test]
    #[DataProvider('getValidationFailedData')]
    public function testValidationFailed(
        string $expected_message,
        mixed $int,
        mixed $float,
        mixed $bool,
        mixed $string,
        mixed $array,
        mixed $object,
        mixed $mixed,
    ): void {
        $class_name = StubInput8::class;

        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getParsedBody')->willReturn(compact(
            'int',
            'float',
            'bool',
            'string',
            'array',
            'object',
            'mixed',
        ));

        $this->expectException(InputParameterValidationException::class);
        $this->expectExceptionMessage($expected_message);

        $input_generator = new InputGenerator($class_name);
        $input_generator->generateInput($request);
    }

    #[Test]
    public function testValidNamedValues(): void
    {
        $class_name = StubInput8::class;

        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getParsedBody')->willReturn([
            'int' => 1,
            'float' => 1.5,
            'bool' => true,
            'string' => 'str',
            'array' => ['a', 'b'],
            'object' => (object)['array'],
            'mixed' => null,
        ]);

        $input_generator = new InputGenerator($class_name);
        $actual = $input_generator->generateInput($request);

        self::assertInstanceOf($class_name, $actual);
    }
}
