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

**Latest stable version is v0.6.1 released 2015-01-08.**

```bash
git clone git://github.com/mosbth/cimage.git
cd cimage
git checkout v0.6.1
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



```
 .
..:  Copyright 2012-2015 by Mikael Roos (me@mikaelroos.se)
```
