<?php
// Include config for all testcases
include __DIR__ . "/config.php";



// The title of the test case
$title = "Testing issue 85 - Load images in a more generic manner";



// Provide a short description of the testcase.
$description = "Do not depend on file extension while loading images.";



// Use these images in the test
$images = array(
    'car-gif',
    'car-jpg',
    'car-png',
);



// For each image, apply these testcases 
$testcase = array(
    '&nc&so',
    '&nc&sa=gif',
    '&nc&sa=jpg',
    '&nc&sa=png',
);



// Apply testcases and present results
include __DIR__ . "/template.php";
