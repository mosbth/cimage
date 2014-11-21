<?php
$angle = 45;
?><!doctype html>
<head>
  <meta charset='utf-8'/>
  <title>Testing img for issue 36 - rotateBefore, rotateAfter <?=$angle?></title>
  <style>
  body {background-color: #ccc;}
  </style>
</head>
<body>
<h1>Testing issue 36 - rotateBefore, rotateAfter <?=$angle?></h1>

<?php
error_reporting(-1);              // Report all type of errors
ini_set('display_errors', 1);     // Display all errors 
ini_set('output_buffering', 0);   // Do not buffer outputs, write directly

$imgphp = "../img.php?src=";

$images = array(
  'kodim08.png',
  'kodim04.png',
);

$testcase = array(
  "&rb=$angle&bgc=ffffff&nc",
  "&rb=$angle&bgc=ffffff&nc&w=200",
  "&rb=$angle&bgc=ffffff&nc&h=200",
  "&rb=$angle&bgc=ffffff&nc&w=200&h=200&cf",
  "&rb=$angle&bgc=ffffff&nc&w=200&h=200&cf&crop=200,200,center,center",
  "&ra=$angle&bgc=ffffff&nc",
  "&ra=$angle&bgc=ffffff&nc&w=200",
  "&ra=$angle&bgc=ffffff&nc&h=200",
  "&ra=$angle&bgc=ffffff&nc&w=200&h=200&cf",
  "&ra=$angle&bgc=ffffff&nc&w=200&h=200&cf&crop=200,200,center,center",
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

