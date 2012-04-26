<?php 

class AmonZeroMQ
{

    private static $instance;

    private function __construct($address)
    {

        $context = new ZMQContext();
        $this->requester = new ZMQSocket($context, ZMQ::SOCKET_DEALER);
        $this->requester->connect(sprintf("tcp://%s", $address));
        $this->requester->setSockOpt(ZMQ::SOCKOPT_LINGER, 0);
    
    }

    public static function getInstance($address) 
    { 

     if(!self::$instance) { 
       self::$instance = new self($address); 
     } 

     return self::$instance; 

   }

    /**
     * Make a zeromq request
     *
     * @param string $address
     * @param array  $data
     *
     * @return array
     */
    public function post(array $data) 
    {
        $this->requester->send(json_encode($data), ZMQ::MODE_NOBLOCK);
    }

}


