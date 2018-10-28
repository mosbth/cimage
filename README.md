Image conversion on the fly using PHP
=====================================

[![Join the chat at https://gitter.im/mosbth/cimage](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/mosbth/cimage?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)
[![Build Status](https://travis-ci.org/mosbth/cimage.svg?branch=master)](https://travis-ci.org/mosbth/cimage)
[![Build Status](https://scrutinizer-ci.com/g/mosbth/cimage/badges/build.png?b=master)](https://scrutinizer-ci.com/g/mosbth/cimage/build-status/master)

About
-------------------------------------

<img src="https://cimage.se/cimage/imgd.php?src=example/kodim07.png&w=200&c=140,140,520,340&sharpen"/>

`CImage` is a PHP class enabling resizing of images through scaling, cropping and filtering effects -- using PHP GD. The script `img.php` uses `CImage` to enable server-side image processing utilizing caching and optimization of the processed images.

Server-side image processing is a most useful tool for any web developer, `img.php` has an easy to use interface and its powerful when you integrate it with your website. Using it might decrease the time and effort for managing images and it might improve your workflow for creating content for websites.

This software is free and open source, licensed according MIT.



Documentation
--------------------------------------

Read full documentation at:
<strike>http://dbwebb.se/opensource/cimage</strike>

New website is being setup at [cimage.se](https://cimage.se), to improve documentation (work is ongoing).




Requirements
--------------------------------------

`CImage` and `img.php` supports GIF (with transparency), JPEG and PNG (8bit transparent, 24bit semi transparent) images. It requires PHP 5.3 and PHP GD. You optionally need the EXIF extension to support auto-rotation of JPEG-images. 

*Version v0.7.x will be the last version to support PHP 5.3. Coming version will require newer version of PHP.*



Installation
--------------------------------------

There are several ways of installing. You either install the whole project which uses the autoloader to include the various files, or you install the all-included bundle that -- for convenience -- contains all code in one script.



### Install source from GitHub 

The [sourcode is available on GitHub](https://github.com/mosbth/cimage). Clone, fork or [download as zip](https://github.com/mosbth/cimage/archive/master.zip). 

**Latest stable version is v0.7.18 released 2016-08-09.**

I prefer cloning like this. Do switch to the latest stable version.

```bash
git clone git://github.com/mosbth/cimage.git
cd cimage
git checkout v0.7.18
```

Make the cache-directory writable by the webserver.

```bash
chmod 777 cache
```


### Install all-included bundle 

There are some all-included bundles of `img.php` that can be downloaded and used without dependency to the rest of the sourcecode.

| Scriptname | Description | 
|------------|-------------|
| `imgd.php` | Development mode with verbose error reporting and option `&verbose` enabled. | 
| `imgp.php` | Production mode logs all errors to file, giving server error 500 for bad usage, option `&verbose` disabled. | 
| `imgs.php` | Strict mode logs few errors to file, giving server error 500 for bad usage, option `&verbose` disabled. | 

Dowload the version of your choice like this.

```bash
wget https://raw.githubusercontent.com/mosbth/cimage/v0.7.18/webroot/imgp.php
```

Open up the file in your editor and edit the array `$config`. Ensure that the paths to the image directory and the cache directory matches your environment, or create an own config-file for the script.



### Install from Packagist

You can install the package [`mos/cimage` from Packagist](https://packagist.org/packages/mos/cimage) using composer.



Use cases 
--------------------------------------

Lets take some use cases to let you know when and how `img.php` might be useful.



### Make a thumbnail 

<img src="https://cimage.se/cimage/imgd.php?src=example/kodim04.png&w=80&h=80&cf">

Lets say you have a larger image and you want to make a smaller thumbnail of it with a size of 80x80 pixels. You simply take the image and add constraints on `width`, `height` and you use the resize strategy `crop-to-fit` to crops out the parts of the image that does not fit.

To produce such a thumbnail, create a link like this:

> `img.php?src=kodim04.png&width=80&height=80&crop-to-fit`



### Slightly complexer use case 

Perhaps you got an image from a friend. The image was taken with the iPhone and thus rotated. 

<img src="https://cimage.se/cimage/imgd.php?src=example/issue36/me-270.jpg&w=250">

The original image is looking like this one, scaled down to a width of 250 pixels. 

So, you need to rotate it and crop off some parts to make it intresting. 

To show it off, I'll auto-rotate the image based on its EXIF-information, I will crop it to a thumbnail of 100x100 pixels and add a filter to make it greyscale finishing up with a sharpen effect. Just for the show I'll rotate the image 25 degrees - do not ask me why.

Lets call this *the URL-Photoshopper*. This is how the magic looks like. 

> `img.php?src=issue36/me-270.jpg&w=100&h=100&cf&aro`
> `&rb=-25&a=8,30,30,38&f=grayscale&convolve=sharpen-alt`

<img src="https://cimage.se/cimage/imgd.php?src=example/issue36/me-270.jpg&w=100&h=100&cf&aro&rb=-25&a=8,30,30,38&f=grayscale&convolve=sharpen-alt">

For myself, I use `img.php` to put up all images on my website, it gives me the power of affecting the resulting images - without opening up a photo-editing application.



Get going quickly 
--------------------------------------



### Check out the test page 

Try it out by pointing your browser to the test file `webroot/test/test.php`. It will show some example images and you can review how they are created.



### Process your first image 

<img src="https://cimage.se/cimage/imgd.php?src=example/kodim04.png&amp;w=w2&amp;a=40,0,50,0" alt=''>

Try it yourself by opening up an image in your browser. Start with 

> `webroot/img.php?src=kodim04.png` 

and try to resize it to a thumbnail by adding the options 

> `&width=100&height=100&crop-to-fit`



### What does "processing the image" involves? 

Add `&verbose` to the link to get a verbose output of what is happens during image processing. This is useful for developers and those who seek a deeper understanding on how it works behind the scene. 



### Check your system 

Open up `webroot/check_system.php` if you are uncertain that your system has the right extensions loaded. 




### How does it work? 

Review the settings in `webroot/img_config.php` and check out `webroot/img.php` on how it uses `CImage`.

The programatic flow, just to get you oriented in the environment, is.

1. Start in `img.php`.
2. `img.php` reads configuration details from `img_config.php` (if the config-file is available).
3. `img.php` reads and processes incoming `$_GET` arguments to prepare using `CImage`.
4. `img.php` uses `CImage`.
5. `CImage` processes, caches and outputs the image according to how its used. 

Read on to learn more on how to use `img.php`.



Basic usage 
--------------------------------------



### Select the source 

Open an image through `img.php` by using its `src` attribute.

> `img.php?src=kodim13.png`

It looks like this.

<img src="https://cimage.se/cimage/imgd.php?src=example/kodim13.png&w=w1&save-as=jpg">

All images are stored in a directory structure and you access them as:

> `?src=dir1/dir2/image.png`



### Resize using constraints on width and height 

Create a thumbnail of the image by applying constraints on width and height, or one of them.

| `&width=150`        | `&height=150`       | `&w=150&h=150`      |
|---------------------|---------------------|---------------------|
| <img src=https://cimage.se/cimage/imgd.php?src=example/kodim13.png&w=150 alt=''> | <img src=https://cimage.se/cimage/imgd.php?src=example/kodim13.png&h=150 alt=''> | <img src=https://cimage.se/cimage/imgd.php?src=example/kodim13.png&w=150&h=150 alt=''> |

By setting `width`, `height` or both, the image gets resized to be *not larger* than the defined dimensions *and* keeping its original aspect ratio.

Think of the constraints as a imaginary box where the image should fit. With `width=150` and `height=150` the box would have the dimension of 150x150px. A landscape image would fit in that box and its width would be 150px and its height depending on the aspect ratio, but for sure less than 150px. A portrait image would fit with a height of 150px and the width depending on the aspect ratio, but surely less than 150px.



### Resize to fit a certain dimension 

Creating a thumbnail with a certain dimension of width and height, usually involves stretching or cropping the image to fit in the selected dimensions. Here is how you create a image that has the exact dimensions of 300x150 pixels, by either *stretching*, *cropping* or *fill to fit*.


| What                | The image           |
|---------------------|---------------------|
| **Original.** The original image resized with a max width and max height.<br>`?w=300&h=150` | <img src=https://cimage.se/cimage/imgd.php?src=example/kodim13.png&w=300&h=150 alt=''> |
| **Stretch.** Stretch the image so that the resulting image has the defined width and height.<br>`?w=300&h=150&stretch` | <img src=https://cimage.se/cimage/imgd.php?src=example/kodim13.png&w=300&h=150&stretch alt=''> |
| **Crop to fit.** Keep the aspect ratio and crop out the parts of the image that does not fit.<br>`?w=300&h=150&crop-to-fit` | <img src=https://cimage.se/cimage/imgd.php?src=example/kodim13.png&w=300&h=150&crop-to-fit alt=''> |
| **Fill to fit.** Keep the aspect ratio and fill then blank space with a background color.<br>`?w=300&h=150&fill-to-fit=006600` | <img src=https://cimage.se/cimage/imgd.php?src=example/kodim13.png&w=300&h=150&fill-to-fit=006600 alt=''> |

Learn to crop your images, creative cropping can make wonderful images from appearingly useless originals.

Stretching might work, like in the above example where you can not really notice that the image is stretched. But usually, stretching is not that a good option since it distorts the ratio. Stretching a face may not turn out particularly well.

Fill to fit is useful when you have some image that must fit in a certain dimension and stretching nor cropping can do it. Carefully choose the background color to make a good resulting image. Choose the same background color as your website and no one will notice.



### List of parameters

`img.php` supports a lot of parameters. Combine the parameters to get the desired behavior and resulting image. For example, take the original image, resize it using width, aspect-ratio and crop-to-fit, apply a sharpen effect, save the image as JPEG using quality 30.

> `img.php?src=kodim13.png&w=600&aspect-ratio=4`
> `&crop-to-fit&sharpen&save-as=jpg&q=30`

<img src=https://cimage.se/cimage/imgd.php?src=example/kodim13.png&w=600&aspect-ratio=4&crop-to-fit&sharpen&save-as=jpg&q=30 alt=''>

Here is a list of all parameters that you can use together with `img.php`, grouped by its basic intent of usage. 


#### Mandatory options and debugging 

Option `src` is the only mandatory option. The options in this section is useful for debugging or deciding what version of the target image is used.

| Parameter      | Explained                                    | 
|----------------|----------------------------------------------|
| `src`          | Source image to use, mandatory. `src=img.png` or with subdirectory `src=dir/img.png`. |
| `nc, no-cache` | Do not use the cached version, do all image processing and save a new image to cache. |
| `so, skip-original`| Skip using the original image, always process image, create and use a cached version of the original image. |
| `v, verbose`   | Do verbose output and print out a log what happens. Good for debugging, analyzing the process and inspecting how the image is being processed. |
| `json`         | Output a JSON-representation of the image, useful for testing or optimizing when one wants to know the image dimensions, before using it. |
| `pwd, password` | Use password to protect unauthorized usage. |



#### Options for deciding width and height of target image

These options are all affecting the final dimensions, width and height, of the resulting image.

| Parameter      | Explained                                    | 
|----------------|----------------------------------------------|
| `h, height`    | `h=200` sets the width to be to max 200px. `h=25%` sets the height to max 25% of its original height. |
| `w, width`     | `w=200` sets the height to be max 200px. `w=100%` sets the width to max 100% of its original width. |
| `ar, aspect-ratio` | Control target aspect ratio. Use together with either height or width or alone to base calculations on original image dimensions. This setting is used to calculate the resulting dimension for the image. `w=160&aspect-ratio=1.6` results in a height of 100px. Use `ar=!1.6` to inverse the ratio, useful for portrait images, compared to landscape images. |
| `dpr, device-pixel-ratio` | Default value is 1, set to 2 when you are delivering the image to a high density screen, `dpr=2` or `dpr=1.4`. Its a easy way to say the image should have larger dimensions. The resulting image will be twice as large (or 1.4 times), keeping its aspect ratio. |



#### Options for resize strategy

These options affect strategy to use when resizing an image into a target image that has both width and height set.

| Parameter      | Explained                                    | 
|----------------|----------------------------------------------|
| `nr, no-ratio, stretch` | Do *not* keep aspect ratio when resizing and using both width & height constraints. Results in stretching the image, if needed, to fit in the resulting box. |
| `cf, crop-to-fit`  | Set together with both `h` and `w` to make the image fit into dimensions, and crop out the rest of the image. |
| `ff, fill-to-fit` | Set together with both `h` and `w` to make the image fit into dimensions, and fill the rest using a background color. You can optionally supply a background color as this `ff=00ff00`, or `ff=00ff007f` when using the alpha channel. |
| `nu, no-upscale` | Avoid smaller images from being upscaled to larger ones. Combine with `stretch`, `crop-to-fit` or `fill-to-fit` to get the smaller image centered on a larger canvas. The requested dimension for the target image are thereby met. |



#### Options for cropping part of image

These options enable to decide what part of image to crop out.

| Parameter      | Explained                                    | 
|----------------|----------------------------------------------|
| `c, crop`      | Crops an area from the original image, set `width`, `height`, `start_x` and `start_y` to define the area to crop, for example `crop=100,100,10,10` (`crop=width,height,start_x,start_y`). Left top corner is 0, 0. You can use `left`, `right` or `center` when setting `start_x`. You may use `top`, `bottom` or `center` when setting `start_y`. |
| `a, area`      | Define the area of the image to work with. Set `area=10,10,10,10` (`top`, `right`, `bottom`, `left`) to crop out the 10% of the outermost area. It works like an offset to define the part of the image you want to process. Its an alternative of using `crop`. |



#### General processing options

These options are general options affecting processing.

| Parameter      | Explained                                    | 
|----------------|----------------------------------------------|
| `bgc, bg-color` | Set the backgroundcolor to use (if its needed). Use six hex digits as `bgc=00ff00` and 8 digits when using the alpha channel, as this `bgc=00ff007f`. The alpha value can be between 00 and 7f. |



#### Processing of image before resizing 

This option are executed *before* the image is resized.

| Parameter      | Explained                                    | 
|----------------|----------------------------------------------|
| `s, scale`     | Scale the image to a size proportional to a percentage of its original size, `scale=25` makes an image 25% of its original size and `size=200` doubles up the image size. Scale is applied before resizing and has no impact of the target width and height. |
| `rb, rotate-before` | Rotate the image before its processed, send the angle as parameter `rb=45`. |
| `aro, auto-rotate`  | Auto rotate the image based on EXIF information (useful when using images from smartphones). |



#### Processing of image after resizing 

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
| `sc, shortcut` | Save longer expressions in `img_config.php`. One place to change your favorite processing options, use as `sc=sepia` which is a shortcut for `&f=grayscale&f0=brightness,-10&f1=contrast,-20`<br>`&f2=colorize,120,60,0,0&sharpen`. |



#### Saving image, affecting quality and file size 

Options for saving the target image.

| Parameter      | Explained                                    | 
|----------------|----------------------------------------------|
| `q, quality`   | Quality affects lossy compression and file size for JPEG images by setting the quality between 1-100, default is 60.  Quality only affects JPEG. |
| `co, compress` | For PNG images it defines the compression algorithm, values can be 0-9, default is defined by PHP GD. Compress only affects PNG. |
| `p, palette`   | Create a palette version of the image with up to 256 colors. |
| `sa, save-as`  | Save resulting image as JPEG, PNG or GIF, for example `?src=river.png&save-as=gif`. |
| `alias`        | Save resulting image as a copy in the alias-directory. |

Carry on reading to view examples on how to use and combine the parameters to achieve desired effects and target images.



The configuration settings in `_config.php`
--------------------------------------

There are several configurations settings that can be used to change the behaviour of `img.php`. Here is an overview, listed as they appear in the default config-file.

| Setting                 | Explained                                    | 
|-------------------------|----------------------------------------------|
| `mode`                  | Set to "development", "production" or "strict" to match the mode of your environment. It mainly affects the error reporting and if option `verbose` is enabled or not. |
| `autoloader`            | Path to the file containing the autoloader. | 
| `image_path`            | Path to the directory-structure containing the images. |  
| `cache_path`            | Path to the directory where all the cache-files are stored. |  
| `alias_path`            | Path to where the alias, or copy, of the images are stored. |  
| `password`              | Set the password to use. |  
| `password_always`       | Always require the use of password and match with `password`. |  
| `remote_allow`          | Allow remote download of images when `src=http://example.com/img.jpg`. |  
| `remote_pattern`        | Pattern (regexp) to detect if a file is remote or not. |  
| `valid_filename`        | A regular expression to test if a `src` filename is valid or not. |  
| `valid_aliasname`       | A regular expression to test if a `alias` filename is valid or not. |  |  
| `img_path_constraint`   | Check that the target image is in a true subdirectory of `img-path` (disables symbolic linking to another part of the filesystem. |  
| `default_timezone`      | Use to set the timezone if its not already set. |  
| `max_width`             | Maximal width of the target image. Fails for larger values. | 
| `max_height`            | Maximal height of the target image. Fails for larger values. | 
| `background_color`      | Specify a default background color and overwrite the one proposed by `CImage`. |  
| `png_filter`            | Use (or not) an external command for filter PNG images. |  
| `png_filter_cmd`        | Path and options to the actual external command. |  
| `png_deflate`           | Use (or not) an external command for deflating PNG images. |  
| `png_deflate_cmd`       | Path and options to the actual external command. |  
| `jpeg_optimize`         | Use (or not) an external command for optimizing JPEG images. |  
| `jpeg_optimize_cmd`     | Path and options to the actual external command. |  
| `convolution_constant`  | Constants for own defined convolution expressions. |  
| `allow_hotlinking`      | Allow or disallow hotlinking7leeching of images. |  
| `hotlinking_whitelist`  | Array of regular expressions that allow hotlinking (if hotlinking is disabled). |  
| `shortcut`              | Define own shortcuts for more advanced combination of options to `img.php`. |  
| `size_constant`         | Create an array with constant values to be used instead of `width` and `height`. |  
| `aspect_ratio_constant` | Create an array for constant values to be used with option 'aspect-ratio`. |  

Consult the file `webroot/img-config.php` for a complete list together with the default values for each configuration setting. There is an [appendix where you can see the default config-file](#img-config).



### Create and name the config file 

The file `img.php` looks for the config-file `img_config.php`, and uses it if its found. The three files where everything is included -- `imgd.php`, `imgp.php` and `imgs.php` -- includes an empty `$config`-array which can be overridden by saving a config-file in the same directory. If the script is `imgp.php` then name the config-file `imgp_config.php` and it will find it and use those settings. 



Debugging image processing 
--------------------------------------

You can visualize what happens during image processing by adding the `v, verbose` parameter. It will then display the resulting image together with a verbose output on what is actually happening behind the scene.

<img src="http://dbwebb.se/image/snapshot/CImage_verbose_output.jpg?w=w2&q=60&sharpen">

This can be most useful for debugging and to understand what actually happen.

The parameter `nc, no-cache` ignores the cached item and will always create a new cached item.

The parameter `so, skip-original` skips the original image, even it that is a best fit. As a result a cached image is created and displayed.



A JSON representation of the image
--------------------------------------

You can ge a JSON representation of the image by adding the option `json`. This can be useful if you need to know the actual dimension of the image. 

For example, the following image is created like this:

> `&w=300&save-as=jpg`

<img src="https://cimage.se/cimage/imgd.php?src=example/kodim24.png&w=300&save-as=jpg" alt=''>

Its JSON-representation is retrieved like this:

> `&w=300&save-as=jpg&json`

Which gives the following result.

```php
{  
    "src":"kodim24.png",
    "srcGmdate":"Wed, 12 Feb 2014 13:46:19",
    "cache":"_._kodim24_300_200_q60.jpg",
    "cacheGmdate":"Sat, 06 Dec 2014 14:09:50",
    "filename":"_._kodim24_300_200_q60.jpg",
    "width":300,
    "height":200,
    "aspectRatio":1.5,
    "size":11008,
    "colors":25751
}
```

I'll use this feature for ease testing of `img.php` and `CImage`. But the feature can also be useful when one really want complete control over the resulting dimension of an image.




Implications and considerations 
--------------------------------------

Here are some thoughts when applying `img.php` on a live system.



### Select the proper mode 

Select the proper mode for `img.php`. Set it to "strict" or "production" to prevent outsiders to get information about your system. Use only "development" for internal use since its quite verbose in its nature of error reporting. 



### Put the installation directory outside web root 

Edit the config file to put the installation directory -- and the cache directory -- outside of the web root. Best practice would be to store the installation directory and cache, outside of the web root. The only thing needed in the web root is `img.php` and `img_config.php` (if used) which can be placed, for example, in `/img/img.php` or just as `/img.php`.



### Friendly urls through `.htaccess` 

Use `.htaccess`and rewrite rules (Apache) to get friendly image urls. Put `img.php` in the `/img` directory. Put the file `.htaccess` in the web root.

**.htaccess for `img.php`.**

```php
#
# Rewrite to have friendly urls to img.php, edit it to suite your environment.
#
# The example is set up as following.
#
#  img                 A directory where all images are stored
#  img/me.jpg          Access a image as usually.
#  img/img.php         This is where I choose to place img.php (and img_config.php).
#  image/me.jpg        Access a image though img.php using htaccess rewrite.
#  image/me.jpg?w=300  Using options to img.php.
# 
# Subdirectories also work.
#  img/me/me.jpg          Direct access to the image.
#  image/me/me.jpg        Accessed through img.php.
#  image/me/me.jpg?w=300  Using options to img.php.
#
RewriteRule ^image/(.*)$        img/img.php?src=$1 [QSA,NC,L]
```

You can now access all images through either `/image/car.jpg` (which uses `img.php`) or as usual through `/img/car.jpg` without passing through `img.php`. You send the arguments as usual.

> `/image/car.jpg?w=300&sharpen`

Or a image that resides in a subdirectory.

> `/image/all-cars/car.jpg?w=300&sharpen`

The result is good readable urls to your images. Its easy for the search engine to track and you can use the directory structure already existing in `/img`. Just like one wants to have it.



### Monitor cache size

There is a utility `cache.bash` included for monitoring the size of the cache-directory. It generates an output like this.

```bash
$ ./cache.bash
Usage: ./cache.bash [cache-dir]   

$ ./cache.bash cache                         
Total size:       27M                                            
Number of files:  225                                            
                                                                 
Top-5 largest files:                                             
1032    cache/_._kodim08_768_512_q60convolvesharpen.png          
960     cache/_._kodim08_768_512_q60convolveemboss.png           
932     cache/_._kodim08_768_512_q60_rb45.png                    
932     cache/_._kodim08_768_512_q60_ra45.png                    
856     cache/_._kodim08_768_512_q60_rb90.png                    
                                                                 
Last-5 created files:                                            
2014-11-26 16:51 cache/_._kodim08_768_512_q60convolvelighten.png 
2014-11-26 16:51 cache/_._kodim08_768_512_q60convolveblur.png    
2014-11-26 16:48 cache/_._kodim08_400_267_q60convolvesharpen.png 
2014-11-26 16:48 cache/_._kodim08_400_267_q60convolvelighten.png 
2014-11-26 16:48 cache/_._kodim08_400_267_q60convolveemboss.png  
                                                                 
Last-5 accessed files:                                           
2014-11-27 16:12 _._wider_900_581_q60.jpg                        
2014-11-27 16:12 _._wider_750_484_q60.jpg                        
2014-11-27 16:12 _._wider_640_413_q60.jpg                        
2014-11-27 16:12 _._wider_640_200_c640-200-0-100_q60.jpg         
2014-11-27 16:12 _._wider_600_387_q60.jpg                        
```

Use it as a base if you feel the need to monitor the size och the cache-directory.



### Read-only cache

The cache directory need to be writable for `img.php` to create new files. But its possible to first create all cache-files and then set the directory to be read-only. This will give you a way of shutting of `img.php` from creating new cache files. `img.php` will then continue to work for all images having a cached version but will fail if someone tries to create a new, not previously cached, version of the image.



### Post-processing with external tools

You can use external tools to post-process the images to optimize the file size. This option is available for JPEG and for PNG images. Post-processing is disabled by default, edit `img_config.php` to enable it.

It takes additional time to do post processing, it can take up to a couple of seconds. This is processing to create the cached image, thereafter the cached version will be used and no more post processing needs to be done.

These tools for post processing is not a part of `CImage` and `img.php`, you need to download and install them separately. I use them myself on my system to get an optimal file size.



### Allowing remote download of images

You can allow `img.php` to download remote images. That can be enabled in the config-file. However, before doing so, consider the implications on allowing anyone to download a file, hopefully an image, to your server and then the possibility to access it through the webserver.

That sounds scary. It should.

For my own sake I will use it like this, since I consider it a most useful feature.

* Create a special version of `img.php` that has remote download allowed, hide it from public usage.
* Always use a password.
* Download and process the image and save it as an `alias`.
* Integrate the image into your webpage and use the image in the alias directory.

This is an easy way to quickly download a remote image, process and share it.

So, its a scary feature and I might regret I did put it in. Still, its disabled by default and you enable it on your own risk. I have tried to make it as secure as I can, but I might have missed something. I will run it on my own system so I guess I'll find out how secure it is.



Community 
--------------------------------------

There is a Swedish forum where you can ask questions, even in English. The forum is a general forum for education in web development, it is not specific for this software. 

Ask questions on `CImage` and `img.php` [in the PHP sub forum]([BASEURL]forum/viewforum.php?f=12).

Or ask it on GitHub by creating an issue -- that would be the best place to ask questions.

Or if you fancy irc.

* `irc://irc.bsnet.se/#db-o-webb`
* `irc://irc.freenode.net/#dbwebb`



Trouble- and feature requests 
--------------------------------------

Use [GitHub to report issues](https://github.com/mosbth/cimage/issues). Always include the following.

1. Describe very shortly: What are you trying to achieve, what happens, what did you expect.
2. Parameter list used for `img.php`.
3. The image used.

If you request a feature, describe its usage and argument for why you think it fits into `CImage` and `img.php`.

Feel free to fork, clone and create pull requests.




```
 .
..:  Copyright 2012-2015 by Mikael Roos (me@mikaelroos.se)
```
