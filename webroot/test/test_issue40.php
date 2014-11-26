<?php
// Include config for all testcases
include __DIR__ . "/config.php";



// The title of the test case
$title = "Testing issue 40 - no ratio";



// Provide a short description of the testcase.
$description = "Showing off how to resize image with and without ratio.";



// Use these images in the test
$images = array(
    'issue40/source.jpg',
);



// For each image, apply these testcases 
$testcase = array(
    '&nc&width=652&height=466',
    '&nc&width=652&height=466&no-ratio',
    '&nc&width=652&height=466&crop-to-fit',
    '&nc&width=652&aspect-ratio=1.4',
    '&nc&width=652&aspect-ratio=1.4&no-ratio',
    '&nc&width=652&aspect-ratio=1.4&crop-to-fit',
);



// Apply testcases and present results
include __DIR__ . "/template.php";
