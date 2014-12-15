<?php
/**
 * Resize images on the fly using CImage, configuration is made in file named.
 *
 */



/**
 * Default configuration options, can be overridden in own config-file.
 *
 * @param string $msg to display.
 *
 * @return void
 */
function errorPage($msg)
{
    header("HTTP/1.0 404 Not Found");
    die('img.php say 404: ' . $msg);
}



/**
 * Custom exception handler.
 */
set_exception_handler(function ($exception) {
    errorPage("<p><b>img.php: Uncaught exception:</b> <p>" . $exception->getMessage() . "</p><pre>" . $exception->getTraceAsString(), "</pre>");
});



/**
 * Get input from query string or return default value if not set.
 *
 * @param mixed $key     as string or array of string values to look for in $_GET.
 * @param mixed $default value to return when $key is not set in $_GET.
 *
 * @return mixed value from $_GET or default value.
 */
function get($key, $default = null)
{
    if (is_array($key)) {
        foreach ($key as $val) {
            if (isset($_GET[$val])) {
                return $_GET[$val];
            }
        }
    } elseif (isset($_GET[$key])) {
        return $_GET[$key];
    }
    return $default;
}



/**
 * Get input from query string and set to $defined if defined or else $undefined.
 *
 * @param mixed $key       as string or array of string values to look for in $_GET.
 * @param mixed $defined   value to return when $key is set in $_GET.
 * @param mixed $undefined value to return when $key is not set in $_GET.
 *
 * @return mixed value as $defined or $undefined.
 */
function getDefined($key, $defined, $undefined)
{
    return get($key) === null ? $undefined : $defined;
}



/**
 * Log when verbose mode, when used without argument it returns the result.
 *
 * @param string $msg to log.
 *
 * @return void or array.
 */
function verbose($msg = null)
{
    global $verbose;
    static $log = array();

    if (!$verbose) {
        return;
    }

    if (is_null($msg)) {
        return $log;
    }

    $log[] = $msg;
}



/**
 * Get configuration options from file.
 */
$configFile = __DIR__.'/'.basename(__FILE__, '.php').'_config.php';
$config = require $configFile;

call_user_func($config['error_reporting']);



/**
 * Set default timezone if not set or if its set in the config-file.
 */
if (isset($config['default_timezone'])) {
    date_default_timezone_set($config['default_timezone']);
} else if (!ini_get('default_timezone')) {
    date_default_timezone_set('UTC');
}



/**
 * verbose, v - do a verbose dump of what happens
 */
$verbose = getDefined(array('verbose', 'v'), true, false);



/**
 * Create the class for the image.
 */
require $config['cimage_class'];

$img = new CImage();
$img->setVerbose($verbose);



/**
 * shortcut, sc - extend arguments with a constant value, defined
 * in config-file.
 */
$shortcut = get(array('shortcut', 'sc'), null);

verbose("shortcut = $shortcut");

if (isset($shortcut)
    && isset($config['shortcut'])
    && isset($config['shortcut'][$shortcut])) {
    
    parse_str($config['shortcut'][$shortcut], $get);
    verbose("shortcut-constant = {$config['shortcut'][$shortcut]}");
    $_GET = array_merge($_GET, $get);
}



/**
 * src - the source image file.
 */
$srcImage = get('src')
    or errorPage('Must set src-attribute.');


// Check for valid/invalid characters
preg_match($config['valid_filename'], $srcImage)
    or errorPage('Filename contains invalid characters.');


// Check that the image is a file below the directory 'image_path'.
if ($config['image_path_constraint']) {
    
    $pathToImage = realpath($config['image_path'] . $srcImage);
    $imageDir    = realpath($config['image_path']);

    is_file($pathToImage)
        or errorPage(
            'Source image is not a valid file, check the filename and that a 
            matching file exists on the filesystem.'
        );

    substr_compare($imageDir, $pathToImage, 0, strlen($imageDir)) == 0
        or errorPage(
            'Security constraint: Source image is not below the directory "image_path" 
            as specified in the config file img_config.php.'
        );
}


verbose("src = $srcImage");



/**
 * width, w - set target width, affecting the resulting image width, height and resize options
 */
$newWidth = get(array('width', 'w'));

// Check to replace predefined size
$sizes = call_user_func($config['size_constant']);
if (isset($sizes[$newWidth])) {
    $newWidth = $sizes[$newWidth];
}

// Support width as % of original width
if ($newWidth[strlen($newWidth)-1] == '%') {
    is_numeric(substr($newWidth, 0, -1))
        or errorPage('Width % not numeric.');
} else {
    is_null($newWidth)
        or ($newWidth > 10 && $newWidth <= $config['max_width'])
        or errorPage('Width out of range.');
}

verbose("new width = $newWidth");



/**
 * height, h - set target height, affecting the resulting image width, height and resize options
 */
$newHeight = get(array('height', 'h'));

// Check to replace predefined size
if (isset($sizes[$newHeight])) {
    $newHeight = $sizes[$newHeight];
}

