<?php

declare(strict_types=1);

/**
 * @license MIT
 */

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Rayleigh\HttpMessage\Response;
use Rayleigh\HttpServer\Emitter;
use Rayleigh\HttpServer\TraditionalServerRequestBuilder;
use Rayleigh\HttpServer\ResponseEmitter;
use Rayleigh\HttpServer\ServerRequestRunner;

(function(): void {
    $middlewares = [];
    $server_request = TraditionalServerRequestBuilder::buildFromSuperGlobals();
    $handler = new class () implements RequestHandlerInterface {
        public function handle(ServerRequestInterface $request): ResponseInterface
        {
            return new Response();
        }
    };
    $server_request_runner = new ServerRequestRunner($middlewares, $handler);
    $response = $server_request_runner->handle($server_request);
    $response_emitter = new ResponseEmitter(new Emitter());
    $response_emitter->emit($response);
    $response_emitter->terminate();
})();
