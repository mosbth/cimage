<?php
/**
 * Resize and crop images on the fly, store generated images in a cache.
 *
 * @author  Mikael Roos mos@dbwebb.se
 * @example http://dbwebb.se/opensource/cimage
 * @link    https://github.com/mosbth/cimage
 *
 */

/**
 * Custom exception handler.
 */
set_exception_handler(function ($exception) {
    errorPage(
        "<p><b>img.php: Uncaught exception:</b> <p>"
        . $exception->getMessage()
        . "</p><pre>"
        . $exception->getTraceAsString()
        . "</pre>",
        500
    );
});



/**
 * Get configuration options from file, if the file exists, else use $config
 * if its defined or create an empty $config.
 */
$configFile = __DIR__.'/'.basename(__FILE__, '.php').'_config.php';

if (is_file($configFile)) {
    $config = require $configFile;
} elseif (!isset($config)) {
    $config = array();
}

// Make CIMAGE_DEBUG false by default, if not already defined
if (!defined("CIMAGE_DEBUG")) {
    define("CIMAGE_DEBUG", false);
}



/**
 * Setup the autoloader, but not when using a bundle.
 */
if (!defined("CIMAGE_BUNDLE")) {
    if (!isset($config["autoloader"])) {
        die("CImage: Missing autoloader.");
    }

    require $config["autoloader"];
}



/**
* verbose, v - do a verbose dump of what happens
* vf - do verbose dump to file
*/
$verbose = getDefined(array('verbose', 'v'), true, false);
$verboseFile = getDefined('vf', true, false);
verbose("img.php version = " . CIMAGE_VERSION);



/**
* status - do a verbose dump of the configuration
*/
$status = getDefined('status', true, false);



/**
 * Set mode as strict, production or development.
 * Default is production environment.
 */
$mode = getConfig('mode', 'production');

// Settings for any mode
set_time_limit(20);
ini_set('gd.jpeg_ignore_warning', 1);

if (!extension_loaded('gd')) {
    errorPage("Extension gd is not loaded.", 500);
}

// Specific settings for each mode
if ($mode == 'strict') {

    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    $verbose = false;
    $status = false;
    $verboseFile = false;

} elseif ($mode == 'production') {

    error_reporting(-1);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    $verbose = false;
    $status = false;
    $verboseFile = false;

} elseif ($mode == 'development') {

    error_reporting(-1);
    ini_set('display_errors', 1);
    ini_set('log_errors', 0);
    $verboseFile = false;

} elseif ($mode == 'test') {

    error_reporting(-1);
    ini_set('display_errors', 1);
    ini_set('log_errors', 0);

} else {
    errorPage("Unknown mode: $mode", 500);
}

verbose("mode = $mode");
verbose("error log = " . ini_get('error_log'));



/**
 * Set default timezone if not set or if its set in the config-file.
 */
$defaultTimezone = getConfig('default_timezone', null);

if ($defaultTimezone) {
    date_default_timezone_set($defaultTimezone);
} elseif (!ini_get('default_timezone')) {
    date_default_timezone_set('UTC');
}



/**
 * Check if passwords are configured, used and match.
 * Options decide themself if they require passwords to be used.
 */
$pwdConfig   = getConfig('password', false);
$pwdAlways   = getConfig('password_always', false);
$pwdType     = getConfig('password_type', 'text');
$pwd         = get(array('password', 'pwd'), null);

// Check if passwords match, if configured to use passwords
$passwordMatch = null;
if ($pwd) {
    switch ($pwdType) {
        case 'md5':
            $passwordMatch = ($pwdConfig === md5($pwd));
            break;
        case 'hash':
            $passwordMatch = password_verify($pwd, $pwdConfig);
            break;
        case 'text':
            $passwordMatch = ($pwdConfig === $pwd);
            break;
        default:
            $passwordMatch = false;
    }
}

