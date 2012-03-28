<?php 

class AmonZeroMQ
{

    /**
     * Make a zeromq request
     *
     * @param string $address
     * @param array  $data
     *
     * @return array
     */
    public static function request($address, array $data) 
    {
        $context = new ZMQContext();
        $requester = new ZMQSocket($context, ZMQ::SOCKET_DEALER);
        $requester->connect(sprintf("tcp://%s", $address));
        $requester->send(json_encode($data));

    }

}