// height
if ($newHeight[strlen($newHeight)-1] == '%') {
    is_numeric(substr($newHeight, 0, -1))
        or errorPage('Height % out of range.');
} else {
    is_null($newHeight)
        or ($newHeight > 10 && $newHeight <= $config['max_height'])
        or errorPage('Hight out of range.');
}

verbose("new height = $newHeight");



/**
 * aspect-ratio, ar - affecting the resulting image width, height and resize options
 */
$aspectRatio = get(array('aspect-ratio', 'ar'));

// Check to replace predefined aspect ratio
$aspectRatios = call_user_func($config['aspect_ratio_constant']);
$negateAspectRatio = ($aspectRatio[0] == '!') ? true : false;
$aspectRatio = $negateAspectRatio ? substr($aspectRatio, 1) : $aspectRatio;

if (isset($aspectRatios[$aspectRatio])) {
    $aspectRatio = $aspectRatios[$aspectRatio];
}

if ($negateAspectRatio) {
    $aspectRatio = 1 / $aspectRatio;
}

is_null($aspectRatio)
    or is_numeric($aspectRatio)
    or errorPage('Aspect ratio out of range');

verbose("aspect ratio = $aspectRatio");



/**
 * crop-to-fit, cf - affecting the resulting image width, height and resize options
 */
$cropToFit = getDefined(array('crop-to-fit', 'cf'), true, false);

verbose("crop to fit = $cropToFit");



/**
 * Set default background color from config file.
 */
if (isset($config['background_color'])) {
    $img->setDefaultBackgroundColor($config['background_color']);
    verbose("Using default background_color = {$config['background_color']}");
}



/**
 * bgColor - Default background color to use
 */
$bgColor = get(array('bgColor', 'bg-color', 'bgc'), null);

verbose("bgColor = $bgColor");



/**
 * fill-to-fit, ff - affecting the resulting image width, height and resize options
 */
$fillToFit = get(array('fill-to-fit', 'ff'), null);

verbose("fill-to-fit = $fillToFit");

if ($fillToFit !== null) {

    if (!empty($fillToFit)) {
        $bgColor   = $fillToFit;
        verbose("fillToFit changed bgColor to = $bgColor");
    }

    $fillToFit = true;
    verbose("fill-to-fit (fixed) = $fillToFit");
}



/**
 * no-ratio, nr, stretch - affecting the resulting image width, height and resize options
 */
$keepRatio = getDefined(array('no-ratio', 'nr', 'stretch'), false, true);

verbose("keep ratio = $keepRatio");



/**
 * crop, c - affecting the resulting image width, height and resize options
 */
$crop = get(array('crop', 'c'));

verbose("crop = $crop");



/**
 * area, a - affecting the resulting image width, height and resize options
 */
$area = get(array('area', 'a'));

verbose("area = $area");



/**
 * skip-original, so - skip the original image and always process a new image
 */
$useOriginal = getDefined(array('skip-original', 'so'), false, true);

verbose("use original = $useOriginal");



/**
 * no-cache, nc - skip the cached version and process and create a new version in cache.
 */
$useCache = getDefined(array('no-cache', 'nc'), false, true);

verbose("use cache = $useCache");



/**
 * quality, q - set level of quality for jpeg images
 */
$quality = get(array('quality', 'q'));

is_null($quality)
    or ($quality > 0 and $quality <= 100)
    or errorPage('Quality out of range');

verbose("quality = $quality");



/**
 * compress, co - what strategy to use when compressing png images
 */
$compress = get(array('compress', 'co'));

    
is_null($compress)
    or ($compress > 0 and $compress <= 9)
    or errorPage('Compress out of range');

verbose("compress = $compress");



/**
 * save-as, sa - what type of image to save
 */
$saveAs = get(array('save-as', 'sa'));

verbose("save as = $saveAs");



/**
 * scale, s - Processing option, scale up or down the image prior actual resize
 */
$scale = get(array('scale', 's'));

is_null($scale)
    or ($scale >= 0 and $scale <= 400)
    or errorPage('Scale out of range');

verbose("scale = $scale");



/**
 * palette, p - Processing option, create a palette version of the image
 */
$palette = getDefined(array('palette', 'p'), true, false);

verbose("palette = $palette");



/**
 * sharpen - Processing option, post filter for sharpen effect
 */
$sharpen = getDefined('sharpen', true, null);

verbose("sharpen = $sharpen");



/**
 * emboss - Processing option, post filter for emboss effect
 */
$emboss = getDefined('emboss', true, null);

verbose("emboss = $emboss");



/**
 * blur - Processing option, post filter for blur effect
 */
$blur = getDefined('blur', true, null);

verbose("blur = $blur");



/**
 * rotateBefore - Rotate the image with an angle, before processing
 */
$rotateBefore = get(array('rotateBefore', 'rotate-before', 'rb'));

is_null($rotateBefore)
    or ($rotateBefore >= -360 and $rotateBefore <= 360)
    or errorPage('RotateBefore out of range');