if ($pwdAlways && $passwordMatch !== true) {
    errorPage("Password required and does not match or exists.", 403);
}

verbose("password match = $passwordMatch");



/**
 * Prevent hotlinking, leeching, of images by controlling who access them
 * from where.
 *
 */
$allowHotlinking = getConfig('allow_hotlinking', true);
$hotlinkingWhitelist = getConfig('hotlinking_whitelist', array());

$serverName  = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : null;
$referer     = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
$refererHost = parse_url($referer, PHP_URL_HOST);

if (!$allowHotlinking) {
    if ($passwordMatch) {
        ; // Always allow when password match
        verbose("Hotlinking since passwordmatch");
    } elseif ($passwordMatch === false) {
        errorPage("Hotlinking/leeching not allowed when password missmatch.", 403);
    } elseif (!$referer) {
        errorPage("Hotlinking/leeching not allowed and referer is missing.", 403);
    } elseif (strcmp($serverName, $refererHost) == 0) {
        ; // Allow when serverName matches refererHost
        verbose("Hotlinking disallowed but serverName matches refererHost.");
    } elseif (!empty($hotlinkingWhitelist)) {
        $whitelist = new CWhitelist();
        $allowedByWhitelist = $whitelist->check($refererHost, $hotlinkingWhitelist);

        if ($allowedByWhitelist) {
            verbose("Hotlinking/leeching allowed by whitelist.");
        } else {
            errorPage("Hotlinking/leeching not allowed by whitelist. Referer: $referer.", 403);
        }

    } else {
        errorPage("Hotlinking/leeching not allowed.", 403);
    }
}

verbose("allow_hotlinking = $allowHotlinking");
verbose("referer = $referer");
verbose("referer host = $refererHost");



/**
 * Create the class for the image.
 */
$CImage = getConfig('CImage', 'CImage');
$img = new $CImage();
$img->setVerbose($verbose || $verboseFile);



/**
 * Get the cachepath from config.
 */
$CCache = getConfig('CCache', 'CCache');
$cachePath = getConfig('cache_path', __DIR__ . '/../cache/');
$cache = new $CCache();
$cache->setDir($cachePath);



/**
 * no-cache, nc - skip the cached version and process and create a new version in cache.
 */
$useCache = getDefined(array('no-cache', 'nc'), false, true);

verbose("use cache = $useCache");



/**
 * Prepare fast track cache for swriting cache items.
 */
$fastTrackCache = "fasttrack";
$allowFastTrackCache = getConfig('fast_track_allow', false);

$CFastTrackCache = getConfig('CFastTrackCache', 'CFastTrackCache');
$ftc = new $CFastTrackCache();
$ftc->setCacheDir($cache->getPathToSubdir($fastTrackCache))
    ->enable($allowFastTrackCache)
    ->setFilename(array('no-cache', 'nc'));
$img->injectDependency("fastTrackCache", $ftc);



/**
 *  Load and output images from fast track cache, if items are available
 * in cache.
 */
if ($useCache && $allowFastTrackCache) {
    if (CIMAGE_DEBUG) {
        trace("img.php fast track cache enabled and used");
    }
    $ftc->output();
}



/**
 * Allow or disallow remote download of images from other servers.
 * Passwords apply if used.
 *
 */
$allowRemote = getConfig('remote_allow', false);

if ($allowRemote && $passwordMatch !== false) {
    $cacheRemote = $cache->getPathToSubdir("remote");

    $pattern = getConfig('remote_pattern', null);
    $img->setRemoteDownload($allowRemote, $cacheRemote, $pattern);

    $whitelist = getConfig('remote_whitelist', null);
    $img->setRemoteHostWhitelist($whitelist);
}



/**
 * shortcut, sc - extend arguments with a constant value, defined
 * in config-file.
 */
$shortcut       = get(array('shortcut', 'sc'), null);
$shortcutConfig = getConfig('shortcut', array(
    'sepia' => "&f=grayscale&f0=brightness,-10&f1=contrast,-20&f2=colorize,120,60,0,0&sharpen",
));

