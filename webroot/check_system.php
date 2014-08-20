<?php

echo 'Current PHP version: ' . phpversion() . '<br><br>';

echo 'Running on: ' . $_SERVER['SERVER_SOFTWARE'] . '<br><br>';

$no = extension_loaded('gd') ? null : 'NOT';
echo "Extension gd is $no loaded.<br>";

if (!$no) {
    var_dump(gd_info());
}
