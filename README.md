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



Installation
-------------------------------------

1. Clone from github: `git://github.com/mosbth/cimage.git`

2. Make the cache directory writable by the webserver.

<pre><code>
chmod 777 cache
</code></pre>

3. Point your browser to `test.php`.

4. Review the settings in `img.php` and try it out.

5. Advanced usage. Put `img.php` in your `/img`-directory. Create a `.htaccess` in your
web root folder containing the following line:

<pre><code>
RewriteEngine on 
RewriteRule ^image/(.*)$ img/img.php?src=$1 [QSA,NC,L]
</code></pre>

Now you can access and resize your images through `/image/someimage.jpg?w=80`. Very handy.



Usage
-------------------------------------

| Parameter      | Explained                                    | 
|----------------|----------------------------------------------|
| `src`          | `src=img.png` choses the source image to use. |
| `h, height`    | `h=200` sets the width to be to max 200px. `h=25%` sets the height to 25% of its original height. |
| `w, width`     | `w=200` sets the height to be max 200px. `w=100%` sets the width to 100% of its original width. |
| `ar, aspect-ratio` | Use this as aspect ratio. Use together with either height or width or alone to base calculations on original image dimensions. This setting is used to calculate the resulting dimension for the image. `w=160&aspect-ratio=1.6` results in a width of 100px. |


`img.php?src=image.jpg&sharpen`

-v, -verbose, Do verbose output and print out a log what happens.
-no-cache, Do not use the cached version, do all conversions.
-skip-original, Skip using the original image, always resize and use cached image.
-save-as, Save image as jpg, png or gif, loosing transparency.

-sharpen to appy a filter that sharpens the image. Good to apply when resizing to smaller dimensions.
-emboss to apply a emboss effect.
-blur to apply a blur effect.

-palette to create a palette version of the image with up to 256 colors.



Revision history
-------------------------------------

ToDo.

* Show how to integrate with WordPress, shortcodes.
* Clean up code in `CImage.php`.
* Better errorhandling for invalid dimensions.
* Define the color of the background of the resulting image, when loosing transparency.


v0.3.x (latest)

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


v0.3 (2012-10-02)

* Added crop. Can crop a area (`width`, `height`, `start_x`, `start_y`) from the original
image.
* Corrected to make the 304 Not Modified header work.
* Pre-defined sizes can be configured for width in `img.php`.
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

* Initial release after rewriting some older code I had lying around.

 .   
..:  Copyright 2012-2013 by Mikael Roos (me@mikaelroos.se)
