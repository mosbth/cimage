<?php
/**
 * Resize and crop images on the fly, store generated images in a cache.
 *
 * This version is a all-in-one version of img.php, it is not dependant an any other file
 * so you can simply copy it to any place you want it.
 *
 * @author  Mikael Roos mos@dbwebb.se
 * @example http://dbwebb.se/opensource/cimage
 * @link    https://github.com/mosbth/cimage
 *
 */
define("CIMAGE_BUNDLE", true);


/**
 * Change configuration details in the array below or create a separate file
 * where you store the configuration details.
 *
 * The configuration file should be named the same name as this file and then
 * add '_config.php'. If this file is named 'img.php' then name the
 * config file should be named 'img_config.php'.
 *
 * The settings below are only a few of the available ones. Check the file in
 * webroot/img_config.php for a complete list of configuration options.
 */
$config = array(

    //'mode'         => 'production',               // 'production', 'development', 'strict'
    //'image_path'   =>  __DIR__ . '/img/',
    //'cache_path'   =>  __DIR__ . '/../cache/',
    //'alias_path'   =>  __DIR__ . '/img/alias/',
    //'remote_allow' => true,
    //'password'     => false,                      // "secret-password",

);
