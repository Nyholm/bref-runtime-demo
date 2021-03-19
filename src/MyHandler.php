<?php

namespace App;

use Bref\Context\Context;
use Bref\Event\Handler;

class MyHandler implements Handler
{
    private $count = 0;

    public function handle($event, Context $context)
    {
        return 'Super cool handler from the container! Call: '.(++$this->count);
    }
}
