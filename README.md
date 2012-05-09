Image conversion on the fly using PHP
=====================================

The `CImage.php` contains a class that can resize and crop images and output them to
a webpage. The class has cache of generated images.

The file `img.php` uses `CImage.php` to resize images. It is a usecase on how to use
the class.

The file `test.php` has some testcases that show the results of `img.php` with different
settings.

Start by reviewing the `test.php`, then have a look at `img.php` and finally go through 
`CImage.php`.

Enjoy.

Mikael Roos (mos@dbwebb.se)


Revision history
----------------

ToDo.

* crop
* Pre-defined sizes.


v0.2 (2012-05-09) 

* Implemented filters as in http://php.net/manual/en/function.imagefilter.php
* Changed `crop` to `crop_to_fit`, woks the same way.
* Changed arguments to method and sends them in array.
* Added quality-setting.
* Added testcases for above.

v0.1.1 (2012-04-27) 

* Corrected calculation where both width and height were set.


v0.1 (2012-04-25) 

* Initial release after rewriting some older code I had lying around.
