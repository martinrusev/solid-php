<?php

require dirname(__FILE__)."/amon.php";

$zeromq_benchmark = True;
$http_benchmark = False;

if($http_benchmark == True) {
    // Set the exception handler
    Amon::config(array('address'=> 'http://127.0.0.1:2464',
        'protocol' => 'http'));

    $time_start = microtime(true);

    for ($i = 0; $i < 10000; $i++) {
        Amon::log("test");
    }
    $time_end = microtime(true);
    $time = $time_end - $time_start;

    echo "HTTP logging: $time seconds\n";
}

if($zeromq_benchmark == True) {
    Amon::config(array('address'=> '127.0.0.1:5464',
        'protocol' => 'zeromq'));

    $time_start = microtime(true);

    for ($i = 0; $i < 10000; $i++) {
        Amon::log("test");
    }
    $time_end = microtime(true);
    $time = $time_end - $time_start;

    echo "ZeroMQ logging: $time seconds\n";

}


