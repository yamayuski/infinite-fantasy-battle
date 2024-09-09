<?php

declare(strict_types=1);

/**
 * @license MIT
 */

namespace Ifb\Http\Cors;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

#[CoversClass(CorsSetting::class)]
#[CoversClass(RequestValidationResult::class)]
#[CoversClass(RequestValidator::class)]
final class RequestValidatorTest extends TestCase
{
    /**
     * @return iterable<string, array{string[], CorsSetting, RequestValidationResult}>
     */
    public static function getData(): iterable
    {
        yield 'No host header' => [[], new CorsSetting('http://a.com'), RequestValidationResult::ORIGIN_NOT_FOUND];

        yield 'Multiple host header' => [['a.com', 'b.com'], new CorsSetting('http://a.com'), RequestValidationResult::ORIGIN_NOT_FOUND];

        yield 'Same origin' => [['http://a.com'], new CorsSetting('http://a.com'), RequestValidationResult::SAME_ORIGIN];

        yield 'Invalid origin' => [['http:// a .com'], new CorsSetting('http://c.com', ['http://a.com']), RequestValidationResult::ORIGIN_NOT_ALLOWED];

        yield 'Origin not allowed' => [['http://a.com'], new CorsSetting('http://c.com', ['http://b.com']), RequestValidationResult::ORIGIN_NOT_ALLOWED];

        yield 'Origin not allowed2' => [['http://a.com'], new CorsSetting('http://c.com', ['https://a.com']), RequestValidationResult::ORIGIN_NOT_ALLOWED];

        yield 'Valid' => [['http://a.com'], new CorsSetting('http://c.com', ['http://a.com']), RequestValidationResult::VALID_CROSS_ORIGIN];
    }

    /**
     * @param string[] $origin_headers
     * @param CorsSetting $setting
     * @param RequestValidationResult $expected
     */
    #[DataProvider('getData')]
    #[Test]
    public function testValidate(array $origin_headers, CorsSetting $setting, RequestValidationResult $expected): void
    {
        $validator = new RequestValidator($setting);

        $request = $this->createMock(ServerRequestInterface::class);

        $request->expects(self::once())
            ->method('getHeader')
            ->with('Origin')
            ->willReturn($origin_headers);

        $actual = $validator->validate($request);

        self::assertSame($expected, $actual);
    }

    #[Test]
    public function testInvalidHost(): void
    {
        $validator = new RequestValidator(new CorsSetting('http://// a .com'));

        $request = $this->createMock(ServerRequestInterface::class);

        $request->expects(self::once())
            ->method('getHeader')
            ->with('Origin')
            ->willReturn(['http://a.com']);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid server origin setting provided: http://// a .com');
        $validator->validate($request);
    }
}
