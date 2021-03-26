<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use Bref\Context\Context;

class Handler implements \Bref\Event\Handler
{
    private static $count = 0;
    public function handle($event, Context $context)
    {
        if (self::$count === 1) {
            sleep(10);
        }
        return 'Hello ' . self::$count++;
    }
}

return new Handler();