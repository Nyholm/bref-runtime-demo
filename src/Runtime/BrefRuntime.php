<?php

namespace App\Runtime;

use Bref\Event\Handler;
use Bref\Event\Http\Psr15Handler;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Runtime\RunnerInterface;
use Symfony\Component\Runtime\SymfonyRuntime;

class BrefRuntime extends SymfonyRuntime
{
    public function getRunner(?object $application): RunnerInterface
    {
        if ($application instanceof HttpKernelInterface) {
            $application = new SymfonyHttpHandler($application);
        }

        if ($application instanceof RequestHandlerInterface) {
            $application = new Psr15Handler($application);
        }

        if ($application instanceof ContainerInterface) {
            $handler = getenv('_HANDLER');
            // TODO error checking
            [$script, $service] = explode(':', $handler);
            $application = $application->get($service);
        }

        if ($application instanceof Handler) {
            return new BrefRunner($application);
        }

        return parent::getRunner($application);
    }
}
