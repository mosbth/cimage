<?php
// Include config for all testcases
include __DIR__ . "/config.php";



// The title of the test case
$title = "Testing issue 49 - flexible convolution";



// Provide a short description of the testcase.
$description = "Creating shortcuts to custom convolutions by using configurable list of constant convolutions.";



// Use these images in the test
$images = array(
    'kodim08.png',
);



// For each image, apply these testcases 
$testcase = array(
    '&nc&width=400&convolve=lighten',
    '&nc&width=400&convolve=darken',
    '&nc&width=400&convolve=sharpen',
    '&nc&width=400&convolve=emboss',
    '&nc&width=400&convolve=blur',
    '&nc&width=400&convolve=blur:blur',
    '&nc&width=400&convolve=blur:blur:blur:blur',
);



// Apply testcases and present results
include __DIR__ . "/template.php";
