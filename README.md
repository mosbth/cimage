Image conversion on the fly using PHP
=====================================

About
-------------------------------------

`CImage` is a PHP class enabling resizing of images through scaling and cropping together with filtering effects -- all using PHP GD. The script `img.php` uses `CImage` to enable server-side image processing together with caching and optimization of the processed images.

Server-side image processing is a most useful tool for any web developer, `img.php` has an easy to use interface and its powerful when you integrate it with your website. Using it might decrease the time and effort put in managing images and improve your workflow when creating content for websites.

This software is free and open source, licensed according MIT.



Use case 
--------------------------------------

You got an image from your friend who took it with the iPhone and you want to put it up on your website.

<img src="http://dbwebb.se/kod-exempel/cimage/webroot/img.php?src=issue36/me-270.jpg&w=300" alt="">

The original image is looking like this one, scaled down to a width of 300 pixels. 

So, you need to rotate it and crop off some parts to make it intresting. 

To show it off, I'll autorotate the image based on its EXIF-information, I will crop it to a thumbnail of 100x100 pixels and add a filter to make it greyscale finishing up with a sharpen effect. Just for the show I'll rotate the image 25 degrees - do not ask me why.

Lets call this *the URL-Photoshopper*. This is how the magic looks like. 

`img.php?src=issue36/me-270.jpg&w=100&h=100&cf&aro&rb=-25&a=8,30,30,38&f=grayscale`<br>`&convolve=sharpen-alt`

<img src="http://dbwebb.se/kod-exempel/cimage/webroot/img.php?src=issue36/me-270.jpg&w=100&h=100&cf&aro&rb=-25&a=8,30,30,38&f=grayscale&convolve=sharpen-alt" alt="">

For myself, I use `img.php` to put up all images on my website, it gives me the power of affecting the resulting images - without opening up a photo-editing application.



Requirements
--------------------------------------

`CImage` and `img.php` supports GIF (with transparency), JPEG and PNG (8bit transparent, 24bit semi transparent) images. It requires PHP 5.3 and PHP GD. You optionally need the EXIF extension to support auto-rotation of JPEG-images. 



Installation
--------------------------------------

