<?php

class AmonRemote
{
	function __construct()
	{
		$amon_conf = file_get_contents('/etc/amon.conf');	
		$to_json = json_decode($amon_conf);
		$this->host = ( $to_json->web_app->host == NULL ) ? 'http://127.0.0.1': $to_json->web_app->host;

		// Check if the host is IP address and add http if necessary
		if (substr($this->host, 0, 7) != 'http://') {
			$this->host = sprintf("http://%s", $this->host);
		}

		$this->port = ( $to_json->web_app->port == NULL ) ? 2464 : $to_json->web_app->port;

		$this->url = sprintf("%s:%d", $this->host, $this->port);

	}

}


class Amon
{
	/**
	 * Make request
	 *
	 * @param string $url
	 * @param array  $data
	 * @param string $refer
	 *
	 * @return array
	 * @throws \InvalidArgumentException On unsupported scheme.
	 * @throws \RuntimeException When communication to server fails.
	 */
	public static function request($url, array $data, $referer='') 
	{

		if (substr($url, 0, 7) != 'http://') {
			throw new \InvalidArgumentException("Only http:// is supported.");
		}
	
		$params = array(
			'http' => array(
				'method'  => 'POST',
				'content' => json_encode($data),
				'timeout' => 5,
			)
		);


		$context = stream_context_create($params);

		$fp = @fopen($url, 'rb', false, $context);	 

		if (!$fp) {
			return array(
				'status' => 'err', 
				'error'  => "$errstr ($errno)"
			);
		}
		
		$response = @stream_get_contents($fp);

		if ($response === false) {
			throw new \RuntimeException("Problem sending POST to {$url}, $php_errormsg");
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

    /**
     * Log!
     *
     * @param string $message
     * @param string $level
     *
     * @return void
     */
	public static function log($message, $tags)
	{
		$data = array(
			'message' => $message,
			'tags'  => $tags
		);

		$remote = new AmonRemote();
		$log_url = sprintf("%s/api/log", $remote->url);

		self::request($log_url, $data);
	}
}

//Amon::log('test me', array('test', 'debug'));
