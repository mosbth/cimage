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
$keepRatio = isset($_GET['no-ratio']) ? false : true;
$cropToFit = isset($_GET['crop-to-fit']) ? true : false; 
$crop = isset($_GET['crop']) ? $_GET['crop'] : (isset($_GET['c']) ? $_GET['c'] : null);
$quality = isset($_GET['quality']) ? $_GET['quality'] : (isset($_GET['q']) ? $_GET['q'] : 100);

// Add all filters to an array
$filters = array();
$filter = isset($_GET['filter']) ? $_GET['filter'] : (isset($_GET['f']) ? $_GET['f'] : null);
if($filter) { $filters[] = $filter; }
for($i=0; $i<10;$i++) {
  $filter = isset($_GET["filter{$i}"]) ? $_GET["filter{$i}"] : (isset($_GET["f{$i}"]) ? $_GET["f{$i}"] : null);
  if($filter) { $filters[] = $filter; }
}

// Do some sanity checks
!preg_match('/^[\w-\.]+$/', $srcImage) or die('Filename contains invalid characters.');
is_null($newWidth) or ($newWidth > 10 && $newWidth < 1000) or die('Width out of range.');
is_null($newHeight) or ($newHeight > 10 && $newHeight < 1000) or die('Hight out of range.');
$quality >= 0 and $quality <= 100 or die('Quality out of range');

// Create the image object
require(__DIR__.'/CImage.php');
$img = new CImage($srcImage, $pathToCache);
$img->ResizeAndOutput(array('newWidth'=>$newWidth, 'newHeight'=>$newHeight, 'keepRatio'=>$keepRatio, 
                            'cropToFit'=>$cropToFit, 'quality'=>$quality,
                            'crop'=>$crop, 'filters'=>$filters,
                      ));