verbose("shortcut = $shortcut");

if (isset($shortcut)
    && isset($shortcutConfig[$shortcut])) {

    parse_str($shortcutConfig[$shortcut], $get);
    verbose("shortcut-constant = {$shortcutConfig[$shortcut]}");
    $_GET = array_merge($_GET, $get);
}



/**
 * src - the source image file.
 */
$srcImage = urldecode(get('src'))
    or errorPage('Must set src-attribute.', 404);

// Get settings for src-alt as backup image
$srcAltImage = urldecode(get('src-alt', null));
$srcAltConfig = getConfig('src_alt', null);
if (empty($srcAltImage)) {
    $srcAltImage = $srcAltConfig;
}

// Check for valid/invalid characters
$imagePath           = getConfig('image_path', __DIR__ . '/img/');
$imagePathConstraint = getConfig('image_path_constraint', true);
$validFilename       = getConfig('valid_filename', '#^[a-z0-9A-Z-/_ \.:]+$#');

// Source is remote
$remoteSource = false;

// Dummy image feature
$dummyEnabled  = getConfig('dummy_enabled', true);
$dummyFilename = getConfig('dummy_filename', 'dummy');
$dummyImage = false;

preg_match($validFilename, $srcImage)
    or errorPage('Source filename contains invalid characters.', 404);

if ($dummyEnabled && $srcImage === $dummyFilename) {

    // Prepare to create a dummy image and use it as the source image.
    $dummyImage = true;

} elseif ($allowRemote && $img->isRemoteSource($srcImage)) {

    // If source is a remote file, ignore local file checks.
    $remoteSource = true;

} else {

    // Check if file exists on disk or try using src-alt
    $pathToImage = realpath($imagePath . $srcImage);

    if (!is_file($pathToImage) && !empty($srcAltImage)) {
        // Try using the src-alt instead
        $srcImage = $srcAltImage;
        $pathToImage = realpath($imagePath . $srcImage);

        preg_match($validFilename, $srcImage)
            or errorPage('Source (alt) filename contains invalid characters.', 404);

        if ($dummyEnabled && $srcImage === $dummyFilename) {
            // Check if src-alt is the dummy image
            $dummyImage = true;
        }
    }

    if (!$dummyImage) {
        is_file($pathToImage)
            or errorPage(
                'Source image is not a valid file, check the filename and that a
                matching file exists on the filesystem.',
                404
            );
    }
}

if ($imagePathConstraint && !$dummyImage && !$remoteSource) {
    // Check that the image is a file below the directory 'image_path'.
    $imageDir = realpath($imagePath);

    substr_compare($imageDir, $pathToImage, 0, strlen($imageDir)) == 0
        or errorPage(
            'Security constraint: Source image is not below the directory "image_path"
            as specified in the config file img_config.php.',
            404
        );
}

verbose("src = $srcImage");



/**
 * Manage size constants from config file, use constants to replace values
 * for width and height.
 */
$sizeConstant = getConfig('size_constant', function () {

    // Set sizes to map constant to value, easier to use with width or height
    $sizes = array(
        'w1' => 613,
        'w2' => 630,
    );

    // Add grid column width, useful for use as predefined size for width (or height).
    $gridColumnWidth = 30;
    $gridGutterWidth = 10;
    $gridColumns     = 24;

    for ($i = 1; $i <= $gridColumns; $i++) {
        $sizes['c' . $i] = ($gridColumnWidth + $gridGutterWidth) * $i - $gridGutterWidth;
    }

    return $sizes;
});

$sizes = call_user_func($sizeConstant);



/**
 * width, w - set target width, affecting the resulting image width, height and resize options
 */
$newWidth     = get(array('width', 'w'));
$maxWidth     = getConfig('max_width', 2000);

