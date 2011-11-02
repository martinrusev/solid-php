<?php
class Amon
{
	function request($url, $data, $referer='') 
	{
		$data = json_encode($data);
	 
		// parse the given URL
		$url = parse_url($url);
	 
		if ($url['scheme'] != 'http') 
		{ 
			die('Error: Only HTTP request are supported !');
		}
	 
		// extract host and path:
		$host = $url['host'];
		$path = $url['path'];
		$port = $url['port'];
	 
		$fp = fsockopen($host, $port, $errno, $errstr, 30);
	 
		if($fp)
		{
	 
			// send the request headers:
			fputs($fp, "POST $path HTTP/1.1\r\n");
			fputs($fp, "Host: $host\r\n");
	 
			if ($referer != '')
				fputs($fp, "Referer: $referer\r\n");
	 
			fputs($fp, "Content-type: application/json\r\n");
			fputs($fp, "Content-length: ". strlen($data) ."\r\n");
			fputs($fp, "Connection: close\r\n\r\n");
			fputs($fp, $data);
	 
			$result = ''; 
			while(!feof($fp)) {
				// receive the results of the request
				$result .= fgets($fp, 128);
			}
	   }
	   else
	   { 
			return array(
				'status' => 'err', 
				'error' => "$errstr ($errno)"
			);
		}
	 
		// close the socket connection:
		fclose($fp);
	 
		// split the result header from the content
		$result = explode("\r\n\r\n", $result, 2);
	 
		$header = isset($result[0]) ? $result[0] : '';
		$content = isset($result[1]) ? $result[1] : '';
	 
		// return as structured array:
		return array(
			'status' => 'ok',
			'header' => $header,
			'content' => $content
		);
	}

	static function log($message, $level)
	{
		$data = array(
			'message' => $message,
			'level' => $level	
		);

		self::request('http://localhost:2464/api/log', $data);
	}
}

Amon::log('test me', 'debug');
