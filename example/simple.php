<?php

// Set the exception handler
require dirname(__FILE__)."/../amon.php";
Amon::setup_exception_handler();

error_reporting(E_ALL);

// Trigger exception
$math = 1 / 0;

// Logging
Amon::log("test");
// Tagged logging
Amon::log("test",array('debug', 'benchmark'));

?>
