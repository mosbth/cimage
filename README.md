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

v0.1 (2012-04-25) 

* Initial release after rewriting some older code I had lying around.
