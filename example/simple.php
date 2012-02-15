<?php

// Set the exception handler
require dirname(__FILE__)."/../amon.php";
Amon::config(array('host'=> 'http://127.0.0.1',
         'port' => 2464,
         'application_key'=>'HpaGahguPrOfsCnMP57FivGlG5fyMRfa0eJO3EUZZRY'));
Amon::setup_exception_handler();

error_reporting(E_ALL);

// Trigger exception
$math = 1 / 0;

// Logging
Amon::log("test");
// Tagged logging
Amon::log("test",array('debug', 'benchmark'));

?>
