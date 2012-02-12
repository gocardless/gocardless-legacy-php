<?php

/**
 * Throw an exception relating to the client object
 *
 * @return exception
 */
class GoCardlessClientException extends Exception {
	public function __construct($description = 'Unknown client error') {
		parent::__construct($description);
	}
}

/**
 * Throw an exception relating to the arguments used in a function
 *
 * @return exception
 */
class GoCardlessArgumentsException extends Exception {
	public function __construct($description = 'Unknown argument error') {
		parent::__construct($description);
	}	
}

/**
 * Throw an exception relating to the GoCardless API
 *
 * @return exception
 */
class GoCardlessApiException extends Exception {
	public function __construct($description = 'Unknown error', $code = 0) {
		if (empty($description)) {
			$description = 'Unknown error';
		}
		parent::__construct($description, $code);
	}
}

?>