// Check to replace predefined size
if (isset($sizes[$newWidth])) {
    $newWidth = $sizes[$newWidth];
}

// Support width as % of original width
if ($newWidth && $newWidth[strlen($newWidth)-1] == '%') {
    is_numeric(substr($newWidth, 0, -1))
        or errorPage('Width % not numeric.', 404);
} else {
    is_null($newWidth)
        or ($newWidth > 10 && $newWidth <= $maxWidth)
        or errorPage('Width out of range.', 404);
}

verbose("new width = $newWidth");



/**
 * height, h - set target height, affecting the resulting image width, height and resize options
 */
$newHeight = get(array('height', 'h'));
$maxHeight = getConfig('max_height', 2000);

// Check to replace predefined size
if (isset($sizes[$newHeight])) {
    $newHeight = $sizes[$newHeight];
}

// height
if ($newHeight && $newHeight[strlen($newHeight)-1] == '%') {
    is_numeric(substr($newHeight, 0, -1))
        or errorPage('Height % out of range.', 404);
} else {
    is_null($newHeight)
        or ($newHeight > 10 && $newHeight <= $maxHeight)
        or errorPage('Height out of range.', 404);
}

verbose("new height = $newHeight");



/**
 * aspect-ratio, ar - affecting the resulting image width, height and resize options
 */
$aspectRatio         = get(array('aspect-ratio', 'ar'));
$aspectRatioConstant = getConfig('aspect_ratio_constant', function () {
    return array(
        '3:1'    => 3/1,
        '3:2'    => 3/2,
        '4:3'    => 4/3,
        '8:5'    => 8/5,
        '16:10'  => 16/10,
        '16:9'   => 16/9,
        'golden' => 1.618,
    );
});

// Check to replace predefined aspect ratio
$aspectRatios = call_user_func($aspectRatioConstant);
$negateAspectRatio = ($aspectRatio && $aspectRatio[0] == '!') ? true : false;
$aspectRatio = $negateAspectRatio ? substr($aspectRatio, 1) : $aspectRatio;

if (isset($aspectRatios[$aspectRatio])) {
    $aspectRatio = $aspectRatios[$aspectRatio];
}

if ($negateAspectRatio) {
    $aspectRatio = 1 / $aspectRatio;
}

is_null($aspectRatio)
    or is_numeric($aspectRatio)
    or errorPage('Aspect ratio out of range', 404);

verbose("aspect ratio = $aspectRatio");



/**
 * crop-to-fit, cf - affecting the resulting image width, height and resize options
 */
$cropToFit = getDefined(array('crop-to-fit', 'cf'), true, false);

verbose("crop to fit = $cropToFit");



/**
 * Set default background color from config file.
 */
$backgroundColor = getConfig('background_color', null);

if ($backgroundColor) {
    $img->setDefaultBackgroundColor($backgroundColor);
    verbose("Using default background_color = $backgroundColor");
}



/**
 * bgColor - Default background color to use
 */
$bgColor = get(array('bgColor', 'bg-color', 'bgc'), null);

verbose("bgColor = $bgColor");



/**
 * Do or do not resample image when resizing.
 */
$resizeStrategy = getDefined(array('no-resample'), true, false);

if ($resizeStrategy) {
    $img->setCopyResizeStrategy($img::RESIZE);
    verbose("Setting = Resize instead of resample");
}




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
$useOriginalDefault = getConfig('skip_original', false);

if ($useOriginalDefault === true) {
    verbose("skip original is default ON");
    $useOriginal = false;
}

verbose("use original = $useOriginal");



/**
 * quality, q - set level of quality for jpeg images
 */
$quality = get(array('quality', 'q'));
$qualityDefault = getConfig('jpg_quality', null);

is_null($quality)
    or ($quality > 0 and $quality <= 100)
    or errorPage('Quality out of range', 404);

if (is_null($quality) && !is_null($qualityDefault)) {
    $quality = $qualityDefault;
}

