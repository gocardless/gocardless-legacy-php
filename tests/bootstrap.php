<?php

// Load the PUPUnit Autoloader
include_once('PHPUnit/Autoload.php');

/**
 * Set error reporting and display errors settings.  You will want to change these when in production.
 */
error_reporting(E_ALL);
ini_set('display_errors', true);

include 'lib/GoCardless.php';