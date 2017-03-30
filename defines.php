<?php
// Version of cimage and img.php
define("CIMAGE_VERSION", "v0.7.19 (2017-03-31)");

// For CRemoteImage
define("CIMAGE_USER_AGENT", "CImage/" . CIMAGE_VERSION);

// Image type IMG_WEBP is only defined from 5.6.25
if (!defined("IMG_WEBP")) {
    define("IMG_WEBP", -1);
}
