Image conversion on the fly using PHP
=====================================

The `CImage.php` is a class that can resize and crop images and output them to 
a webpage. The class preserves a cache of the generated images and responds with 
HTTP 304 if the image has not changed.

The file `img.php` uses `CImage.php` to resize images. It is a usecase on how to use
the class. `img.php` is useful for webpages which want to dynamically resize the images.

The file `test.php` has testcases that show the results of `img.php` with different
settings.

The file `example.php` makes an example on how to use and integrate `img.php` with your 
website and shows why this might be a handy tool for content providers.

Start by reading the `example.php`, proceed by reviewing the `test.php`, then have a look 
at `img.php` and finally go through `CImage.php`.

Enjoy!

Mikael Roos (mos@dbwebb.se)


Revision history
----------------

ToDo.

* Pre-defined sizes.
* crop-to-fit, add parameter for offset x and y to enable to define which area is the 
center of the image from which the crop is done.
* Show how to integrate with WordPress.

v0.3 (2012-08-28)

* Added crop. Can crop a area (`width`, `height`, `start_x`, `start_y`) from the original
image.
* Corrected to make the 304 Not Modified header work.
* Added `example.php`to walk through a real live example on how to use `img.php` in a 
website.
 
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
