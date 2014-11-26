<?php
// Include config for all testcases
include __DIR__ . "/config.php";



// The title of the test case
$title = "Testing issue 36 - autoRotate";



// Provide a short description of the testcase.
$description = "";



// Use these images in the test
$images = array(
    'issue36/me-0.jpg',
    'issue36/me-90.jpg',
    'issue36/me-180.jpg',
    'issue36/me-270.jpg',
    'issue36/flower-0.jpg',
    'issue36/flower-90.jpg',
    'issue36/flower-180.jpg',
    'issue36/flower-270.jpg',
);



// For each image, apply these testcases 
$testcase = array(
    '&aro&nc',
    '&aro&nc&w=200',
    '&aro&nc&h=200',
    '&aro&nc&w=200&h=200&cf',
);



// Applu testcases and present results
include __DIR__ . "/template.php";
