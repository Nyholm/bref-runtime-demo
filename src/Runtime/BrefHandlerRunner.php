<?php

namespace App\Runtime;

use Bref\Event\Handler;
use Bref\Runtime\LambdaRuntime;
use Symfony\Component\Runtime\RunnerInterface;

class BrefHandlerRunner implements RunnerInterface
{
    private $handler;

    public function __construct(Handler $handler)
    {
        $this->handler = $handler;
    }

    public function run(): int
    {
        $lambda = LambdaRuntime::fromEnvironmentVariable();
        $lambda->processNextEvent($this->handler);

        return 0;
    }
}
