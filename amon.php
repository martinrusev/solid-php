<?php
require_once dirname(__FILE__)."/amon/data.php";
require_once dirname(__FILE__)."/amon/errors.php";
require_once dirname(__FILE__)."/amon/http.php";
require_once dirname(__FILE__)."/amon/zeromq.php";

class Amon
{

    static $exceptions;

    static $previous_exception_handler;
    static $previous_error_handler;

    static $controller;
    static $action;
    static $config_array;

    /**
     *  Overwrite the default configuration
     *  Amon::config(array('address', 'application_key','protocol'))
     *
     */
    public static function config($array)
    {
        self::$config_array = (object)$array;
    }

    /** Create a config array with some defaults */
    private function _get_config_object()
    {
        if(empty(self::$config_array))
        {
            // Defaults
            $config = array("address" => 'http://127.0.0.1:2464',
                "protocol" => 'http');
        }
        else
        {
            $config = self::$config_array;
        }

        return $config;

    }

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
        $config = self::_get_config_object();
        $log_url = sprintf("%s/api/log", $config->address);
        
        if(property_exists($config, 'application_key')){ 
            $log_url = sprintf("%s/%s", $log_url, $config->application_key);
        }
        
        if($config->protocol == 'http') {
            AmonHTTP::request($log_url, $data);
        }
        else if($config->protocol == 'zeromq') {
            $requester = AmonZeroMQ::getInstance($config->address);
            
            $zeromq_data = array('content' => $data,
                                 'type' => 'log');
            
            if(property_exists($config, 'application_key')){
                $zeromq_data['app_key'] = $config->application_key;
            }    
            $requester->post($zeromq_data);
        }
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
        $config = self::_get_config_object();

        self::$exceptions[] = $exception;

        $data = new AmonData($exception);
        
        if($config->protocol == 'http') {

            $exception_url = sprintf("%s/api/exception", $config->address);
            if(property_exists($config, 'application_key')){ 
                $exception_url = sprintf("%s/%s", $exception_url, $config->application_key);
            }
            
            AmonHTTP::request($exception_url, $data->data);
        }
        else if($config->protocol == 'zeromq') {
            $zeromq_data = array('content'=> $data->data, 'type' => 'exception');
            if(property_exists($config, 'application_key')){ 
                $zeromq_data['app_key'] = $config->application_key;
            }    
            AmonZeroMQ::getInstance($config->address)->post($zeromq_data);
        }

        // if there's a previous exception handler, we call that as well
        if ($call_previous && self::$previous_exception_handler) {
            call_user_func(self::$previous_exception_handler, $exception);
        }
    }

}


