<?php
require_once dirname(__FILE__)."/amon/config.php";
require_once dirname(__FILE__)."/amon/data.php";
require_once dirname(__FILE__)."/amon/errors.php";
require_once dirname(__FILE__)."/amon/remote.php";

class Amon
{

    static $exceptions;

    static $previous_exception_handler;
    static $previous_error_handler;

    static $controller;
    static $action;

    /**
     * Log!
     *
     * @param string $message
     * @param string $level
     *
     * @return void
     */
	public static function log($message, $tags='')
	{
		$data = array(
			'message' => $message,
			'tags'  => $tags
		);

		$config = new AmonConfig();
		$log_url = sprintf("%s/api/log", $config->url);
		
		AmonRemote::request($log_url, $data);
	}

	static function setup_exception_handler() 
	{

        self::$exceptions = array();
        self::$action = "";
        self::$controller = "";

        // set exception handler & keep old exception handler around
        self::$previous_exception_handler = set_exception_handler(
            array("Amon", "handle_exception")
        );

        self::$previous_error_handler = set_error_handler(
            array("Amon", "handle_error")
        );

        register_shutdown_function(
            array("Amon", "shutdown")
        );
    }

	
	static function shutdown() 
	{
		if ($e = error_get_last()) 
		{
            self::handle_error($e["type"], $e["message"], $e["file"], $e["line"]);
        }
    }

	static function handle_error($errno, $errstr, $errfile, $errline)
	{
        if (!(error_reporting() & $errno)) {
            return;
        }

        switch ($errno) {
            case E_NOTICE:
            case E_USER_NOTICE:
                $ex = new PhpNotice($errstr, $errno, $errfile, $errline);
                break;

            case E_WARNING:
            case E_USER_WARNING:
                $ex = new PhpWarning($errstr, $errno, $errfile, $errline);
                break;

            case E_STRICT:
                $ex = new PhpStrict($errstr, $errno, $errfile, $errline);
                break;

            case E_PARSE:
                $ex = new PhpParse($errstr, $errno, $errfile, $errline);
                break;

            default:
                $ex = new PhpError($errstr, $errno, $errfile, $errline);
        }

        self::handle_exception($ex, false);

        if (self::$previous_error_handler) {
            call_user_func(self::$previous_error_handler, $errno, $errstr, $errfile, $errline);
        }
    }


    /*
     * Exception handle class. Pushes the current exception onto the exception
     * stack and calls the previous handler, if it exists. Ensures seamless
     * integration.
     */
	static function handle_exception($exception, $call_previous = true) 
	{
		$config = new AmonConfig();
		$exception_url = sprintf("%s/api/exception", $config->url);
        self::$exceptions[] = $exception;

		$data = new AmonData($exception);
		AmonRemote::request($exception_url, $data->data);

        // if there's a previous exception handler, we call that as well
        if ($call_previous && self::$previous_exception_handler) {
            call_user_func(self::$previous_exception_handler, $exception);
        }
    }

}


