<?php
/**
 * Resize images on the fly using cache.
 * 
 */
error_reporting(-1);
set_time_limit(20);

// Append ending slash
$pathToImages = __DIR__.'/img/';
$pathToCache = __DIR__.'/cache/';

// Get input from querystring
$srcImage = isset($_GET['src']) ? $pathToImages . basename($_GET['src']) : null;
$newWidth = isset($_GET['width'])  ? $_GET['width']  : (isset($_GET['w'])  ? $_GET['w']  : null);
$newHeight = isset($_GET['height']) ? $_GET['height'] : (isset($_GET['h']) ? $_GET['h'] : null);
$keepRatio = isset($_GET['no-ratio']) ? false : true; // Keep Aspect Ratio?
$crop = isset($_GET['crop']) ? true : false; // Crop image?

// Do some sanity checks
!preg_match('/^[\w-\.]+$/', $srcImage) or die('Filename contains invalid characters.');
if(isset($newWidth)) {
  $newWidth < 1000 or die('To large width.');
  $newWidth > 10 or die('To small width.');
}
if(isset($newHeight)) {
  $newHeight < 1000 or die('To large height.');
  $newHeight > 10 or die('To small height.');
}

// Create the image object
require(__DIR__.'/CImage.php');
$img = new CImage($srcImage, $newWidth, $newHeight, $keepRatio, $crop, null/*$pathToCache*/);
$img->ResizeAndOutput();