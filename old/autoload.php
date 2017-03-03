<?php
/**
 * Autoloader for CImage and related class files.
 *
 */
require_once __DIR__ . "/defines.php";
require_once __DIR__ . "/functions.php";



/**
 * Autoloader for classes.
 *
 * @param string $class the fully-qualified class name.
 *
 * @return void
 */
spl_autoload_register(function ($class) {
    $path = __DIR__ . "/src/CImage/{$class}.php";
    if (is_file($path)) {
        require($path);
    }
});
