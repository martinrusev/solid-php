<?php

// Set the exception handler
require dirname(__FILE__)."/../amon.php";
Amon::config(array('address'=> 'http://127.0.0.1:2465', 
		'protocol' => 'http', 
		'secret_key' => "u6ljlx2glnf8xq45ut1etkpxghmjpe3e"));
Amon::setup_exception_handler();

error_reporting(E_ALL);

//// Trigger exception
$math = 1 / 0;

// Logging
Amon::log("test");
// Tagged logging
Amon::log("test tags", array('debug', 'benchmark'));

?>