verbose("quality = $quality");



/**
 * compress, co - what strategy to use when compressing png images
 */
$compress = get(array('compress', 'co'));
$compressDefault = getConfig('png_compression', null);

is_null($compress)
    or ($compress > 0 and $compress <= 9)
    or errorPage('Compress out of range', 404);

if (is_null($compress) && !is_null($compressDefault)) {
    $compress = $compressDefault;
}

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
    or errorPage('Scale out of range', 404);

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
    or errorPage('RotateBefore out of range', 404);

verbose("rotateBefore = $rotateBefore");



/**
 * rotateAfter - Rotate the image with an angle, before processing
 */
$rotateAfter = get(array('rotateAfter', 'rotate-after', 'ra', 'rotate', 'r'));

is_null($rotateAfter)
    or ($rotateAfter >= -360 and $rotateAfter <= 360)
    or errorPage('RotateBefore out of range', 404);

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
* json -  output the image as a JSON object with details on the image.
* ascii - output the image as ASCII art.
 */
$outputFormat = getDefined('json', 'json', null);
$outputFormat = getDefined('ascii', 'ascii', $outputFormat);

verbose("outputformat = $outputFormat");

if ($outputFormat == 'ascii') {
    $defaultOptions = getConfig(
        'ascii-options',
        array(
            "characterSet" => 'two',
            "scale" => 14,
            "luminanceStrategy" => 3,
            "customCharacterSet" => null,
        )
    );
    $options = get('ascii');
    $options = explode(',', $options);

    if (isset($options[0]) && !empty($options[0])) {
        $defaultOptions['characterSet'] = $options[0];
    }

    if (isset($options[1]) && !empty($options[1])) {
        $defaultOptions['scale'] = $options[1];
    }

    if (isset($options[2]) && !empty($options[2])) {
        $defaultOptions['luminanceStrategy'] = $options[2];
    }

    if (count($options) > 3) {
        // Last option is custom character string
        unset($options[0]);
        unset($options[1]);
        unset($options[2]);
        $characterString = implode($options);
        $defaultOptions['customCharacterSet'] = $characterString;
    }

    $img->setAsciiOptions($defaultOptions);
}




/**
 * dpr - change to get larger image to easier support larger dpr, such as retina.
 */
$dpr = get(array('ppi', 'dpr', 'device-pixel-ratio'), 1);

verbose("dpr = $dpr");



/**
 * convolve - image convolution as in http://php.net/manual/en/function.imageconvolution.php
 */
$convolve = get('convolve', null);
$convolutionConstant = getConfig('convolution_constant', array());

// Check if the convolve is matching an existing constant
if ($convolve && isset($convolutionConstant)) {
    $img->addConvolveExpressions($convolutionConstant);
    verbose("convolve constant = " . print_r($convolutionConstant, 1));
}

verbose("convolve = " . print_r($convolve, 1));



/**
 * no-upscale, nu - Do not upscale smaller image to larger dimension.
 */
$upscale = getDefined(array('no-upscale', 'nu'), false, true);

verbose("upscale = $upscale");



/**
 * Get details for post processing
 */
$postProcessing = getConfig('postprocessing', array(
    'png_lossy'        => false,
    'png_lossy_cmd'    => '/usr/local/bin/pngquant --force --output',

    'png_filter'        => false,
    'png_filter_cmd'    => '/usr/local/bin/optipng -q',

    'png_deflate'       => false,
    'png_deflate_cmd'   => '/usr/local/bin/pngout -q',

    'jpeg_optimize'     => false,
    'jpeg_optimize_cmd' => '/usr/local/bin/jpegtran -copy none -optimize',
));



/**
 * lossy - Do lossy postprocessing, if available.
 */
$lossy = getDefined(array('lossy'), true, null);

verbose("lossy = $lossy");



