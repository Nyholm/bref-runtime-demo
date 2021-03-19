<?php

namespace App;

use Bref\Context\Context;
use Bref\Event\Handler;

class MyHandler implements Handler
{
    public function handle($event, Context $context)
    {
        return 'Super cool handler form the container!';
    }
}
