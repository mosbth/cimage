Image conversion on the fly using PHP
=====================================

About
-------------------------------------

`CImage` is a PHP class which enables resizing of images through scaling and cropping together with filtering effects, all using PHP GD. The script `img.php` uses `CImage` to enable server-side image processing together with caching and optimization of the processed images.

Server-side image processing is a most useful tool for any web developer, `img.php` has an easy to use interface and its quite powerful when you integrate it with your website. Using it might decrease the time and effort put in managing images and improve your work flow when creating content for websites.

Read more on http://dbwebb.se/opensource/cimage

Enjoy!

Mikael Roos (me@mikaelroos.se)



License
-------------------------------------

This is free software and open source software, licensed according MIT.



Installation and get going
-------------------------------------

**Latest stable version is v0.5.2 released 2014-04-01.**

```bash
git clone git://github.com/mosbth/cimage.git
cd cimage
git checkout v0.5.2
```

Make the cache-directory writable by the webserver.

```bash
chmod 777 cache
```

Try it out by pointing your browser to the test file `webroot/test.php`.

Review the settings in `webroot/img_config.php` and check out `webroot/img.php` on how it uses `CImage`.




Usage
-------------------------------------

###List of parameters

The `img.php` supports a lot of parameters. Combine the parameters to get the desired behavior and resulting image. For example, take the original image, resize it using width, aspect-ratio and crop-to-fit, apply a sharpen effect, save the image as JPEG using quality 30.

| `img.php?src=kodim13.png&w=600&aspect-ratio=4&crop-to-fit&sharpen&save-as=jpg&q=30` |
|-----------------------------------------------------------|
| <img src=http://dbwebb.se/kod-exempel/cimage_/webroot/img.php?src=kodim13.png&w=600&aspect-ratio=4&crop-to-fit&sharpen&save-as=jpg&q=30 alt=''> |

Here is a list of all parameters that you can use together with `img.php`, grouped by its basic intent of usage. 


####Mandatory options and debugging

The `src` is the only mandatory option. The other in this section is useful for debugging or deciding what version of the target image is used.

| Parameter      | Explained                                    | 
|----------------|----------------------------------------------|
| `src`          | Source image to use, mandatory. `src=img.png` or with subdirectory `src=dir/img.png`. |
| `nc, no-cache` | Do not use the cached version, do all image processing and save a new image to cache. |
| `so, skip-original`| Skip using the original image, always process image, create and use a cached version of the original image. |
| `v, verbose`   | Do verbose output and print out a log what happens. Good for debugging, analyzing the process and inspecting how the image is being processed. |



####Options for resizing image

These options are all affecting the dimensions used when resizing the image. Its used to define the area to use in the source image and the resulting dimensions for the target image.

| Parameter      | Explained                                    | 
|----------------|----------------------------------------------|
| `h, height`    | `h=200` sets the width to be to max 200px. `h=25%` sets the height to max 25% of its original height. |
| `w, width`     | `w=200` sets the height to be max 200px. `w=100%` sets the width to max 100% of its original width. |
| `ar, aspect-ratio` | Control target aspect ratio. Use together with either height or width or alone to base calculations on original image dimensions. This setting is used to calculate the resulting dimension for the image. `w=160&aspect-ratio=1.6` results in a height of 100px. Use ar=!1.6 to inverse the ratio, useful when using portrat instead of landscape images. |
| `nr, no-ratio, stretch` | Do *not* keep aspect ratio when resizing and using both width & height constraints. Results in stretching the image, if needed, to fit in the resulting box. |
| `cf, crop-to-fit`  | Set together with both `h` & `w` to make the image fit into dimensions, and crop out the rest of the image. |
| `a, area`      | Define the area of the image to work with. Set `area=10,10,10,10` (top,right,bottom,left) to crop out the 10% of the outermost area. It works like an offset to define which part of the image you want to process. Its an alternative of using `crop`. |
| `c, crop`      | Crops an area from the original image, set width, height, start_x and start_y to define the area to crop, for example `crop=100,100,10,10` (`crop=width,height,start_x,start_y`). Left top corner is 0, 0. You can use `left`, `right` or `center` when setting start_x. You may use `top`, `bottom` or `center` when setting start_y. |



