<!doctype html>
<head>
  <meta charset='utf-8'/>
  <title>Testing img for issue 29</title>
  <style>
  body {background-color: #ccc;}
  </style>
</head>
<body>
<h1>Testing issue 29</h1>

<?php
error_reporting(-1);              // Report all type of errors
ini_set('display_errors', 1);     // Display all errors 
ini_set('output_buffering', 0);   // Do not buffer outputs, write directly

echo "<p>Version of PHP is: " . phpversion();

$imgphp = "../img.php?src=";

$images = array(
  'issue29/400x265.jpg',
  'issue29/400x268.jpg',
  'issue29/400x300.jpg',
  'issue29/465x304.jpg',
  'issue29/640x273.jpg',
);


$testcase = array(
  '&w=300&cf&q=80&nc',
  '&w=75&h=75&cf&q=80&nc',
  '&w=75&h=75&q=80',
);
?>


<h2>Images used in test</h2>

<p>The following images are used for this test.</p>

<?php foreach($images as $image) : ?>
  <p><code><a href="img/<?=$image?>"><?=$image?></a></code><br>
  <img src="<?=$imgphp . $image?>"></p>
<?php endforeach; ?>



<h2>Testcases used for each image</h2>

<p>The following testcases are used for each image.</p>

<?php foreach($testcase as $tc) : ?>
  <code><?=$tc?></code><br>
<?php endforeach; ?>



<h2>Applying testcase for each image</h2>

<?php foreach($images as $image) : ?>
<h3><?=$image?></h3>

<p><code><a href="img/<?=$image?>"><?=$image?></a></code><br>
<img src="<?=$imgphp . $image?>"></p>

<?php foreach($testcase as $tc) : ?>
<h4><?=$tc?></h4>

<p><code><a href="<?=$imgphp . $image . $tc?>"><?=$image . $tc?></a></code><br>
<img src="<?=$imgphp . $image . $tc?>"></p>

<?php endforeach; ?>
<?php endforeach; ?>

