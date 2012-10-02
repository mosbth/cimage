Image conversion on the fly using PHP
=====================================

About
-------------------------------------

The `CImage.php` is a PHP class that can resize and crop images on the fly on the server side 
and output them to, for example to a webpage. The class preserves a cache of the generated 
images and responds with HTTP 304 (not modified) if the image has not changed.

The file `img.php` uses `CImage.php` to resize images. It is a usecase on how to use
the class. `img.php` is useful for webpages which want to dynamically resize the images.

The file `test.php` has testcases that show the results of `img.php` with different
settings.

Start by reviewing the `test.php`, then have a look at `img.php` and finally go through 
`CImage.php`.

CImage lives at github: https://github.com/mosbth/cimage

You can try out a live example at: http://dbwebb.se/kod-exempel/cimage/

Enjoy!

Mikael Roos (me@mikaelroos.se)


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


Revision history
-------------------------------------

ToDo.

* Improved support for pre-defined sizes.
* crop-to-fit, add parameter for offset x and y to enable to define which area is the 
center of the image from which the crop is done.
* Show how to integrate with WordPress, shortcodes.
* Support for resizing opaque images.
* Clean up code in `CImage.php`.
* Better errorhandling for invalid dimensions.
* Crop-to-fit does not work.


v0.3x (latest)

* Corrected error on naming cache-files using subdir.
* Corrected calculation error on width & height for crop-to-fit.


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
..:  Copyright 2012 by Mikael Roos (me@mikaelroos.se)