/**
 * alias - Save resulting image to another alias name.
 * Password always apply, must be defined.
 */
$alias          = get('alias', null);
$aliasPath      = getConfig('alias_path', null);
$validAliasname = getConfig('valid_aliasname', '#^[a-z0-9A-Z-_]+$#');
$aliasTarget    = null;

if ($alias && $aliasPath && $passwordMatch) {

    $aliasTarget = $aliasPath . $alias;
    $useCache    = false;

    is_writable($aliasPath)
        or errorPage("Directory for alias is not writable.", 403);

    preg_match($validAliasname, $alias)
        or errorPage('Filename for alias contains invalid characters. Do not add extension.', 404);

} elseif ($alias) {
    errorPage('Alias is not enabled in the config file or password not matching.', 403);
}

verbose("alias = $alias");



/**
 * Add cache control HTTP header.
 */
$cacheControl = getConfig('cache_control', null);

if ($cacheControl) {
    verbose("cacheControl = $cacheControl");
    $img->addHTTPHeader("Cache-Control", $cacheControl);
}



/**
 * interlace - Enable configuration for interlaced progressive JPEG images.
 */
$interlaceConfig  = getConfig('interlace', null);
$interlaceValue   = getValue('interlace', null);
$interlaceDefined = getDefined('interlace', true, null);
$interlace = $interlaceValue ?? $interlaceDefined ?? $interlaceConfig;
verbose("interlace (configfile) = ", $interlaceConfig);
verbose("interlace = ", $interlace);



/**
 * Prepare a dummy image and use it as source image.
 */
if ($dummyImage === true) {
    $dummyDir = $cache->getPathToSubdir("dummy");

    $img->setSaveFolder($dummyDir)
        ->setSource($dummyFilename, $dummyDir)
        ->setOptions(
            array(
                'newWidth'  => $newWidth,
                'newHeight' => $newHeight,
                'bgColor'   => $bgColor,
            )
        )
        ->setJpegQuality($quality)
        ->setPngCompression($compress)
        ->createDummyImage()
        ->generateFilename(null, false)
        ->save(null, null, false);

    $srcImage = $img->getTarget();
    $imagePath = null;

    verbose("src (updated) = $srcImage");
}



/**
 * Prepare a sRGB version of the image and use it as source image.
 */
$srgbDefault = getConfig('srgb_default', false);
$srgbColorProfile = getConfig('srgb_colorprofile', __DIR__ . '/../icc/sRGB_IEC61966-2-1_black_scaled.icc');
$srgb = getDefined('srgb', true, null);

if ($srgb || $srgbDefault) {

    $filename = $img->convert2sRGBColorSpace(
        $srcImage,
        $imagePath,
        $cache->getPathToSubdir("srgb"),
        $srgbColorProfile,
        $useCache
    );

    if ($filename) {
        $srcImage = $img->getTarget();
        $imagePath = null;
        verbose("srgb conversion and saved to cache = $srcImage");
    } else {
        verbose("srgb not op");
    }
}



/**
 * Display status
 */
