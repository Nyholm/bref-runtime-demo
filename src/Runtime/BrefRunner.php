<?php

namespace App\Runtime;

use Bref\Event\Handler;
use Bref\Runtime\LambdaRuntime;
use Symfony\Component\Runtime\RunnerInterface;

class BrefRunner implements RunnerInterface
{
    private $handler;

    public function __construct(Handler $handler)
    {
        $this->handler = $handler;
    }

    public function run(): int
    {
        $lambda = LambdaRuntime::fromEnvironmentVariable();

        $loopMax = getenv('BREF_LOOP_MAX') ?: 1;
        $loops = 0;
        while (true) {
            if (++$loops > $loopMax) {
                return 0;
            }
            $lambda->processNextEvent($this->handler);
        }

        return 0;
    }
}
