<?php

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

return function () {
    return new class implements RequestHandlerInterface {
        public function handle(ServerRequestInterface $request): ResponseInterface
        {
            $name = $request->getQueryParams()['name'] ?? 'world';

            return new Response(200, [], "Hello $name");
        }
    };
};