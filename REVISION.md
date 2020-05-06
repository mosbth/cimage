Revision history
=====================================

[![Build Status](https://travis-ci.org/mosbth/cimage.svg?branch=master)](https://travis-ci.org/mosbth/cimage)
[![Build Status](https://scrutinizer-ci.com/g/mosbth/cimage/badges/build.png?b=master)](https://scrutinizer-ci.com/g/mosbth/cimage/build-status/master)


v0.7.23 (2020-05-06)
-------------------------------------

* Fix error in composer.json 



v0.7.22 (2020-05-06)
-------------------------------------

* Update composer.json and move ext-gd from required to suggested to ease installation where cli does not have all extensions installed.



v0.7.21 (2020-01-15)
-------------------------------------

* Support PHP 7.4, some minor fixes with notices.


v0.7.20 (2017-11-06)
-------------------------------------

* Remove webroot/img/{round8.PNG,wider.JPEG,wider.JPG} to avoid unzip warning message when installing with composer.
* Adding docker-compose.yml #169.


v0.7.19 (2017-03-31)
-------------------------------------

* Move exception handler from functions.php to img.php #166.
* Correct XSS injection in `check_system.php`.
* Composer suggests ext-imagick and ext-curl.


v0.7.18 (2016-08-09)
-------------------------------------

* Made `&lossless` a requirement to not use the original image.


v0.7.17 (2016-08-09)
-------------------------------------

* Made `&lossless` part of the generated cache filename.


v0.7.16 (2016-08-09)
-------------------------------------

* Fix default mode to be production.
* Added pngquant as extra postprocessing utility for PNG-images, #154.
* Bug `&status` wrong variable name for fast track cache.


v0.7.15 (2016-08-09)
-------------------------------------

* Added the [Lenna/Lena sample image](http://www.cs.cmu.edu/~chuck/lennapg/) as tif and created a png, jpeg and webp version using Imagick convert `convert lena.tif lena.{png,jpg,webp}`, #152.
* Limited and basic support for WEBP format, se #132.


v0.7.14 (2016-08-08)
-------------------------------------

* Re-add removed cache directory.
* Make fast track cache disabled by default in the config file.


v0.7.13 (2016-08-08)
-------------------------------------

* Moved functions from img.php to `functions.php`.
* Added function `trace()` to measure speed and memory consumption, only for development.
* Added fast cache #149.
* Added `imgf.php` as shortcut to check for fast cache, before loading `img.php` as usual, adding `imgf_config.php` as symlink to `img_config.php`.
* Created `defines.php` and moved definition av version there.
* Fixed images in README, #148.
* Initiated dependency injection to `CImage`, class names can be set in config file and will be injected to `CImage` from `img.php`. Not implemented for all classes. #151.
* Enabled debug mode to make it easier to trace what actually happens while processing the image, #150.


v0.7.12 (2016-06-01)
-------------------------------------

* Fixed to correctly display image when using a resize strategy without height or width.
* Fixed background color for option `no-upscale`, #144.


v0.7.11 (2016-04-18)
-------------------------------------

* Add option for `skip_original` to config file to always skip original, #118.


v0.7.10 (2016-04-01)
-------------------------------------

* Add backup option for images `src-alt`, #141.
* Add require of ext-gd in composer.json, #133.
* Fix strict mode only reporting 404 when failure, #127.


v0.7.9 (2015-12-07)
-------------------------------------

* Strict mode only reporting 404 when failure, #127.
* Added correct CImage version to remote agent string, #131.
* Adding CCache to improve cache handling of caching for dummy, remote and srgb. #130.


v0.7.8 (2015-12-06)
-------------------------------------

* HTTP error messages now 403, 404 and 500 as in #128 and #127.
* More examples on dealing with cache through bash `bin/cache.bash`, #129.
* Added conversion to sRGB using option `?srgb`. #120.
* Added Gitter badge to README, #126.
* Fix proper download url in README, #125.
* Change path in `webroot/htaccess` to make it work in current environment.


v0.7.7 (2015-10-21)
-------------------------------------

* One can now add a HTTP header for Cache-Control in the config file, #109.
* Added hook in img,php before CImage is called, #123.
* Added configuration for default jpeg quality and png compression in the config file, #107.
* Strip comments and whitespace in imgs.php, #115.
* Bundle imgs.php did not have the correct mode.
* Adding option &status to get an overview of the installed on configured utilities, #116.
* Bug, all files saved as png-files, when not saving as specific file.
* Removed saving filename extension for alias images.
* Added option to decide if resample or resize when copying images internally. `&no-resample` makes resize, instead of resample as is default.
* Verbose now correctly states if transparent color is detected.
* Compare-tool now supports 6 images.
* Added option for dark background in the compare-tool.
* Removed that source png-files, containing less than 255 colors, is always saved as palette images since this migth depend on processing of the image.
* Adding save-as as part of the generated cache filename, #121.
* Add extra fields to json-response, #114.
* Add header for Content-Length, #111.
* Add check for postprocessing tools in path in `webroot/check_system.php`, #104.


v0.7.6 (2015-10-18)
-------------------------------------

* Adding testpage for dummy images `webroot/test/test_issue101-dummy.php`.
* Adding width and height when creating dummy image.


v0.7.5 (2015-10-18)
-------------------------------------

* Adding feature for creating dummy images `src=dummy`, #101.
* Add png compression to generated cache filename, fix #103.
* Removed file prefix from storing images in cache, breaking filenamestructure for cache images.
* Code cleaning in `CImage.php`.


v0.7.4 (2015-09-15)
-------------------------------------

* Add CAsciiArt.php to composer for autoloading, fix #102.
* Generate filename with filters, does not work on Windows, fix #100.


v0.7.3 (2015-09-01)
-------------------------------------

* Support output of ascii images, #67.


v0.7.2 (2015-08-17)
-------------------------------------

* Allow space in remote filenames, fix #98.


v0.7.1 (2015-07-25)
-------------------------------------

* Support for password hashes using `text`, `md5` and `hash`, fix #77.
* Using `CWhitelist` for checking hotlinking to images, fix #88.
* Added mode for `test` which enables logging verbose mode to file, fix #97.
* Improved codestyle and added `phpcs.xml` to start using phpcs to check code style, fix #95.
* Adding `composer.json` for publishing on packagist.
* Add permalink to setup for comparing images with `webroot/compare/compare.php`, fix #92.
* Allow space in filename by using `urlencode()` and allow space as valid filenam character. fix #91.
* Support redirections for remote images, fix #87, fix #90.
* Improving usage of Travis and Scrutinizer.
* Naming cache-file using md5 for remote images, fix #86.
* Loading images without depending on filename extension, fix #85.
* Adding unittest with phpunit #84, fix #13
* Adding support for whitelist of remote hostnames, #84
* Adding phpdoc, fix #48.
* Adding travis, fix #15.
* Adding scrutinizer, fix #57.


v0.7.0 (2015-02-10)
-------------------------------------

* Always use password, setting in img_config.php, fix #78.
* Resize gif keeping transparency #81.
* Now returns statuscode 500 when something fails #55.
* Three different modes: strict, production, development #44.
* Three files for all-in-one `imgs.php`, `imgp.php`, `imgd.php` #73.
* Change name of script all-in-one to `webroot/imgs.php` #73.
* Combine all code into one singel script, `webroot/img_single.php` #73.
* Disallow hotlinking/leeching by configuration #46.
* Alias-name is without extension #47.
* Option `alias` now requires `password` to work #47.
* Support for option `password, pwd` to protect usage of `alias` and remote download.
* Added support for option `alias` that creates a link to a cached version  of the image #47.
* Create cache directory for remote download if it does not exists.
* Cleaned up `img_config.php` and introduced default values for almost all options #72.


v0.6.2 (2015-01-14)
-------------------------------------

* Added support for download of remote images #43.
* Added autoloader.


v0.6.1 (2015-01-08)
-------------------------------------

* Adding compare-page for comparing images. Issue #20.
* Added option `no-upscale, nu` as resizing strategy to decline upscaling of smaller images. Fix #61.
* Minor change in `CImage::resize()`, crop now does imagecopy without resamling.
* Correcting internal details for save-as and response json which indicated wrong colors. Fix #62.
* Fixed fill-to-fit that failed when using aspect-ratio. Fix #52.
* JSON returns correct values for resulting image. Fix #58.
* Corrected behaviour for skip-original. Fix #60.


v0.6 (2014-12-06)
-------------------------------------

* Rewrote and added documentation.
* Moved conolution expressesion from `img_config.php` to `CImage`.
* Minor cleaning of properties in `CImage`. Fix #23.
* Adding `webroot/htaccess` to show off how friendly urls can be created for `img.php`. Fix #45.
* Added option `fill-to-fit, ff`. Fix #38.
* Added option `shortcut, sc` to enable configuration of complex expressions. Fix #2.
* Added support for custom convolutions. Fix #49.
* Restructured testprograms. Fix #41.
* Corrected json on PHP 5.3. Fix #42.
* Improving template for tests in `webroot/tests` when testing out #40.
* Adding testcase for #40.
* Adding option `convolve` taking comma-separated list of 11 float-values, wraps and exposes `imageconvoluttion()`. #4
* Adding option `dpr, device-pixel-ratio` which defaults to 1. Set to 2 to get a twice as large image. Useful for Retina displays. Basically a shortcut to enlarge the image.
* Adding utility `cache.bash` to ease gathering stats on cache usage. #21
* Cache-directory can now be readonly and serve all cached files, still failing when need to save files. #5
* Cache now uses same file extension as original image #37.
* Can output image as json format using `json` #11.


v0.5.3 (2014-11-21)
-------------------------------------

* Support filenames of uppercase JPEG, JPG, PNG and GIF, as proposed in #37.
* Changing `CImage::output()` as proposed in #37.
* Adding security check that image filename is always below the path `image_path` as specified in `img_config.php` #37.
* Adding configuration item in `img_config.php` for setting valid characters in image filename.
* Moving `webroot/test*` into directory `webroot/test`.
* `webroot/check_system.php` now outputs if extension for exif is loaded.
* Broke API when `initDimensions()` split into two methods, new `initDimensions()` and `loadImageDetails()`.
* Added `autoRotate, aro` to auto rotate image based on EXIF information.
* Added `bgColor, bgc` to use as backgroundcolor when needing a filler color, for example rotate 45.
* Added `rotateBefore, rb` to rotate image a certain angle before processing.
* Added `rotateAfter, ra` to rotate image a certain angle after processing.
* Cleaned up code formatting, removed trailing spaces.
* Removed @ from opening images, better to display correct warning when failing #34, but put it back again.
* Setting gd.jpeg_ignore_warning to true as default #34.
* `webroot/check_system.php` now outputs version of PHP and GD.
* #32 correctly send 404 header when serving an error message.
* Trying to verify issue #29, but can not.
* Adding structure for testprograms together with, use `webroot/test_issue29.php` as sample.
* Improving code formatting.
* Moving parts of verbose output from img.php to CImage.php.


v0.5.2 (2014-04-01)
-------------------------------------

* Correcting issue #26 providing error message when not using postprocessing.
* Correcting issue #27 warning of default timezone.
* Removed default $config options in `img.php`, was not used, all configuration should be in `img_config.php`.
* Verified known bug - sharpen acts as blur in PHP 5.5.9 and 5.5.10 #28


v0.5.1 (2014-02-12)
-------------------------------------

* Display image in README-file.
* Create an empty `cache` directory as part of repo.


v0.5 (2014-02-12)
-------------------------------------

* Change constant name `CImage::PNG_QUALITY_DEFAULT` to `CImage::PNG_COMPRESSION_DEFAULT`.
* Split JPEG quality and PNG compression, `CImage->quality` and `CImage->compression`
* Changed `img.php` parameter name `d, deflate` to `co, compress`.
* Separating configuration issues from `img.php` to `img_config.php`.
* Format code according to PSR-2.
* Disabled post-processing JPEG and PNG as default.
* This version is supporting PHP 5.3, later versions will require 5.5 or later.
* Using GitHub issue tracking for feature requests and planning.
* Rewrote [the manual](http://dbwebb.se/opensource/cimage).
* Created directory `webroot` and moved some files there.


v0.4.1 (2014-01-27)
-------------------------------------

* Changed => to == on Modified-Since.
* Always send Last-Modified-Header.
* Added `htmlentities()` to verbose output.
* Fixed support for jpeg, not only jpg.
* Fixed crop whole image by setting crop=0,0,0,0
* Use negative values for crop width & height to base calulation on original width/height and withdraw selected amount.
* Correcting jpeg when setting quality.
* Removed obsolete reference to `$newName` in `CImage::__construct()` (issue 1).


v0.4 (2013-10-08)
-------------------------------------

* Improved support for pre-defined sizes.
* Adding grid column size as predefined size, c1-c24 for a 24 column grid. Configure in `img.php`.
* Corrected error on naming cache-files using subdir.
* Corrected calculation error on width & height for crop-to-fit.
* Adding effects for sharpen, emboss and blur through imageconvolution using matrixes.
* crop-to-fit, add parameter for offset x and y to enable to define which area is the, implemented as area.
* Support for resizing opaque images.
* Center of the image from which the crop is done. Improved usage of area to crop.
* Added support for % in width & height.
* Added aspect-ratio.
* Added scale.
* Quality for PNG images is now knows as deflate.
* Added palette to create images with max 256 colors.
* Added usage of all parameters to README.md
* Added documentation here http://dbwebb.se/opensource/cimage
* Adding `.gitignore`
* Re-adding `cache` directory


v0.3 (2012-10-02)
-------------------------------------

* Added crop. Can crop a area (`width`, `height`, `start_x`, `start_y`) from the original
image.
* Corrected to make the 304 Not Modified header work.
* Predefined sizes can be configured for width in `img.php`.
* Corrected to make crop work with width or height in combination with crop-to-fit.


v0.2 (2012-05-09)
-------------------------------------

* Implemented filters as in http://php.net/manual/en/function.imagefilter.php
* Changed `crop` to `crop_to_fit`, works the same way.
* Changed arguments and sends them in array.
* Added quality-setting.
* Added testcases for above.


v0.1.1 (2012-04-27)
-------------------------------------

* Corrected calculation where both width and height were set.


v0.1 (2012-04-25)
-------------------------------------

* Initial release after rewriting some older code doing the same, but not that good and flexible.