The [sourcode is available on GitHub](https://github.com/mosbth/cimage). Clone, fork or [download as zip](https://github.com/mosbth/cimage/archive/master.zip). 

I prefer cloning like this. Do switch to the latest stable version.

**Latest stable version is v0.6 released 2014-12-06.**

```bash
git clone git://github.com/mosbth/cimage.git
cd cimage
git checkout v0.6
```

Make the cache-directory writable by the webserver.

```bash
chmod 777 cache
```



Get going quickly 
--------------------------------------



###Check out the test page

Try it out by pointing your browser to the test file `webroot/test/test.php`. It will show some images and you can review how they are created.



###Process your first image 

<img src="http://dbwebb.se/kod-exempel/cimage/webroot/img.php?src=kodim04.png&amp;w=w2&amp;a=40,0,50,0" alt=''>

Try it yourself by opening up an image in your browser. Start with `webroot/img.php?src=kodim04.png` and then try to resize it to a thumbnail `webroot/img.php?src=kodim04.png&width=100&height=100&crop-to-fit`.



###What does "processing the image" involves? 

Add `&verbose` to the link to get a verbose output of what is happens during image processing. This is useful for developers or those who seek a deeper understanding on how it all works.



###Check your system 

Open up `webroot/check_system.php` if you need to troubleshoot or if you are uncertain if your system has the right extensions loaded. 



###How does it work?

Review the settings in `webroot/img_config.php` and check out `webroot/img.php` on how it uses `CImage`.

The programatic flow, just to get you oriented in the environment, is.

1. Start in `img.php`.
2. `img.php` reads configuration details from `img_config.php`.
3. `img.php` reads and processes incoming `$_GET` arguments to prepare using `CImage`.
4. `img.php` uses `CImage`.
5. `CImage` processes and outputs the image according to how its used. 

Read on to learn more on how to use `img.php`.



Basic usage 
--------------------------------------



###Select the source

Open an image through `img.php` by using its `src` attribute.

> `img.php?src=kodim13.png`

It looks like this.

<img src=http://dbwebb.se/kod-exempel/cimage/webroot/img.php?src=kodim13.png&w=w1&save-as=jpg alt="">

All images are stored in a directory structure and you access them as `?src=dir1/dir2/image.png`. 



###Resize using constraints on width and height 

Create a thumbnail of the image by applying constraints on width and height, or one of them.

| `&width=150`        | `&height=150`       | `&w=150&h=150`      |
|---------------------|---------------------|---------------------|
| <img src=http://dbwebb.se/kod-exempel/cimage/webroot/img.php?src=kodim13.png&w=150 alt=''> | <img src=http://dbwebb.se/kod-exempel/cimage/webroot/img.php?src=kodim13.png&h=150 alt=''> | <img src=http://dbwebb.se/kod-exempel/cimage/webroot/img.php?src=kodim13.png&w=150&h=150 alt=''> |

By setting width, height or both, the image gets resized to be *not larger* than the defined dimensions *and* keeping its original aspect ratio.

Think of the constraints as a imaginary box where the image should fit. With `width=150` and `height=150` the box would have the dimension of 150x150px. A landscape image would fit in that box and its width would be 150px and its height depending on the aspect ratio, but for sure less than 150px. A portrait image would fit with a height of 150px and the width depending on the aspect ratio, but surely less than 150px.



###Resize to fit a certain dimension 

Creating a thumbnail with a certain dimension of width and height, usually involves stretching or cropping the image to fit in the selected dimensions. Here is how you create a image that has the exact dimensions of 300x150 pixels, by either *stretching*, *cropping* or *fill to fit*.


| What                | The image           |
|---------------------|---------------------|
| **Original.** The original image resized with a max width and max height.<br>`?w=300&h=150` | <img src=http://dbwebb.se/kod-exempel/cimage/webroot/img.php?src=kodim13.png&w=300&h=150 alt=''> |
| **Stretch.** Stretch the image so that the resulting image has the defined width and height.<br>`?w=300&h=150&stretch` | <img src=http://dbwebb.se/kod-exempel/cimage/webroot/img.php?src=kodim13.png&w=300&h=150&stretch alt=''> |
| **Crop to fit.** Keep the aspect ratio and crop out the parts of the image that does not fit.<br>`?w=300&h=150&crop-to-fit` | <img src=http://dbwebb.se/kod-exempel/cimage/webroot/img.php?src=kodim13.png&w=300&h=150&crop-to-fit alt=''> |
| **Fill to fit.** Keep the aspect ratio and fill then blank space with a background color.<br>`?w=300&h=150&fill-to-fit=006600` | <img src=http://dbwebb.se/kod-exempel/cimage/webroot/img.php?src=kodim13.png&w=300&h=150&fill-to-fit=006600 alt=''> |

Learn to crop your images, creative cropping can make wonderful images from appearingly useless originals.

Stretching might work, like in the above example where you can not really notice that the image is stretched. But usually, stretching is not that a good option since it distorts the ratio resulting in a image with incorrect dimensions. Stretching a face may not turn out particularly well.

Fill to fit is useful when you have some image that must fit in a certain dimension and stretching nor cropping can do it. Carefully choose the background color to make a good resulting image. Choose the same background color as your website and no one will notice.



###List of parameters 

`img.php` supports a lot of parameters. Combine the parameters to get the desired behavior and resulting image. For example, take the original image, resize it using width, aspect-ratio and crop-to-fit, apply a sharpen effect, save the image as JPEG using quality 30.

| `img.php?src=kodim13.png&w=600&aspect-ratio=4&crop-to-fit&sharpen&save-as=jpg&q=30` |
|-----------------------------------------------------------|
| <img src=http://dbwebb.se/kod-exempel/cimage/webroot/img.php?src=kodim13.png&w=600&aspect-ratio=4&crop-to-fit&sharpen&save-as=jpg&q=30 alt=''> |

Here is a list of all parameters that you can use together with `img.php`, grouped by its basic intent of usage. 


####Mandatory options and debugging

Option `src` is the only mandatory option. The other in this section is useful for debugging or deciding what version of the target image is used.

| Parameter      | Explained                                    | 
|----------------|----------------------------------------------|
| `src`          | Source image to use, mandatory. `src=img.png` or with subdirectory `src=dir/img.png`. |
| `nc, no-cache` | Do not use the cached version, do all image processing and save a new image to cache. |
| `so, skip-original`| Skip using the original image, always process image, create and use a cached version of the original image. |
| `v, verbose`   | Do verbose output and print out a log what happens. Good for debugging, analyzing the process and inspecting how the image is being processed. |
| `json`         | Output a JSON-representation of the image, useful for testing or optimizing when one wants to know the image dimensions, before using it. |



####Options for deciding width and height of target image 

These options are all affecting the final dimensions, width and height, of the resulting image.

| Parameter      | Explained                                    | 
|----------------|----------------------------------------------|
| `h, height`    | `h=200` sets the width to be to max 200px. `h=25%` sets the height to max 25% of its original height. |
| `w, width`     | `w=200` sets the height to be max 200px. `w=100%` sets the width to max 100% of its original width. |
| `ar, aspect-ratio` | Control target aspect ratio. Use together with either height or width or alone to base calculations on original image dimensions. This setting is used to calculate the resulting dimension for the image. `w=160&aspect-ratio=1.6` results in a height of 100px. Use `ar=!1.6` to inverse the ratio, useful for portrait images, compared to landscape images. |
| `dpr, device-pixel-ratio` | Default value is 1, set to 2 when you are delivering the image to a high density screen, `dpr=2` or `dpr=1.4`. Its a easy way to say the image should have larger dimensions. The resulting image will be twice as large (or 1.4 times), keeping its aspect ratio. |



####Options for resize strategy 

These options affect strategy to use when resizing an image into a target image that has both width and height set.

| Parameter      | Explained                                    | 
|----------------|----------------------------------------------|
| `nr, no-ratio, stretch` | Do *not* keep aspect ratio when resizing and using both width & height constraints. Results in stretching the image, if needed, to fit in the resulting box. |
| `cf, crop-to-fit`  | Set together with both `h` and `w` to make the image fit into dimensions, and crop out the rest of the image. |
| `ff, fill-to-fit` | Set together with both `h` and `w` to make the image fit into dimensions, and fill the rest using a background color. You can optionally supply a background color as this `ff=00ff00`, or `ff=00ff007f` when using the alpha channel. |
| `nu, no-upscale` | Avoid smaller images from being upscaled to larger ones. Combine with `stretch`, `crop-to-fit` or `fill-to-fit` to get the smaller image centered on a larger canvas. The requested dimension for the target image are thereby met. |



####Options for cropping part of image 

These options enable to decide what part of image to crop out.

| Parameter      | Explained                                    | 
|----------------|----------------------------------------------|
| `a, area`      | Define the area of the image to work with. Set `area=10,10,10,10` (top,right,bottom,left) to crop out the 10% of the outermost area. It works like an offset to define the part of the image you want to process. Its an alternative of using `crop`. |
| `c, crop`      | Crops an area from the original image, set width, height, start_x and start_y to define the area to crop, for example `crop=100,100,10,10` (`crop=width,height,start_x,start_y`). Left top corner is 0, 0. You can use `left`, `right` or `center` when setting start_x. You may use `top`, `bottom` or `center` when setting start_y. |



####General processing options 

These options are general options affecting processing.

| Parameter      | Explained                                    | 
|----------------|----------------------------------------------|
| `bgc, bg-color` | Set the backgroundcolor to use (if its needed). Use six hex digits as `bgc=00ff00` and 8 digits when using the alpha channel, as this `bgc=00ff007f`. The alpha value can be between 00 and 7f. |



####Processing of image before resizing 

This option are executed *before* the image is resized.

| Parameter      | Explained                                    | 
|----------------|----------------------------------------------|
| `s, scale`     | Scale the image to a size proportional to a percentage of its original size, `scale=25` makes an image 25% of its original size and `size=200` doubles up the image size. Scale is applied before resizing and has no impact of the target width and height. |
| `rb, rotate-before` | Rotate the image before its processed, send the angle as parameter `rb=45`. |
| `aro, auto-rotate`  | Auto rotate the image based on EXIF information (useful when using images from smartphones). |



####Processing of image after resizing 

These options are executed *after* the image is resized.

| Parameter      | Explained                                    |
|----------------|----------------------------------------------|
| `ra, rotate-after`<br>`r, rotate` | Rotate the image after its processed, send the angle as parameter `ra=45`. |
| `sharpen`            | Appy a convolution filter that sharpens the image.       |
| `emboss`             | Appy a convolution filter with an emboss effect.         |
| `blur`               | Appy a convolution filter with a blur effect.            |
| `convolve`           | Appy custom convolution filter as a 3x3 matrix, a divisor and offset, `convolve=0,-1,0,-1,5,-1,0,-1,0,1,0` sharpens the image. |
| `convolve`           | Use predefined convolution expression as `convolve=sharpen-alt` or a serie of convolutions as `convolve=draw,mean,motion`. These are supported out of the box: `lighten`, `darken`, `sharpen`, `sharpen-alt`, `emboss`, `emboss-alt`, `blur`, `gblur`, `edge`, `edge-alt`, `draw`, `mean`, `motion`. Add your own, or overwrite existing, in `img_config.php`. |
| `f, filter`          | Apply filter to image, `f=colorize,0,255,0,0` makes image more green. Supports all filters as defined in [PHP GD `imagefilter()`](http://php.net/manual/en/function.imagefilter.php). |
| `f0, f1-f9`    | Same as `filter`, just add more filters. Applied in order `f`, `f0-f9`.  |
| `sc, shortcut` | Save longer expressions in `img_config.php`. One place to change your favorite processing options, use as `sc=sepia` which is a shortcut for `&f=grayscale&f0=brightness,-10&f1=contrast,-20&f2=colorize,120,60,0,0&sharpen`. |



Documentation 
--------------------------------------

Read full documentation at:
http://dbwebb.se/opensource/cimage



Revision history
-------------------------------------


v0.6.x (latest)

* Adding compare-page for comparing images. Issue #20.
* Added option `no-upscale, nu` as resizing strategy to decline upscaling of smaller images. Fix #61.
* Minor change in `CImage::resize()`, crop now does imagecopy without resamling.
* Correcting internal details for save-as and response json which indicated wrong colors. Fix #62.
* Fixed fill-to-fit that failed when using aspect-ratio. Fix #52.
* JSON returns correct values for resulting image. Fix #58.
* Corrected behaviour for skip-original. Fix #60.


v0.6 (2014-12-06)

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
