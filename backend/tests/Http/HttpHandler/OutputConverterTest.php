<?php

declare(strict_types=1);

/**
 * @license MIT
 */

namespace Ifb\Http\HttpHandler;

use Ifb\TestCase;
use JsonSerializable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(OutputConverter::class)]
final class OutputConverterTest extends TestCase
{
    #[Test]
    public function testConvert(): void
    {
        $converter = new OutputConverter();

        $data = new class implements JsonSerializable {
            /**
             * @return array<array-key, mixed>
             */
            public function jsonSerialize(): array
            {
                return [
                    'hello' => 'world',
                ];
            }
        };

        $actual = $converter->convert($data);

        self::assertSame('{"hello":"world"}', $actual);
    }

    #[Test]
    public function testConvertString(): void
    {
        $converter = new OutputConverter();

        $data = 'hello';

        $actual = $converter->convert($data);

        self::assertSame('hello', $actual);
    }

    #[Test]
    public function testNotJsonable(): void
    {
        $converter = new OutputConverter();

        $data = new class {
        };

        $this->expectException(InvalidHandlerDefinitionException::class);
        $this->expectExceptionMessage('Output class must implement JsonSerializable');

        $converter->convert($data);
    }

    #[Test]
    public function testFailedToEncodeJson(): void
    {
        $converter = new OutputConverter();

        $data = new class implements JsonSerializable {
            /**
             * @return array<array-key, mixed>
             */
            public function jsonSerialize(): array
            {
                return [
                    'hello' => "\xB1\x31",
                ];
            }
        };

        $this->expectException(InvalidHandlerDefinitionException::class);
        $this->expectExceptionMessage('Failed to encode JSON: Malformed UTF-8 characters, possibly incorrectly encoded');

        $converter->convert($data);
    }
}
