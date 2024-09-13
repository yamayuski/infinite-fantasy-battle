<?php

declare(strict_types=1);

/**
 * @author Masaru Yamagishi <m.yamagishi90+git@gmail.com>
 * @license MIT
 */

namespace Ifb\Http\HttpHandler;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;

readonly class HttpHandler implements RequestHandlerInterface
{
    /**
     * @param ContainerInterface $container
     * @param class-string $handler_name
     * @return void
     */
    public function __construct(
        private ContainerInterface $container,
        private string $handler_name,
    ) {}

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if ($this->container->has($this->handler_name) === false) {
            throw new InvalidHandlerDefinitionException(\sprintf('Handler %s not found in container', $this->handler_name));
        }
        $handler = $this->container->get($this->handler_name);
        if (\is_callable($handler) === false) {
            throw new InvalidHandlerDefinitionException(\sprintf('Handler %s must be callable or implement __invoke method', $this->handler_name));
        }
        $input_generator = new InputGenerator($this->handler_name);
        $input = $input_generator->generateInput($request);
        $output = $handler($input);
        $output_converter = new OutputConverter();
        $result = $output_converter->convert($output);

        $response_factory = $this->container->get(ResponseFactoryInterface::class);
        \assert($response_factory instanceof ResponseFactoryInterface);
        $stream_factory = $this->container->get(StreamFactoryInterface::class);
        \assert($stream_factory instanceof StreamFactoryInterface);

        $response = $response_factory->createResponse();
        $stream = $stream_factory->createStream($result);
        $length = \mb_strlen($result);

        return $response->withBody($stream)
            ->withHeader('Content-Length', \strval($length));
    }
}