####Processing of image before resizing

These options are executed *before* the image is resized.

| Parameter      | Explained                                    | 
|----------------|----------------------------------------------|
| `s, scale`     | Scale the image to a size proportional to a percentage of its original size, `scale=25` makes an image 25% of its original size and `size=200` doubles up the image size. Scale is applied before resizing and has no impact of the target width and height. |



####Processing of image after resizing

These options are executed *after* the image is resized.

| Parameter      | Explained                                    | 
|----------------|----------------------------------------------|
| `sharpen`      | Appy a filter that sharpens the image.       |
| `emboss`       | Appy a filter with an emboss effect.         |
| `blur`         | Appy a filter with a blur effect.            |
| `f, filter`    | Apply filter to image, `f=colorize,0,255,0,0` makes image more green. Supports all filters as defined in [PHP GD `imagefilter()`](http://php.net/manual/en/function.imagefilter.php). |
| `f0, f1-f9`    | Same as `filter`, just add more filters. Applied in order `f`, `f0-f9`.  |


####Saving image, affecting quality and filesize

Options for saving the target image.

| Parameter      | Explained                                    | 
|----------------|----------------------------------------------|
| `q, quality`   | Quality affects lossy compression and file size for JPEG images by setting the quality between 1-100, default is 60.  Quality only affects JPEG. |
| `co, compress` | For PNG images it defines the compression algorithm, values can be 0-9, default is defined by PHP GD. Compress only affects PNG. |
| `p, palette`   | Create a palette version of the image with up to 256 colors. |
| `sa, save-as`  | Save resulting image as JPEG, PNG or GIF, for example `?src=river.png&save-as=gif`. |




Revision history
-------------------------------------


v0.5.x (latest)

* Trying to verify issue #29, but can not.
* Adding structure for testprograms together with, use `webroot/test_issue29.php` as sample.
* Improving code formatting.
* Moving parts of verbose output from img.php to CImage.php.


v0.5.2 (2014-04-01)

* Correcting issue #26 providing error message when not using postprocessing.
* Correcting issue #27 warning of default timezone.
* Removed default $config options in `img.php`, was not used, all configuration should be in `img_config.php`.
* Verified known bug - sharpen acts as blur in PHP 5.5.9 and 5.5.10 #28


v0.5.1 (2014-02-12)

* Display image in README-file.
* Create an empty `cache` directory as part of repo.


v0.5 (2014-02-12)

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

* Changed => to == on Modified-Since.
* Always send Last-Modified-Header.
* Added `htmlentities()` to verbose output.
* Fixed support for jpeg, not only jpg.
* Fixed crop whole image by setting crop=0,0,0,0
* Use negative values for crop width & height to base calulation on original width/height and withdraw selected amount.
* Correcting jpeg when setting quality.
* Removed obsolete reference to `$newName` in `CImage::__construct()` (issue 1). 


v0.4 (2013-10-08)

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

* Added crop. Can crop a area (`width`, `height`, `start_x`, `start_y`) from the original
image.
* Corrected to make the 304 Not Modified header work.
* Predefined sizes can be configured for width in `img.php`.
* Corrected to make crop work with width or height in combination with crop-to-fit.

 
v0.2 (2012-05-09) 

* Implemented filters as in http://php.net/manual/en/function.imagefilter.php
* Changed `crop` to `crop_to_fit`, works the same way.
* Changed arguments and sends them in array.
* Added quality-setting.
* Added testcases for above.


v0.1.1 (2012-04-27) 

* Corrected calculation where both width and height were set.


v0.1 (2012-04-25) 

* Initial release after rewriting some older code doing the same, but not that good and flexible.

<pre>
 .   
..:  Copyright 2012-2014 by Mikael Roos (me@mikaelroos.se)
</pre>
