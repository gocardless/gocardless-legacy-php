<?php

class GoCardlessClientException extends Exception {
	public function __construct($description = 'Unknown client error') {
		parent::__construct($description);
	}
}

class GoCardlessArgumentsException extends Exception {
	public function __construct($description = 'Unknown argument error') {
		parent::__construct($description);
	}	
}

class GoCardlessApiException extends Exception {
	public function __construct($description = 'Unknown error', $code = 0) {
		if (empty($description)) {
			$description = 'Unknown error';
		}
		parent::__construct($description, $code);
	}
}

?>