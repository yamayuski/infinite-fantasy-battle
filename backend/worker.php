<?php

declare(strict_types=1);

/**
 * @license MIT
 */

require_once __DIR__ . '/vendor/autoload.php';

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Shibare\HttpServer\RoadRunnerHttpDispatcher;
use Shibare\HttpServer\RoutingHandler;
use Shibare\HttpMessage\Response;
use Shibare\Log\Formatters\JsonLineFormatter;
use Shibare\Log\Logger;
use Shibare\Log\Writers\StderrWriter;

(static function (): void {
    $dispatcher = new RoadRunnerHttpDispatcher();
    $logger = new Logger([
        new StderrWriter(new JsonLineFormatter()),
    ]);
    Logger::setInstance($logger);

    $handler = new RoutingHandler();
    $handler->options('*', new class () implements RequestHandlerInterface {
        public function handle(ServerRequestInterface $request): ResponseInterface
        {
            $headers = [
                'Access-Control-Allow-Origin' => ['https://ifb.test'],
                'Access-Control-Allow-Methods' => ['POST, GET, OPTIONS'],
                'Access-Control-Allow-Headers' => ['Content-Type, Content-Length'],
            ];
            return new Response(204, $headers);
        }
    });
    $handler->get('/', new class () implements RequestHandlerInterface {
        public function handle(ServerRequestInterface $request): ResponseInterface
        {
            $body = '{"ok":false}';
            $headers = [
                'Content-Type' => ['application/json'],
                'Content-Length' => [\strlen($body)],
                'Access-Control-Allow-Origin' => ['https://ifb.test'],
                'Access-Control-Allow-Methods' => ['POST, GET, OPTIONS'],
                'Access-Control-Allow-Headers' => ['Content-Type, Content-Length'],
            ];
            return new Response(headers: $headers, body: $body);
        }
    }, []);

    $dispatcher->serve($logger, $handler);
})();
