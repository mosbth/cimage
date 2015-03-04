<?php
$script = <<<EOD
CImage.compare({
    "input1": "../img.php?src=car.png",
    "input2": "../img.php?src=car.png&sharpen",
    "input3": "../img.php?src=car.png&blur",
    "input4": "../img.php?src=car.png&emboss",
    "json": true,
    "stack": false
});
EOD;

include __DIR__ . "/compare.php";
