<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use Bref\Context\Context;

class Handler implements \Bref\Event\Handler
{
    private static $count = 0;
    public function handle($event, Context $context)
    {
        return 'Hello ' . self::$count++;
    }
}

return new Handler();