<?php

/**
 * GoCardless exceptions
 *
 * @package GoCardless\Exceptions
 */

/**
 * Exceptions pertaining to the client object
 *
 * @return exception
 */
class GoCardless_ClientException extends Exception {

  /**
   * Throw a default exception
   *
   * @param string $description Description of the error
   */
  public function __construct($description = 'Unknown client error') {
    parent::__construct($description);
  }

}

/**
 * Exceptions pertaining to the arguments used in a function
 *
 * @return exception
 */
class GoCardless_ArgumentsException extends Exception {

  /**
   * Throw a default exception
   *
   * @param string $description Description of the error
   */
  public function __construct($description = 'Unknown argument error') {
    parent::__construct($description);
  }

}

/**
 * Exceptions pertaining to the GoCardless API
 *
 * @return exception
 */
class GoCardless_ApiException extends Exception {

  /**
   * Throw a default exception
   *
   * @param string $description Description of the error
   * @param integer $code The returned error code
   */

  private $json;

  public function __construct($description = 'Unknown error', $code = 0, $json = null) {
    if (empty($description)) {
      $description = 'Unknown error';
    }

    $this->json = $json;

    parent::__construct($description, $code);
  }

  public function getJson() {
    return $this->json;
  }

  public function getResponse() {
    return json_decode($this->json, true);
  }

  public function getError() {
    // This is primitive way of trying to extract the errors, your mileage may
    // vary - use getJson() or getResponse() for something more robust
    $object = json_decode($this->json, true);

    // reset() gets the first element in an associative array, since we don't
    // know necessarily what the keys will be
    return reset(reset($object["errors"]));
  }

}

/**
 * Exceptions pertaining to the signature
 *
 * @return exception
 */
class GoCardless_SignatureException extends Exception {

  /**
   * Throw a default exception
   *
   * @param string $description Description of the error
   */
  public function __construct($description = 'Signature error') {
    parent::__construct($description);
  }

}