verbose("rotateBefore = $rotateBefore");



/**
 * rotateAfter - Rotate the image with an angle, before processing
 */
$rotateAfter = get(array('rotateAfter', 'rotate-after', 'ra', 'rotate', 'r'));

is_null($rotateAfter)
    or ($rotateAfter >= -360 and $rotateAfter <= 360)
    or errorPage('RotateBefore out of range');

verbose("rotateAfter = $rotateAfter");



/**
 * autoRotate - Auto rotate based on EXIF information
 */
$autoRotate = getDefined(array('autoRotate', 'auto-rotate', 'aro'), true, false);

verbose("autoRotate = $autoRotate");



/**
 * filter, f, f0-f9 - Processing option, post filter for various effects using imagefilter()
 */
$filters = array();
$filter = get(array('filter', 'f'));
if ($filter) {
    $filters[] = $filter;
}

for ($i = 0; $i < 10; $i++) {
    $filter = get(array("filter{$i}", "f{$i}"));
    if ($filter) {
        $filters[] = $filter;
    }
}

verbose("filters = " . print_r($filters, 1));



/**
 * json - output the image as a JSON object with details on the image.
 */
$outputFormat = getDefined('json', 'json', null);

verbose("json = $outputFormat");



/**
 * dpr - change to get larger image to easier support larger dpr, such as retina.
 */
$dpr = get(array('ppi', 'dpr', 'device-pixel-ratio'), 1);

verbose("dpr = $dpr");



/**
 * convolve - image convolution as in http://php.net/manual/en/function.imageconvolution.php
 */
$convolve = get('convolve', null);

// Check if the convolve is matching an existing constant 
if ($convolve && isset($config['convolution_constant'])) {
    $img->addConvolveExpressions($config['convolution_constant']);
    verbose("convolve constant = " . print_r($config['convolution_constant'], 1));
}

verbose("convolve = " . print_r($convolve, 1));



/**
 * no-upscale, nu - Do not upscale smaller image to larger dimension.
 */
$upscale = getDefined(array('no-upscale', 'nu'), false, true);

verbose("upscale = $upscale");



/**
 * Display image if verbose mode
 */
if ($verbose) {
    $query = array();
    parse_str($_SERVER['QUERY_STRING'], $query);
    unset($query['verbose']);
    unset($query['v']);
    unset($query['nocache']);
    unset($query['nc']);
    unset($query['json']);
    $url1 = '?' . htmlentities(urldecode(http_build_query($query)));
    $url2 = '?' . urldecode(http_build_query($query));
    echo <<<EOD
<a href=$url1><code>$url1</code></a><br>
<img src='{$url1}' />
<pre id="json"></pre>
<script src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
<script type="text/javascript">
window.getDetails = function (url, id) {
  $.getJSON(url, function(data) {
    element = document.getElementById(id);
    element.innerHTML = "filename: " + data.filename + "\\ncolors: " + data.colors + "\\nsize: " + data.size + "\\nwidth: " + data.width + "\\nheigh: " + data.height + "\\naspect-ratio: " + data.aspectRatio;
  });
}
</script>
<script type="text/javascript">window.getDetails("{$url2}&json", "json")</script>
EOD;
}



/**
 * Load, process and output the image
 */
$img->log("Incoming arguments: " . print_r(verbose(), 1))
    ->setSource($srcImage, $config['image_path'])
    ->setOptions(
        array(
            // Options for calculate dimensions
            'newWidth'  => $newWidth,
            'newHeight' => $newHeight,
            'aspectRatio' => $aspectRatio,
            'keepRatio' => $keepRatio,
            'cropToFit' => $cropToFit,
            'fillToFit' => $fillToFit,
            'crop'      => $crop,
            'area'      => $area,
            'upscale'   => $upscale,

            // Pre-processing, before resizing is done
            'scale'        => $scale,
            'rotateBefore' => $rotateBefore,
            'autoRotate'  => $autoRotate,

            // General processing options
            'bgColor'    => $bgColor,

            // Post-processing, after resizing is done
            'palette'   => $palette,
            'filters'   => $filters,
            'sharpen'   => $sharpen,
            'emboss'    => $emboss,
            'blur'      => $blur,
            'convolve'  => $convolve,
            'rotateAfter' => $rotateAfter,

            // Output format
            'outputFormat' => $outputFormat,
            'dpr'          => $dpr,
        )
    )
    ->loadImageDetails()
    ->initDimensions()
    ->calculateNewWidthAndHeight()
    ->setSaveAsExtension($saveAs)
    ->setJpegQuality($quality)
    ->setPngCompression($compress)
    ->useOriginalIfPossible($useOriginal)
    ->generateFilename($config['cache_path'])
    ->useCacheIfPossible($useCache)
    ->load()
    ->preResize()
    ->resize()
    ->postResize()
    ->setPostProcessingOptions($config['postprocessing'])
    ->save()
    ->output();
