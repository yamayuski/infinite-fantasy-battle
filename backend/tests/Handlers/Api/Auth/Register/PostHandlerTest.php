<?php

declare(strict_types=1);

/**
 * @license MIT
 */

namespace Ifb\Handlers\Api\Auth\Register;

use Ifb\Infrastructure\Config\ArrayConfig;
use Ifb\Kernel;
use Ifb\TestCase;
use JsonSerializable;
use LogicException;

final class PostHandlerTest extends TestCase
{
    public function testInvoke(): void
    {
        $array = require_once __DIR__ . '/../../../../../config.php';
        if (!\is_array($array)) {
            throw new LogicException('Invalid config.php');
        }
        $config = new ArrayConfig($array);
        $kernel = new Kernel($config);
        $kernel->boot();
        $container = $kernel->getContainer();
        if (\is_null($container)) {
            throw new LogicException('Container not set');
        }
        $handler = $container->get(PostHandler::class);
        \assert($handler instanceof PostHandler);

        $output = $handler(new PostInput('test@example.com'));

        self::assertInstanceOf(JsonSerializable::class, $output);
    }
}
