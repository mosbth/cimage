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

// Do some sanity checks
function errorPage($msg) {
  header("Status: 404 Not Found");
  die('404: ' . $msg);
}

// Custom exception handler
function myExceptionHandler($exception) {
  errorPage("<p><b>img.php: Uncaught exception:</b> <p>" . $exception->getMessage() . "</p><pre>" . $exception->getTraceAsString(), "</pre>");
}
set_exception_handler('myExceptionHandler');


// Use preprocessing of images
define('PNG_FILTER',    '/usr/local/bin/optipng -q');
define('PNG_DEFLATE',   '/usr/local/bin/pngout -q');
define('JPEG_OPTIMIZE', '/usr/local/bin/jpegtran -copy none -optimize');


// Append ending slash
$cimageClassFile  = __DIR__.'/CImage.php'; // Where is the class file
$pathToImages     = __DIR__.'/img/';       // Where are the image base directory
$pathToCache      = __DIR__.'/cache/';     // Where is the cache directory
$gridColumnWidth = 30;
$gridGutterWidth = 10;
$gridColumns     = 24;
// settings for do not largen smaller images

// settings for max image dimensions
$maxWidth = $maxHeight = 2000;
$maxScale = 400;

// Set sizes to map constant to value, easier to use with width or height
$sizes = array(
  'w1' => 613,
  'w2' => 630,
);

// Predefine some common aspect ratios
$aspectRatios = array(
  '3:1' => 3/1,
  '3:2' => 3/2,
  '4:3' => 4/3,
  '8:5' => 8/5,
  '16:10' => 16/10,
  '16:9' => 16/9,
  'golden' => 1.618,
);



// Add column width to $area, useful for use as predefined size for width (or height).
for($i = 1; $i <= $gridColumns; $i++) {
  $sizes['c' . $i] = ($gridColumnWidth + $gridGutterWidth) * $i - $gridGutterWidth; 
}



// Get input from querystring
$srcImage     = isset($_GET['src'])           ? $_GET['src']          : null;
$newWidth     = isset($_GET['width'])         ? $_GET['width']        : (isset($_GET['w'])  ? $_GET['w']  : null);
$newHeight    = isset($_GET['height'])        ? $_GET['height']       : (isset($_GET['h'])  ? $_GET['h'] : null);
$aspectRatio  = isset($_GET['aspect-ratio'])  ? $_GET['aspect-ratio'] : (isset($_GET['ar']) ? $_GET['ar'] : null);
$scale        = isset($_GET['scale'])         ? $_GET['scale']        : (isset($_GET['s'])  ? $_GET['s'] : null);
$area         = isset($_GET['area'])          ? $_GET['area']         : (isset($_GET['a'])  ? $_GET['a'] : null); 
$crop         = isset($_GET['crop'])          ? $_GET['crop']         : (isset($_GET['c'])  ? $_GET['c'] : null);
$quality      = isset($_GET['quality'])       ? $_GET['quality']      : (isset($_GET['q'])  ? $_GET['q'] : null);
$deflate      = isset($_GET['deflate'])       ? $_GET['deflate']      : (isset($_GET['d'])  ? $_GET['d'] : null);
$saveAs       = isset($_GET['save-as'])       ? $_GET['save-as']      : (isset($_GET['sa']) ? $_GET['sa'] : null);
$sharpen      = isset($_GET['sharpen'])       ? true : null;
$emboss       = isset($_GET['emboss'])        ? true : null;
$blur         = isset($_GET['blur'])          ? true : null;
$palette      = isset($_GET['palette'])       || isset($_GET['p'])  ? true : false;
$verbose      = isset($_GET['verbose'])       || isset($_GET['v'])  ? true : false;
$useCache     = isset($_GET['no-cache'])      || isset($_GET['nc']) ? false : true;
$useOriginal  = isset($_GET['skip-original']) || isset($_GET['so']) ? false : true;
$keepRatio    = isset($_GET['no-ratio'])      ? false : (isset($_GET['nr']) ? false : (isset($_GET['stretch']) ? false : true ));
$cropToFit    = isset($_GET['crop-to-fit'])   ? true  : (isset($_GET['cf']) ? true : false); 



// Check to replace predefined size
if(isset($sizes[$newWidth])) {
  $newWidth = $sizes[$newWidth];
}
if(isset($sizes[$newHeight])) {
  $newHeight = $sizes[$newHeight];
}

// Check to replace predefined aspect ratio
$negateAspectRatio = ($aspectRatio[0] == '!') ? true : false;
$aspectRatio = $negateAspectRatio ? substr($aspectRatio, 1) : $aspectRatio;

if(isset($aspectRatios[$aspectRatio])) {
  $aspectRatio = $aspectRatios[$aspectRatio];
}

if($negateAspectRatio) {
  $aspectRatio = 1 / $aspectRatio;
}



// Add all filters to an array
$filters = array();
$filter = isset($_GET['filter']) ? $_GET['filter'] : (isset($_GET['f']) ? $_GET['f'] : null);
if($filter) { $filters[] = $filter; }
for($i=0; $i<10;$i++) {
  $filter = isset($_GET["filter{$i}"]) ? $_GET["filter{$i}"] : (isset($_GET["f{$i}"]) ? $_GET["f{$i}"] : null);
  if($filter) { $filters[] = $filter; }
}

// Santize and check domain for incoming parameters. (Move to CImage)
isset($srcImage) or errorPage('Must set src-attribute.');
preg_match('#^[a-z0-9A-Z-/_\.]+$#', $srcImage) or errorPage('Filename contains invalid characters.');
is_file($pathToImages . '/' . $srcImage) or errorPage('Imagefile does not exists.');
is_writable($pathToCache) or errorPage('Cache-directory does not exists or is not writable.');
is_null($quality) or ($quality > 0 and $quality <= 100) or errorPage('Quality out of range');
is_null($deflate) or ($defalte > 0 and $deflate <= 9) or errorPage('Deflate out of range');
is_null($scale) or ($scale >= 0 and $quality <= 400) or errorPage('Scale out of range');
is_null($aspectRatio) or is_numeric($aspectRatio) or errorPage('Aspect ratio out of range');

// width
if($newWidth[strlen($newWidth)-1] == '%') {
  is_numeric(substr($newWidth, 0, -1)) or errorPage('Width % out of range.');  
}
else {
  is_null($newWidth) or ($newWidth > 10 && $newWidth <= $maxWidth) or errorPage('Width out of range.');  
}

// height
if($newHeight[strlen($newHeight)-1] == '%') {
  is_numeric(substr($newHeight, 0, -1)) or errorPage('Height % out of range.');  
}
else {
  is_null($newHeight) or ($newHeight > 10 && $newHeight <= $maxHeight) or errorPage('Hight out of range.');
}




// Display image if verbose mode
if($verbose) {
  $query = array();
  parse_str($_SERVER['QUERY_STRING'], $query);
  unset($query['verbose']);
  unset($query['v']);
  unset($query['nocache']);
  unset($query['nc']);
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
  'aspectRatio' => $aspectRatio, 
  'keepRatio' => $keepRatio, 
  'cropToFit' => $cropToFit, 
  'scale'     => $scale, 
  'area'      => $area, 
  'quality'   => $quality,
  'deflate'   => $deflate,
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

