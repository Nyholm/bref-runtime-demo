<?php

namespace App\Runtime;

use Bref\Context\Context;
use Bref\Event\Http\HttpHandler;
use Bref\Event\Http\HttpRequestEvent;
use Bref\Event\Http\HttpResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\TerminableInterface;

class SymfonyHttpHandler extends HttpHandler
{
    private $kernel;

    public function __construct(HttpKernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    public function handleRequest(HttpRequestEvent $event, Context $context): HttpResponse
    {
        $server = [
            'SERVER_PROTOCOL' => $event->getProtocolVersion(),
            'REQUEST_METHOD' => $event->getMethod(),
            'REQUEST_TIME' => time(),
            'REQUEST_TIME_FLOAT' => microtime(true),
            'QUERY_STRING' => $event->getQueryString(),
            'DOCUMENT_ROOT' => getcwd(),
            'REQUEST_URI' => $event->getUri(),
        ];

        $headers = $event->getHeaders();
        if (isset($headers['Host'])) {
            $server['HTTP_HOST'] = $headers['Host'];
        }

        // TODO convert request better
        $request = Request::create(
            $event->getUri(),
            $event->getMethod(),
            [],
            [],
            [],
            $server,
            $event->getBody()
        );

        $response = $this->kernel->handle($request);

        if ($this->kernel instanceof TerminableInterface) {
            $this->kernel->terminate($request, $response);
        }

        return new HttpResponse($response->getContent(), $response->headers->all(), $response->getStatusCode());
    }
}
