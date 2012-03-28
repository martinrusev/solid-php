<?php

// Set the exception handler
require dirname(__FILE__)."/../amon.php";
Amon::config(array('address'=> '127.0.0.1:5464',
          'protocol' => 'zeromq'));
Amon::setup_exception_handler();

error_reporting(E_ALL);

//// Trigger exception
$math = 1 / 0;

// Logging
//Amon::log("test");
// Tagged logging
//Amon::log("test zeromq",array('debug', 'benchmark'));

?>
