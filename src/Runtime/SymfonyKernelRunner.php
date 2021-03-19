<?php


namespace App\Runtime;

use Bref\Context\Context;
use Bref\Event\Http\HttpRequestEvent;
use Bref\Event\Http\HttpResponse;
use Bref\Runtime\LambdaRuntime;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\TerminableInterface;
use Symfony\Component\Runtime\RunnerInterface;

class SymfonyKernelRunner implements RunnerInterface
{
    private $kernel;

    public function __construct(HttpKernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    public function run(): int
    {
        $lambda = LambdaRuntime::fromEnvironmentVariable();
        $lambda->processNextEvent(function (HttpRequestEvent $event, Context $context) {
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
        });

        return 0;
    }
}
