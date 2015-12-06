<?php
// Include config for all testcases
include __DIR__ . "/config.php";



// The title of the test case
$title = "Testing resize landscape image";



// Provide a short description of the testcase.
$description = "Resize landscape image";



// Use these images in the test
$images = array(
    'car.png',
);



// For each image, apply these testcases
$nc = empty($_SERVER['QUERY_STRING']) ? "" : "&" . $_SERVER['QUERY_STRING'];

$testcase = array(
    $nc . '&w=500',
    $nc . '&h=200',
    $nc . '&w=500&h=500',
    $nc . '&w=500&h=200',
    $nc . '&w=500&h=200&crop-to-fit',
    $nc . '&w=200&h=500&crop-to-fit',
    $nc . '&w=500&h=200&fill-to-fit',
    $nc . '&w=200&h=500&fill-to-fit',
    $nc . '&w=500&h=200&stretch',
    $nc . '&w=200&h=500&stretch',
);



// Apply testcases and present results
include __DIR__ . "/template.php";
