<?php
// Include config for all testcases
include __DIR__ . "/config.php";



// The title of the test case
$title = "Testing issue 52 - Fill to fit fails with aspect ratio";



// Provide a short description of the testcase.
$description = "Verify that Fill To Fit resize strategy works with all variants of sizes.";



// Use these images in the test
$images = array(
    'car.png',
);



// For each image, apply these testcases
$nc = '&nc'; 
$testcase = array(
    $nc . '&w=300&h=300&stretch',
    $nc . '&w=300&ar=1.1&stretch',
    $nc . '&w=300&ar=3&stretch',
    $nc . '&h=300&ar=1.1&stretch',
    $nc . '&h=300&ar=3&stretch',
    $nc . '&w=50%&ar=1.1&stretch',
    $nc . '&w=50%&ar=3&stretch',
    $nc . '&h=50%&ar=1.1&stretch',
    $nc . '&h=50%&ar=3&stretch',
);



// Apply testcases and present results
include __DIR__ . "/template.php";
