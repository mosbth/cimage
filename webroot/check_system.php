<?php

echo 'Current PHP version: ' . phpversion() . '<br><br>';

echo 'Running on: ' . htmlentities($_SERVER['SERVER_SOFTWARE']) . '<br><br>';

$no = extension_loaded('exif') ? null : 'NOT';
echo "Extension exif is $no loaded.<br>";

$no = extension_loaded('curl') ? null : 'NOT';
echo "Extension curl is $no loaded.<br>";

$no = extension_loaded('imagick') ? null : 'NOT';
echo "Extension imagick is $no loaded.<br>";

$no = extension_loaded('gd') ? null : 'NOT';
echo "Extension gd is $no loaded.<br>";
if (!$no) {
    echo "<pre>", var_dump(gd_info()), "</pre>";
}

echo "<strong>Checking path for postprocessing tools</strong>";

echo "<br>pngquant: ";
system("which pngquant");

echo "<br>optipng: ";
system("which optipng");

echo "<br>pngout: ";
system("which pngout");

echo "<br>jpegtran: ";
system("which jpegtran");
