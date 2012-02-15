<?php 

class AmonRemote
{

    /**
     * Make request
     *
     * @param string $url
     * @param array  $data
     * @param string $refer
     *
     * @return array
     */
    public static function request($url, array $data, $referer='') 
    {

        $params = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => json_encode($data),
                'timeout' => 5,
            )
        );


        $context = stream_context_create($params);

        $fp = fopen($url, 'rb', false, $context);	 

        $response = @stream_get_contents($fp);

        if (!$fp) {
            return false;
        }

        if ($response === false) {
            $error = sprintf("Problem sending POST to %s", $url);
        }

        // split the result header from the content
        $result  = explode("\r\n\r\n", $response, 2);
        $header  = isset($result[0]) ? $result[0] : '';
        $content = isset($result[1]) ? $result[1] : '';
        
        // return as structured array:
        return array(
            'status'  => 'ok',
            'header'  => $header,
            'content' => $content
        );
    }

}

