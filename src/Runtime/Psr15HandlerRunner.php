<?php

namespace App\Runtime;

use Bref\Context\Context;
use Bref\Event\Http\HttpRequestEvent;
use Bref\Event\Http\Psr7Bridge;
use Bref\Runtime\LambdaRuntime;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Runtime\RunnerInterface;

/**
 * This handler shows that one can remove the PSR-15 features from bref/bref.
 * (If one would like to).
 */
class Psr15HandlerRunner implements RunnerInterface
{
    private $handler;

    public function __construct(RequestHandlerInterface $handler)
    {
        $this->handler = $handler;
    }

    public function run(): int
    {
        $lambda = LambdaRuntime::fromEnvironmentVariable();
        $lambda->processNextEvent(function (HttpRequestEvent $event, Context $context) {
            $request = Psr7Bridge::convertRequest($event, $context);

            $response = $this->handler->handle($request);

            return Psr7Bridge::convertResponse($response);
        });

        return 0;
    }
}
