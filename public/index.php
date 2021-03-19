<?php

use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    error_log(json_encode($context));
    $e = new \RuntimeException('Foo');
    error_log($e->getTraceAsString());
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};