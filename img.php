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


// Use preprocessing of images
define('PNG_FILTER',    '/usr/local/bin/optipng -q');
define('PNG_DEFLATE',   '/usr/local/bin/pngout -q');
define('JPEG_OPTIMIZE', '/usr/local/bin/jpegtran -copy none -optimize');


// Append ending slash
$cimageClassFile  = __DIR__ .'/CImage.php';
$pathToImages     = __DIR__.'/img/';
$pathToCache      = __DIR__.'/cache/';
$maxWidth = $maxHeight = 2000;
$gridColumnWidth = 30;
$gridGutterWidth = 10;
$gridColumns     = 24;
// settings for do not largen smaller images
// settings for max image dimensions

// Set sizes to map constant to value, easier to use with width or height
$sizes = array(
  'w1' => 613,
  'w2' => 630,
);



// Add column width to $area, useful for use as predefined size for width (or height).
for($i = 1; $i <= $gridColumns; $i++) {
  $sizes['c' . $i] = ($gridColumnWidth + $gridGutterWidth) * $i - $gridGutterWidth; 
}



// Get input from querystring
$srcImage   = isset($_GET['src'])         ? $_GET['src'] : null;
$newWidth   = isset($_GET['width'])       ? $_GET['width']  : (isset($_GET['w'])  ? $_GET['w']  : null);
$newHeight  = isset($_GET['height'])      ? $_GET['height'] : (isset($_GET['h']) ? $_GET['h'] : null);
$keepRatio  = isset($_GET['no-ratio'])    ? false : true;
$cropToFit  = isset($_GET['crop-to-fit']) ? true : false; 
$area       = isset($_GET['area'])        ? $_GET['area'] : null; 
$crop       = isset($_GET['crop'])        ? $_GET['crop'] : (isset($_GET['c']) ? $_GET['c'] : null);
$quality    = isset($_GET['quality'])     ? $_GET['quality'] : (isset($_GET['q']) ? $_GET['q'] : null);
$verbose    = (isset($_GET['verbose']) || isset($_GET['v'])) ? true : false;
$useCache   = isset($_GET['no-cache'])    ? false : true;
$useOriginal = isset($_GET['skip-original']) ? false : true;
$saveAs     = isset($_GET['save-as'])     ? $_GET['save-as'] : null;
$sharpen    = isset($_GET['sharpen'])     ? true : null;
$emboss     = isset($_GET['emboss'])      ? true : null;
$blur       = isset($_GET['blur'])        ? true : null;
$palette    = isset($_GET['palette'])     ? true : null;



// Check to replace predefined size
if(isset($sizes[$newWidth])) {
  $newWidth = $sizes[$newWidth];
}
if(isset($sizes[$newHeight])) {
  $newHeight = $sizes[$newHeight];
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



// Display image if vebose mode
if($verbose) {
  $query = array();
  parse_str($_SERVER['QUERY_STRING'], $query);
  unset($query['verbose']);
  unset($query['v']);
  unset($query['nocache']);
  $url1 = '?' . http_build_query($query);
  echo <<<EOD
<a href=$url1><code>$url1</code></a><br>
<img src='{$url1}' />

EOD;
}



// Create and output the image
require($cimageClassFile);
$img = new CImage($srcImage, $pathToImages, $pathToCache);
$img->ResizeAndOutput(array(
  'newWidth'  => $newWidth, 
  'newHeight' => $newHeight, 
  'keepRatio' => $keepRatio, 
  'cropToFit' => $cropToFit, 
  'area'      => $area, 
  'quality'   => $quality,
  'crop'      => $crop, 
  'filters'   => $filters,
  'verbose'   => $verbose,
  'useCache'  => $useCache,
  'useOriginal' => $useOriginal,
  'saveAs'    => $saveAs,
  'sharpen'   => $sharpen,
  'emboss'    => $emboss,
  'blur'      => $blur,
  'palette'   => $palette,
));

