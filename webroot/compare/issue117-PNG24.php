<?php
$script = <<<EOD
CImage.compare({
    "input1": "../img.php?src=issue117/tri_original.png",
    "input2": "../img.php?src=issue117/tri_imageresizing.png",
    "input3": "../img.php?src=issue117/tri_cimage.png",
    "input4": "../img.php?src=issue117/tri_imagemagick.png",
    "input5": "../img.php?src=issue117/tri_original.png&w=190",
    "input6": "../img.php?src=issue117/tri_original.png&w=190&no-resample",
    "json": true,
    "stack": false,
    "bg": true
});
EOD;

include __DIR__ . "/compare.php";
