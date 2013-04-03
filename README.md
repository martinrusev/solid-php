## Install

Download the client from [https://github.com/martinrusev/amon-php](https://github.com/martinrusev/amon-php)



## Configuration


  require_once 'amon.php'
	Amon::config(array('host'=> 'http://127.0.0.1', 'port' => 2464, 
	'secret_key': 'the secret key from /etc/amonlite.conf'));


## Usage

### Logging

You can use the logging module in any PHP application:

	# message - array, string
	Amon::log(message, tags);

	# Will still work and in the web interface you will see these logs with level 'unset'
	Amon::log(message);

	# Tagged logging
	Amon::log(message, array('debug', 'info'));



### Exception handling

To capture exceptions triggered from your PHP applications, add the following 2 lines in your index.php file


	require "amon.php";
	Amon::setup_exception_handler();


