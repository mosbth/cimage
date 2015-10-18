<?php
// Include config for all testcases
include __DIR__ . "/config.php";



// The title of the test case
$title = "Testing issue 100 - Dummy images";



// Provide a short description of the testcase.
$description = "Create dummy images.";



// Use these images in the test
$images = array(
    'dummy',
);



// For each image, apply these testcases 
$testcase = array(
    '&nc&so',
    '&nc&width=300',
    '&nc&height=300',
    '&nc&width=300&height=300',
    '&nc&bgc=006600',
);



// Apply testcases and present results
include __DIR__ . "/template.php";
