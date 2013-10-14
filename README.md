Image conversion on the fly using PHP
=====================================

About
-------------------------------------

`CImage` is a PHP class which enables scaling, cropping, filtering effects and processing of images using PHP GD. The script `img.php` uses `CImage` to enable server-side image processing together with caching and optimization of the processed images.

Server-side image processing is a useful tool for any web developer, `img.php` has an easy to use interface and its quite powerful when you integrate it with your website. This is a most useful tool for any web developer who has a need to create and process images for a website.

This is free software and open source.

Read more on http://dbwebb.se/opensource/cimage

Enjoy!

Mikael Roos (me@mikaelroos.se)


License
-------------------------------------

License according to MIT.



Installation and get going
-------------------------------------

**Latest stable version is v0.4 released 2013-10-08.**

```bash
git clone git://github.com/mosbth/cimage.git
cd cimage
git checkout v0.4
```

Make the cache-directory writable by the webserver.

```bash
chmod 777 cache
```

Try it out by pointing your browser to the test file `test.php`.

Review the settings in `img.php`.



Usage
-------------------------------------

| Parameter      | Explained                                    | 
|----------------|----------------------------------------------|
| `src`          | `src=img.png` choses the source image to use. |
| `h, height`    | `h=200` sets the width to be to max 200px. `h=25%` sets the height to 25% of its original height. |
| `w, width`     | `w=200` sets the height to be max 200px. `w=100%` sets the width to 100% of its original width. |
| `ar, aspect-ratio` | Use this as aspect ratio. Use together with either height or width or alone to base calculations on original image dimensions. This setting is used to calculate the resulting dimension for the image. `w=160&aspect-ratio=1.6` results in a width of 100px. |
| `s, scale`     | Scale the image to a size proportional to a percentage of its original size, `scale=25` makes a image 25% of its original size and `size=200` doubles up the image size. Scale is applied before all processing and has no impact of the final width and height. |
| `nr, no-ratio, stretch` | Do *not* keep aspect ratio when resizing using both width & height constraints. Results in stretching the image, if needed, to fit in the resulting box. |
| `cf, crop-to-fit`  | Set together with both `h` & `w` to make the image fit into dimensions, and crop out the rest of the image. |
| `a, area`      | Define the area of the image to work with. Set `area=10,10,10,10` (top,right,bottom,left) to crop out the 10% of the outermost area. It works like an offset to define which part of the image you want to process. Its an alternative to use `crop`. |
| `c, crop`      | Crops an area from the original image, set width, height, start_x and start_y to define the area to crop, for example `crop=100,100,10,10` (`crop=width,height,start_x,start_y`). Left top corner is 0, 0. You can use left, right or center when setting start_x. You may use top, bottom or center when setting start_y. Use 0 for width or height to get the width/height of the original image. Use negative values for width/height to get original width/height minus selected value. |
| `q, quality`   | Quality affects lossy compression and file size for JPEG images by setting the quality between 1-100, default is 60.  Quality has no effect on PNG or GIF. |
| `d, deflate`   | For PNG images it defines the compression algorithm, values can be 1-9, default is defined by PHP GD. Quality has no effect on JPEG or GIF. |
| `sharpen`      | Appy a filter that sharpens the image.       |
| `emboss`       | Appy a filter with an emboss effect.         |
| `blur`         | Appy a filter with a blur effect.            |
| `f, filter`    | Apply filter to image, `f=colorize,0,255,0,0` makes image more green. Supports all filters as defined in [PHP GD `imagefilter()`](http://php.net/manual/en/function.imagefilter.php). |
| `f0, f1-f9`    | Same as `filter`, just add more filters. Applied in order `f`, `f0-f9`.  |
| `p, palette`       | Create a palette version of the image with up to 256 colors. |
| `sa, save-as`      | Save resulting image as JPEG, PNG or GIF, for example `?src=river.png&save-as=gif`. |
| `nc, no-cache`     | Do not use the cached version, do all image processing and save a new image to cache. |
| `so, skip-original`| Skip using the original image, always process image, create and use a cached version of the original image. |
| `v, verbose`   | Do verbose output and print out a log what happens. Good for debugging, analyzing what happens or analyzing the image being processed. |

Combine the parameters to get the desired behavior and resulting image. For example, take the original image, resize it, apply a sharpen effect, save the image as JPEG and use quality 30.

`img.php?src=kodim13.png&w=600&sharpen&save-as=jpg&q=30`



Revision history
-------------------------------------

v0.4.x (latest)

* Changed => to == on Modified-Since.
* Always send Last-Modified-Header.
* Added `htmlentities()` to verbose output.
* Fixed support for jpeg, not only jpg.
* Fixed crop whole image by setting crop=0,0,0,0
* Use negative values for crop width & height to base calulation on original width/height and withdraw selected amount.
* Correcting jpeg when setting quality.


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
..:  Copyright 2012-2013 by Mikael Roos (me@mikaelroos.se)
</pre>
