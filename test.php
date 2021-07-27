<?php

require_once __DIR__ . '/vendor/autoload.php';

use Rx\Observable;
use React\EventLoop\Factory;
use Rx\Scheduler;

$loop = Factory::create();

//You only need to set the default scheduler once
Scheduler::setDefaultFactory(function() use($loop){
    return new Scheduler\EventLoopScheduler($loop);
});

Observable::interval(1000)
    ->take(6)
    ->flatMap(function ($i) {
        return Observable::of($i + 1);
    })
    ->subscribe(function ($e) {
        echo $e, PHP_EOL;
    });

$loop->run();

?>