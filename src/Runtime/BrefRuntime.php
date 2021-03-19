<?php

namespace App\Runtime;

use Bref\Event\Handler;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Runtime\RunnerInterface;
use Symfony\Component\Runtime\SymfonyRuntime;

class BrefRuntime extends SymfonyRuntime
{
    public function getRunner(?object $application): RunnerInterface
    {
        if ($application instanceof HttpKernelInterface) {
            return new SymfonyKernelRunner($application);
        }

        if ($application instanceof RequestHandlerInterface) {
            return new Psr15HandlerRunner($application);
        }

        if ($application instanceof Handler) {
            return new BrefHandlerRunner($application);
        }

        return parent::getRunner($application);
    }
}
