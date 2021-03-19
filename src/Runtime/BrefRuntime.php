<?php

namespace App\Runtime;

use Bref\Event\Handler;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Runtime\ResolverInterface;
use Symfony\Component\Runtime\RunnerInterface;
use Symfony\Component\Runtime\SymfonyRuntime;

class BrefRuntime extends SymfonyRuntime
{
    public function getRunner(?object $application): RunnerInterface
    {
        if ($application instanceof HttpKernelInterface) {
            $application = new SymfonyHttpHandler($application);
        }

        if ($application instanceof Handler) {
            return new BrefHandlerRunner($application);
        }

        if ($application instanceof RequestHandlerInterface) {
            return new Psr15HandlerRunner($application);
        }

        return parent::getRunner($application);
    }

    public function getResolver($callable, \ReflectionFunction $reflector = null): ResolverInterface
    {
        if ($callable instanceof Handler || $callable instanceof RequestHandlerInterface) {
            return new class($callable) implements ResolverInterface {
                private $app;

                public function __construct($app)
                {
                    $this->app = $app;
                }

                public function resolve(): array
                {
                    return [$this->app, []];
                }
            };
        }

        return parent::getResolver($callable, $reflector);
    }
}