if ($status) {
    $text  = "img.php version = " . CIMAGE_VERSION . "\n";
    $text .= "PHP version = " . PHP_VERSION . "\n";
    $text .= "Running on: " . $_SERVER['SERVER_SOFTWARE'] . "\n";
    $text .= "Allow remote images = $allowRemote\n";

    $res = $cache->getStatusOfSubdir("");
    $text .= "Cache $res\n";

    $res = $cache->getStatusOfSubdir("remote");
    $text .= "Cache remote $res\n";

    $res = $cache->getStatusOfSubdir("dummy");
    $text .= "Cache dummy $res\n";

    $res = $cache->getStatusOfSubdir("srgb");
    $text .= "Cache srgb $res\n";

    $res = $cache->getStatusOfSubdir($fastTrackCache);
    $text .= "Cache fasttrack $res\n";

    $text .= "Alias path writable = " . is_writable($aliasPath) . "\n";

    $no = extension_loaded('exif') ? null : 'NOT';
    $text .= "Extension exif is $no loaded.<br>";

    $no = extension_loaded('curl') ? null : 'NOT';
    $text .= "Extension curl is $no loaded.<br>";

    $no = extension_loaded('imagick') ? null : 'NOT';
    $text .= "Extension imagick is $no loaded.<br>";

    $no = extension_loaded('gd') ? null : 'NOT';
    $text .= "Extension gd is $no loaded.<br>";

    $text .= checkExternalCommand("PNG LOSSY", $postProcessing["png_lossy"], $postProcessing["png_lossy_cmd"]);
    $text .= checkExternalCommand("PNG FILTER", $postProcessing["png_filter"], $postProcessing["png_filter_cmd"]);
    $text .= checkExternalCommand("PNG DEFLATE", $postProcessing["png_deflate"], $postProcessing["png_deflate_cmd"]);
    $text .= checkExternalCommand("JPEG OPTIMIZE", $postProcessing["jpeg_optimize"], $postProcessing["jpeg_optimize_cmd"]);

    if (!$no) {
        $text .= print_r(gd_info(), 1);
    }

    echo <<<EOD
<!doctype html>
<html lang=en>
<meta charset=utf-8>
<title>CImage status</title>
<pre>$text</pre>
EOD;
    exit;
}



/**
 * Log verbose details to file
 */
if ($verboseFile) {
    $img->setVerboseToFile("$cachePath/log.txt");
}



/**
 * Hook after img.php configuration and before processing with CImage
 */
$hookBeforeCImage = getConfig('hook_before_CImage', null);

if (is_callable($hookBeforeCImage)) {
    verbose("hookBeforeCImage activated");

    $allConfig = $hookBeforeCImage($img, array(
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
            'autoRotate'   => $autoRotate,

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
            'interlace' => $interlace,

            // Output format
            'outputFormat' => $outputFormat,
            'dpr'          => $dpr,

            // Other
            'postProcessing' => $postProcessing,
            'lossy' => $lossy,
    ));
    verbose(print_r($allConfig, 1));
    extract($allConfig);
}



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
<!doctype html>
<html lang=en>
<meta charset=utf-8>
<title>CImage verbose output</title>
<style>body{background-color: #ddd}</style>
<a href=$url1><code>$url1</code></a><br>
<img src='{$url1}' />
<pre id="json"></pre>
<script src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
<script type="text/javascript">
window.getDetails = function (url, id) {
  $.getJSON(url, function(data) {
    element = document.getElementById(id);
    element.innerHTML = "filename: " + data.filename + "\\nmime type: " + data.mimeType + "\\ncolors: " + data.colors + "\\nsize: " + data.size + "\\nwidth: " + data.width + "\\nheigh: " + data.height + "\\naspect-ratio: " + data.aspectRatio + ( data.pngType ? "\\npng-type: " + data.pngType : '');
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
    ->setSaveFolder($cachePath)
    ->useCache($useCache)
    ->setSource($srcImage, $imagePath)
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
            'autoRotate'   => $autoRotate,

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
            'interlace' => $interlace,

            // Output format
            'outputFormat' => $outputFormat,
            'dpr'          => $dpr,

            // Postprocessing using external tools
            'lossy' => $lossy,
        )
    )
    ->loadImageDetails()
    ->initDimensions()
    ->calculateNewWidthAndHeight()
    ->setSaveAsExtension($saveAs)
    ->setJpegQuality($quality)
    ->setPngCompression($compress)
    ->useOriginalIfPossible($useOriginal)
    ->generateFilename($cachePath)
    ->useCacheIfPossible($useCache)
    ->load()
    ->preResize()
    ->resize()
    ->postResize()
    ->setPostProcessingOptions($postProcessing)
    ->save()
    ->linkToCacheFile($aliasTarget)
    ->output();
