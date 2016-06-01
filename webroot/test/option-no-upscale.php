<?php
// Include config for all testcases
include __DIR__ . "/config.php";



// The title of the test case
$title = "Testing option no-upscale";



// Provide a short description of the testcase.
$description = "Do not upscale image when original image (slice) is smaller than target image.";



// Use these images in the test
$images = array(
    'car.png',
    'apple.jpg',
    'tower.jpg',
);



// For each image, apply these testcases
$nc = "&bgc=660000&nc"; //null; //"&nc"; //null; //&nc'; 
$testcase = array(
    $nc . '&w=600',
    $nc . '&w=600&no-upscale',
    $nc . '&h=420',
    $nc . '&h=420&no-upscale',
    $nc . '&w=600&h=420',
    $nc . '&w=600&h=420&no-upscale',
    $nc . '&w=700&h=420&stretch',
    $nc . '&w=700&h=420&no-upscale&stretch',
    $nc . '&w=700&h=200&stretch',
    $nc . '&w=700&h=200&no-upscale&stretch',
    $nc . '&w=250&h=420&stretch',
    $nc . '&w=250&h=420&no-upscale&stretch',
    $nc . '&w=700&h=420&crop-to-fit',
    $nc . '&w=700&h=420&no-upscale&crop-to-fit',
    $nc . '&w=700&h=200&crop-to-fit',
    $nc . '&w=700&h=200&no-upscale&crop-to-fit',
    $nc . '&w=250&h=420&crop-to-fit',
    $nc . '&w=250&h=420&no-upscale&crop-to-fit',
    $nc . '&w=600&h=500&fill-to-fit',
    $nc . '&w=600&h=500&no-upscale&fill-to-fit',
    $nc . '&w=250&h=420&fill-to-fit',
    $nc . '&w=250&h=420&no-upscale&fill-to-fit',
    $nc . '&w=700&h=420&fill-to-fit',
    $nc . '&w=700&h=420&no-upscale&fill-to-fit',
);



// Apply testcases and present results
include __DIR__ . "/template.php";
