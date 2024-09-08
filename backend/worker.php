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

    $handler = new RoutingHandler();
    $handler->get('/', new class () implements RequestHandlerInterface {
        public function handle(ServerRequestInterface $request): ResponseInterface
        {
            return new Response();
        }
    }, []);

    $dispatcher->serve($logger, $handler);
})();
