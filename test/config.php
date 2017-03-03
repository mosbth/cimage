<?php
/**
 * Get all configuration details to be able to execute the test suite.
 *
 */
require __DIR__ . "/../vendor/autoload.php";

if (!defined("IMAGE_PATH")) {
    define("IMAGE_PATH", __DIR__ . "/../webroot/img/");
}

if (!defined("CACHE_PATH")) {
    define("CACHE_PATH", __DIR__ . "/../cache/");
}
