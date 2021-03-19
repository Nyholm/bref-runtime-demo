<?php

namespace App\Runtime;

use Bref\Event\Handler;
use Bref\Runtime\LambdaRuntime;
use Symfony\Component\Runtime\RunnerInterface;

class BrefRunner implements RunnerInterface
{
    private $handler;
    private $loopMax;

    public function __construct(Handler $handler, int $loopMax)
    {
        $this->handler = $handler;
        $this->loopMax = $loopMax;
    }

    public function run(): int
    {
        $lambda = LambdaRuntime::fromEnvironmentVariable();

        $loops = 0;
        while (true) {
            if (++$loops > $this->loopMax) {
                return 0;
            }
            $lambda->processNextEvent($this->handler);
        }
    }
}
