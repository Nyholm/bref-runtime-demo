<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->import('../config/{packages}/*.yaml');
        $container->import('../config/{packages}/'.$this->environment.'/*.yaml');

        if (is_file(\dirname(__DIR__).'/config/services.yaml')) {
            $container->import('../config/services.yaml');
            $container->import('../config/{services}_'.$this->environment.'.yaml');
        } elseif (is_file($path = \dirname(__DIR__).'/config/services.php')) {
            (require $path)($container->withPath($path), $this);
        }
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import('../config/{routes}/'.$this->environment.'/*.yaml');
        $routes->import('../config/{routes}/*.yaml');

        if (is_file(\dirname(__DIR__).'/config/routes.yaml')) {
            $routes->import('../config/routes.yaml');
        } elseif (is_file($path = \dirname(__DIR__).'/config/routes.php')) {
            (require $path)($routes->withPath($path), $this);
        }
    }

    public function handle(Request $request, int $type = HttpKernelInterface::MASTER_REQUEST, bool $catch = true)
    {
        if (isset($_SERVER['LAMBDA_TASK_ROOT'])) {
           \Bref\Timeout\Timeout::enable();
        }

        return parent::handle($request, $type, $catch);
    }


    public function getLogDir()
    {
        // When on the lambda only /tmp is writeable
        if (isset($_SERVER['LAMBDA_TASK_ROOT'])) {
            return '/tmp/log/';
        }

        return parent::getLogDir();
    }

    public function getCacheDir()
    {
        // When on the lambda only /tmp is writeable
        if (isset($_SERVER['LAMBDA_TASK_ROOT'])) {
            return '/tmp/cache/'.$this->environment;
        }

        return parent::getCacheDir();
    }

    public function getBuildDir(): string
    {
        return $this->getProjectDir().'/var/cache/'.$this->environment;
    }
}
