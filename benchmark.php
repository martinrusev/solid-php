<?php
// Set the exception handler
require dirname(__FILE__)."/amon.php";
Amon::config(array('address'=> 'http://127.0.0.1:2465',
    'protocol' => 'http'));

$time_start = microtime(true);

for ($i = 0; $i < 10000; $i++) {
    Amon::log("test");
}
$time_end = microtime(true);
$time = $time_end - $time_start;

echo "HTTP logging: $time seconds\n";


Amon::config(array('address'=> '127.0.0.1:5464',
    'protocol' => 'zeromq'));

$time_start = microtime(true);

for ($i = 0; $i < 10000; $i++) {
    Amon::log("test");
}
$time_end = microtime(true);
$time = $time_end - $time_start;

echo "ZeroMQ logging: $time seconds\n";


