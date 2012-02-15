<?php

class PhpException extends ErrorException {

	function __construct($errstr, $errno, $errfile, $errline) {
		parent::__construct($errstr, 0, $errno, $errfile, $errline);
	}

}

class PhpError extends PhpException {

}

class PhpWarning extends PhpException {
}

class PhpStrict extends PhpException {
}

class PhpParse extends PhpException {
}

class PhpNotice extends PhpException {
}
