<!doctype html>
<head>
  <meta charset='utf-8'/>
  <title>Testing img for issue 40 - no ratio</title>
  <style>
  body {background-color: #ccc;}
  </style>
  <script src="https://code.jquery.com/jquery-2.1.1.min.js"></script>

<script type="text/javascript">
window.getDetails = function (url, id) {
  $.getJSON(url, function(data) {
    element = document.getElementById(id);
    element.innerHTML = "width: " + data.width + "\nheigh: " + data.height + "\naspect-ratio: " + data.aspectRatio;
  });  
}
</script>

</head>
<body>
<h1>Testing issue 40 - no ratio</h1>

<?php
error_reporting(-1);              // Report all type of errors
ini_set('display_errors', 1);     // Display all errors 

$imgphp = "../img.php?src=";

$images = array(
  'issue40/source.jpg',
);


$testcase = array(
  '&nc&width=652&height=466',
  '&nc&width=652&height=466&no-ratio',
  '&nc&width=652&height=466&crop-to-fit',
  '&nc&width=652&aspect-ratio=1.4',
  '&nc&width=652&aspect-ratio=1.4&no-ratio',
  '&nc&width=652&aspect-ratio=1.4&crop-to-fit',
);
?>


<h2>Images used in test</h2>

<p>The following images are used for this test.</p>

<?php foreach($images as $image) : ?>
  <p>
    <code>
      <a href="img/<?=$image?>"><?=$image?></a> 
      <a href="<?=$imgphp . $image . '&json'?>">(json)</a>
      <a href="<?=$imgphp . $image . '&verbose'?>">(verbose)</a>
    </code>
    <br>
  <img src="<?=$imgphp . $image?>"></p>
  <p></p>

<pre id="<?=$image?>"></pre>
<script type="text/javascript">window.getDetails("<?=$imgphp . $image . '&json'?>", "<?=$image?>")</script>

<?php endforeach; ?>



<h2>Testcases used for each image</h2>

<p>The following testcases are used for each image.</p>

<?php foreach($testcase as $tc) : ?>
  <code><?=$tc?></code><br>
<?php endforeach; ?>



<h2>Applying testcase for each image</h2>

<?php 
$ch1 = 1;
foreach($images as $image) :
?>
<h3><?=$ch1?>. Using source image <?=$image?></h3>

<p>
  <code>
    <a href="img/<?=$image?>"><?=$image?></a> 
    <a href="<?=$imgphp . $image . '&json'?>">(json)</a>
    <a href="<?=$imgphp . $image . '&verbose'?>">(verbose)</a>
  </code>
  <br>
  <img src="<?=$imgphp . $image?>">
</p>

<pre id="<?=$ch1?>"></pre>
<script type="text/javascript">window.getDetails("<?=$imgphp . $image . '&json'?>", "<?=$ch1?>")</script>

<?php 
$ch2 = 1;
foreach($testcase as $tc) : 
$tcId = "$ch1.$ch2";
?>
<h4>Testcase <?=$tcId?>: <?=$tc?></h4>

<p>
  <code>
    <a href="<?=$imgphp . $image . $tc?>"><?=$image . $tc?></a> 
    <a href="<?=$imgphp . $image . $tc . '&json'?>">(json)</a>
    <a href="<?=$imgphp . $image . $tc . '&verbose'?>">(verbose)</a>
  </code>
  <br>
  <img src="<?=$imgphp . $image . $tc?>">
</p>

<pre id="<?=$tcId?>"></pre>
<script type="text/javascript">window.getDetails("<?=$imgphp . $image . $tc . '&json'?>", "<?=$tcId?>")</script>

<?php $ch2++; endforeach; ?>
<?php $ch1++; endforeach; ?>

