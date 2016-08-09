<?php
/**
 * Configuration for img.php, name the config file the same as your img.php and
 * append _config. If you are testing out some in imgtest.php then label that
 * config-file imgtest_config.php.
 *
 */



/**
 * Change to true to enable debug mode which logs additional information
 * to file. Only use for test and development. You must create the logfile
 * and make it writable by the webserver or log entries will silently fail.
 *
 * CIMAGE_DEBUG will be false by default, if its not defined.
 */
if (!defined("CIMAGE_DEBUG")) {
    define("CIMAGE_DEBUG", false);
    //define("CIMAGE_DEBUG", true);
    define("CIMAGE_DEBUG_FILE", "/tmp/cimage");
}



return array(

    /**
     * Set mode as 'strict', 'production' or 'development'.
     *
     * development: Development mode with verbose error reporting. Option
     *              &verbose and &status enabled.
     * production:  Production mode logs all errors to file, giving server
     *              error 500 for bad usage. Option &verbose and &status
     *              disabled.
     * strict:      Strict mode logs few errors to file, giving server error
     *              500 for bad usage. Stripped from comments and spaces.
     *              Option &verbose and &status disabled.
     *
     * Default values:
     *  mode: 'production'
     */
     //'mode' => 'production',
     //'mode' => 'development',
     //'mode' => 'strict',



    /**
     * Where to find the autoloader.
     *
     * Default values:
     *  autoloader:  null
     */
    'autoloader'   =>  __DIR__ . '/../autoload.php',



    /**
     * Paths, where are the images stored and where is the cache.
     * End all paths with a slash.
     *
     * Default values:
     *  image_path:     __DIR__ . '/img/'
     *  cache_path:     __DIR__ . '/../cache/'
     *  alias_path:     null
     */
    'image_path'        =>  __DIR__ . '/img/',
    'cache_path'        =>  __DIR__ . '/../cache/',
    //'alias_path'   =>  __DIR__ . '/img/alias/',



    /**
     * Fast track cache. Save a json representation of the image as a
     * fast track to the cached version of the image. This avoids some
     * processing and allows for quicker load times of cached images.
     *
     * Default values:
     *  fast_track_allow: false
     */
    //'fast_track_allow' => true,



    /**
     * Class names to use, to ease dependency injection. You can change Class
     * name if you want to use your own class instead. This is a way to extend
     * the codebase.
     *
     * Default values:
     *  CImage: CImage
     *  CCache: CCache
     *  CFastTrackCache: CFastTrackCache
     */
     //'CImage' => 'CImage',
     //'CCache' => 'CCache',
     //'CFastTrackCache' => 'CFastTrackCache',



    /**
     * Use password to protect from missusage, send &pwd=... or &password=..
     * with the request to match the password or set to false to disable.
     * Passwords are only used together with options for remote download
     * and aliasing.
     *
     * Create a passwords like this, depending on the type used:
     *  text: 'my_password'
     *  md5:  md5('my_password')
     *  hash: password_hash('my_password', PASSWORD_DEFAULT)
     *
     * Default values.
     *  password_always: false  // do not always require password,
     *  password:        false  // as in do not use password
     *  password_type:   'text' // use plain password, not encoded,
     */
    //'password_always' => false, // always require password,
    //'password'        => false, // "secret-password",
    //'password_type'   => 'text', // supports 'text', 'md5', 'hash',



    /**
     * Allow or disallow downloading of remote images available on
     * remote servers. Default is to disallow remote download.
     *
     * When enabling remote download, the default is to allow download any
     * link starting with http or https. This can be changed using
     * remote_pattern.
     *
     * When enabling remote_whitelist a check is made that the hostname of the
     * source to download matches the whitelist. By default the check is
     * disabled and thereby allowing download from any hosts.
     *
     * Default values.
     *  remote_allow:     false
     *  remote_pattern:   null  // use default values from CImage which is to
     *                          // allow download from any http- and
     *                          // https-source.
     *  remote_whitelist: null  // use default values from CImage which is to
     *                          // allow download from any hosts.
     */
    //'remote_allow'     => true,
    //'remote_pattern'   => '#^https?://#',
    //'remote_whitelist' => array(
    //    '\.facebook\.com$',
    //    '^(?:images|photos-[a-z])\.ak\.instagram\.com$',
    //    '\.google\.com$'
    //),



    /**
     * Use backup image if src-image is not found on disk. The backup image
     * is only available for local images and based on wether the original
     * image is found on disk or not. The backup image must be a local image
     * or the dummy image.
     *
     * Default value:
     *  src_alt:  null //disabled by default
     */
     //'src_alt' => 'car.png',
     //'src_alt' => 'dummy',



    /**
     * A regexp for validating characters in the image or alias filename.
     *
     * Default value:
     *  valid_filename:  '#^[a-z0-9A-Z-/_ \.:]+$#'
     *  valid_aliasname: '#^[a-z0-9A-Z-_]+$#'
     */
     //'valid_filename'  => '#^[a-z0-9A-Z-/_ \.:]+$#',
     //'valid_aliasname' => '#^[a-z0-9A-Z-_]+$#',



     /**
      * Change the default values for CImage quality and compression used
      * when saving images.
      *
      * Default value:
      *  jpg_quality:     null, integer between 0-100
      *  png_compression: null, integer between 0-9
      */
      //'jpg_quality'  => 75,
      //'png_compression' => 1,



      /**
       * Convert the image to srgb before processing. Saves the converted
       * image in a cache subdir 'srgb'. This option is default false but can
       * be changed to default true to do this conversion for all images.
       * This option requires PHP extension imagick and will silently fail
       * if that is not installed.
       *
       * Default value:
       *  srgb_default:      false
       *  srgb_colorprofile: __DIR__ . '/../icc/sRGB_IEC61966-2-1_black_scaled.icc'
       */
       //'srgb_default' => false,
       //'srgb_colorprofile' => __DIR__ . '/../icc/sRGB_IEC61966-2-1_black_scaled.icc',



       /**
        * Set skip-original to true to always process the image and use
        * the cached version. Default is false and to use the original
        * image when its no processing needed.
        *
        * Default value:
        *  skip_original: false
        */
        //'skip_original' => true,



      /**
       * A function (hook) can be called after img.php has processed all
       * configuration options and before processing the image using CImage.
       * The function receives the $img variabel and an array with the
       * majority of current settings.
       *
       * Default value:
       *  hook_before_CImage:     null
       */
       /*'hook_before_CImage' => function (CImage $img, Array $allConfig) {
           if ($allConfig['newWidth'] > 10) {
               $allConfig['newWidth'] *= 2;
           }
           return $allConfig;
       },*/



       /**
        * Add header for cache control when outputting images.
        *
        * Default value:
        *  cache_control: null, or set to string
        */
        //'cache_control' => "max-age=86400",



     /**
      * The name representing a dummy image which is automatically created
      * and stored as a image in the dir CACHE_PATH/dummy. The dummy image
      * can then be used as a placeholder image.
      * The dir CACHE_PATH/dummy is automatically created when needed.
      * Write protect the CACHE_PATH/dummy to prevent creation of new
      * dummy images, but continue to use the existing ones.
      *
      * Default value:
      *  dummy_enabled:  true as default, disable dummy feature by setting
      *                  to false.
      *  dummy_filename: 'dummy' use this as ?src=dummy to create a dummy image.
      */
      //'dummy_enabled' => true,
      //'dummy_filename' => 'dummy',



     /**
     * Check that the imagefile is a file below 'image_path' using realpath().
     * Security constraint to avoid reaching images outside image_path.
     * This means that symbolic links to images outside the image_path will
     * fail.
     *
     * Default value:
     *  image_path_constraint: true
     */
     //'image_path_constraint' => false,



     /**
     * Set default timezone.
     *
     * Default values.
     *  default_timezone: ini_get('default_timezone') or 'UTC'
     */
    //'default_timezone' => 'UTC',



    /**
     * Max image dimensions, larger dimensions results in 404.
     * This is basically a security constraint to avoid using resources on creating
     * large (unwanted) images.
     *
     * Default values.
     *  max_width:  2000
     *  max_height: 2000
     */
    //'max_width'     => 2000,
    //'max_height'    => 2000,



    /**
     * Set default background color for all images. Override it using
     * option bgColor.
     * Colorvalue is 6 digit hex string between 000000-FFFFFF
     * or 8 digit hex string if using the alpha channel where
     * the alpha value is between 00 (opaqe) and 7F (transparent),
     * that is between 00000000-FFFFFF7F.
     *
     * Default values.
     *  background_color: As specified by CImage
     */
    //'background_color' => "FFFFFF",
    //'background_color' => "FFFFFF7F",



    /**
     * Post processing of images using external tools, set to true or false
     * and set command to be executed.
     *
     * The png_lossy can alos have a value of null which means that its
     * enabled but not used as default. Each image having the option
     * &lossy will be processed. This means one can individually choose
     * when to use the lossy processing.
     *
     * Default values.
     *
     *  png_lossy:        false
     *  png_lossy_cmd:    '/usr/local/bin/pngquant --force --output'
     *
     *  png_filter:        false
     *  png_filter_cmd:    '/usr/local/bin/optipng -q'
     *
     *  png_deflate:       false
     *  png_deflate_cmd:   '/usr/local/bin/pngout -q'
     *
     *  jpeg_optimize:     false
     *  jpeg_optimize_cmd: '/usr/local/bin/jpegtran -copy none -optimize'
     */
    /*
    'postprocessing' => array(
        'png_lossy'       => null,
        'png_lossy_cmd'   => '/usr/local/bin/pngquant --force --output',

        'png_filter'        => false,
        'png_filter_cmd'    => '/usr/local/bin/optipng -q',

        'png_deflate'       => false,
        'png_deflate_cmd'   => '/usr/local/bin/pngout -q',

        'jpeg_optimize'     => false,
        'jpeg_optimize_cmd' => '/usr/local/bin/jpegtran -copy none -optimize',
    ),
    */



    /**
     * Create custom convolution expressions, matrix 3x3, divisor and
     * offset.
     *
     * Default values.
     *  convolution_constant: array()
     */
    /*
    'convolution_constant' => array(
        //'sharpen'       => '-1,-1,-1, -1,16,-1, -1,-1,-1, 8, 0',
        //'sharpen-alt'   => '0,-1,0, -1,5,-1, 0,-1,0, 1, 0',
    ),
    */



    /**
     * Prevent leeching of images by controlling the hostname of those who
     * can access the images. Default is to allow hotlinking.
     *
     * Password apply when hotlinking is disallowed, use password to allow
     * hotlinking.
     *
     * The whitelist is an array of regexpes for allowed hostnames that can
     * hotlink images.
     *
     * Default values.
     *  allow_hotlinking:     true
     *  hotlinking_whitelist: array()
     */
     /*
    'allow_hotlinking' => false,
    'hotlinking_whitelist' => array(
        '^dbwebb\.se$',
    ),
    */


    /**
     * Create custom shortcuts for more advanced expressions.
     *
     * Default values.
     *  shortcut: array(
     *      'sepia' => "&f=grayscale&f0=brightness,-10&f1=contrast,-20&f2=colorize,120,60,0,0&sharpen",
     *  )
     */
     /*
    'shortcut' => array(
        'sepia' => "&f=grayscale&f0=brightness,-10&f1=contrast,-20&f2=colorize,120,60,0,0&sharpen",
    ),*/



    /**
     * Predefined size constants.
     *
     * These can be used together with &width or &height to create a constant value
     * for a width or height where can be changed in one place.
     * Useful when your site changes its layout or if you have a grid to fit images into.
     *
     * Example:
     *  &width=w1  // results in width=613
     *  &width=c2  // results in spanning two columns with a gutter, 30*2+10=70
     *  &width=c24 // results in spanning whole grid 24*30+((24-1)*10)=950
     *
     * Default values.
     *  size_constant: As specified by the function below.
     */
    /*
    'size_constant' => function () {

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
    },*/



    /**
     * Predefined aspect ratios.
     *
     * Default values.
     *  aspect_ratio_constant: As the function below.
     */
    /*'aspect_ratio_constant' => function () {
        return array(
            '3:1'   => 3/1,
            '3:2'   => 3/2,
            '4:3'   => 4/3,
            '8:5'   => 8/5,
            '16:10' => 16/10,
            '16:9'  => 16/9,
            'golden' => 1.618,
        );
    },*/



    /**
     * Default options for ascii image.
     *
     * Default values as specified below in the array.
     *  ascii-options:
     *   characterSet:       Choose any character set available in CAsciiArt.
     *   scale:              How many pixels should each character
     *                       translate to.
     *   luminanceStrategy:  Choose any strategy available in CAsciiArt.
     *   customCharacterSet: Define your own character set.
     */
    /*'ascii-options' => array(
            "characterSet" => 'two',
            "scale" => 14,
            "luminanceStrategy" => 3,
            "customCharacterSet" => null,
        );
    },*/
);
