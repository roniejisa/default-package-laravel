<?php
namespace ExampleRS\Listeners;

use ExampleRS\Jobs\Example;

class ExampleListener
{
    public function subscribe($events)
    {
        $events->listen('example.create', Example::class);
    }
}
