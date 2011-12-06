<?php
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
				'content' => json_encode($data)
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
	public static function log($message, $level)
	{
		$data = array(
			'message' => $message,
			'level'   => $level	
		);

		self::request('http://localhost:2464/api/log', $data);
	}
}

Amon::log('test me', 'debug');
