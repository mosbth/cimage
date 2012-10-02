<?php
/**
 * Resize images on the fly using cache.
 *
 * @author Mikael Roos mos@dbwebb.se
 * @example http://dbwebb.se/kod-exempel/cimage/
 * @link https://github.com/mosbth/cimage
 *
 */
error_reporting(-1);
set_time_limit(20);

// Append ending slash
$cimageClassFile  = __DIR__ .'/CImage.php';
$pathToImages     = __DIR__.'/img/';
$pathToCache      = __DIR__.'/cache/';
$maxWidth = $maxHeight = 2000;

// Set areas to map constant to value, easier to use with width or height
$area = array(
  'w1' => 613,
);

// Get input from querystring
$srcImage   = isset($_GET['src'])         ? $_GET['src'] : null;
$newWidth   = isset($_GET['width'])       ? $_GET['width']  : (isset($_GET['w'])  ? $_GET['w']  : null);
$newHeight  = isset($_GET['height'])      ? $_GET['height'] : (isset($_GET['h']) ? $_GET['h'] : null);
$keepRatio  = isset($_GET['no-ratio'])    ? false : true;
$cropToFit  = isset($_GET['crop-to-fit']) ? true : false; 
$crop       = isset($_GET['crop'])        ? $_GET['crop'] : (isset($_GET['c']) ? $_GET['c'] : null);
$quality    = isset($_GET['quality'])     ? $_GET['quality'] : (isset($_GET['q']) ? $_GET['q'] : 100);

// Check to replace area
if(isset($area[$newWidth])) {
  $newWidth = $area[$newWidth];
}

// Add all filters to an array
$filters = array();
$filter = isset($_GET['filter']) ? $_GET['filter'] : (isset($_GET['f']) ? $_GET['f'] : null);
if($filter) { $filters[] = $filter; }
for($i=0; $i<10;$i++) {
  $filter = isset($_GET["filter{$i}"]) ? $_GET["filter{$i}"] : (isset($_GET["f{$i}"]) ? $_GET["f{$i}"] : null);
  if($filter) { $filters[] = $filter; }
}

// Do some sanity checks
function errorPage($msg) {
  header("Status: 404 Not Found");
  die('404: ' . $msg);
}

isset($srcImage) or errorPage('Must set src-attribute.');
preg_match('#^[a-z0-9A-Z-/_\.]+$#', $srcImage) or errorPage('Filename contains invalid characters.');
is_file($pathToImages . '/' . $srcImage) or errorPage('Imagefile does not exists.');
is_writable($pathToCache) or errorPage('Cache-directory does not exists or is not writable.');
is_null($newWidth) or ($newWidth > 10 && $newWidth <= $maxWidth) or errorPage('Width out of range.');
is_null($newHeight) or ($newHeight > 10 && $newHeight <= $maxHeight) or errorPage('Hight out of range.');
$quality >= 0 and $quality <= 100 or errorPage('Quality out of range');

// Create the image object
require($cimageClassFile);
$img = new CImage($srcImage, $pathToImages, $pathToCache);
$img->ResizeAndOutput(array('newWidth'=>$newWidth, 'newHeight'=>$newHeight, 'keepRatio'=>$keepRatio, 
                            'cropToFit'=>$cropToFit, 'quality'=>$quality,
                            'crop'=>$crop, 'filters'=>$filters,
                      ));