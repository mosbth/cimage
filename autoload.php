<?php
/**
 * Autoloader for CImage and related class files.
 *
 */
spl_autoload_register(function ($class) {
    $path = __DIR__ . "/src/CImage/{$class}.php";
    if (is_file($path)) {
        require($path);
    }
});
