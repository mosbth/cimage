<!doctype html>
<head>
  <meta charset='utf-8'/>
  <title>Testing img resizing using CImage.php</title>
</head>
<body>
<h1>Testing <code>CImage.php</code> through <code>img.php</code></h1>

<h2>Testcases</h2>

<?php
$testcase = array(
  array('text'=>'Original image', 'query'=>''),
  array('text'=>'Crop out a rectangle of 100x100, start by position 200x200.', 'query'=>'&crop=100,100,200,200'),
  array('text'=>'Crop out a full width rectangle with height of 200, start by position 0x100.', 'query'=>'&crop=0,200,0,100'),
  array('text'=>'Max width 200.', 'query'=>'&w=200'),
  array('text'=>'Max height 200.', 'query'=>'&h=200'),
  array('text'=>'Max width 200 and max height 200.', 'query'=>'&w=200&h=200'),
  array('text'=>'No-ratio makes image fit in area of width 200 and height 200.', 'query'=>'&w=200&h=200&no-ratio'),
  array('text'=>'Crop to fit in width 200 and height 200.', 'query'=>'&w=200&h=200&crop-to-fit'),
  array('text'=>'Crop to fit in width 200 and height 100.', 'query'=>'&w=200&h=100&crop-to-fit'),
  array('text'=>'Crop to fit in width 100 and height 200.', 'query'=>'&w=100&h=200&crop-to-fit'),
  array('text'=>'Quality 70', 'query'=>'&w=200&h=200&quality=70'),
  array('text'=>'Quality 40', 'query'=>'&w=200&h=200&quality=40'),
  array('text'=>'Quality 10', 'query'=>'&w=200&h=200&quality=10'),
  array('text'=>'Filter: Negate', 'query'=>'&w=200&h=200&f=negate'),
  array('text'=>'Filter: Grayscale', 'query'=>'&w=200&h=200&f=grayscale'),
  array('text'=>'Filter: Brightness 90', 'query'=>'&w=200&h=200&f=brightness,90'),
  array('text'=>'Filter: Contrast 50', 'query'=>'&w=200&h=200&f=contrast,50'),
  array('text'=>'Filter: Colorize 0,255,0,0', 'query'=>'&w=200&h=200&f=colorize,0,255,0,0'),
  array('text'=>'Filter: Edge detect', 'query'=>'&w=200&h=200&f=edgedetect'),
  array('text'=>'Filter: Emboss', 'query'=>'&w=200&h=200&f=emboss'),
  array('text'=>'Filter: Gaussian blur', 'query'=>'&w=200&h=200&f=gaussian_blur'),
  array('text'=>'Filter: Selective blur', 'query'=>'&w=200&h=200&f=selective_blur'),
  array('text'=>'Filter: Mean removal', 'query'=>'&w=200&h=200&f=mean_removal'),
  array('text'=>'Filter: Smooth 2', 'query'=>'&w=200&h=200&f=smooth,2'),
  array('text'=>'Filter: Pixelate 10,10', 'query'=>'&w=200&h=200&f=pixelate,10,10'),
  array('text'=>'Multiple filter: Negate, Grayscale and Pixelate 10,10', 'query'=>'&w=200&h=200&&f=negate&f0=grayscale&f1=pixelate,10,10'),
  array('text'=>'Crop with width & height and crop-to-fit with quality and filter', 'query'=>'&crop=100,100,100,100&w=200&h=200&crop-to-fit&q=70&f0=grayscale'),
);
?>

<h3>Test case with image <code>wider.jpg</code></h3>
<table>
<caption>Test case with image <code>wider.jpg</code></caption>
<thead><tr><th>Testcase:</th><th>Result:</th></tr></thead>
<tbody>
<?php
foreach($testcase as $key => $val) {
  $url = "../img.php?src=wider.jpg{$val['query']}";
  echo "<tr><td id=w$key><a href=#w$key>$key</a></br>{$val['text']}</br><code><a href='$url'>".htmlentities($url)."</a></code></td><td><img src='$url' /></td></tr>";
}
?>
</tbody>
</table>

<h3>Test case with image <code>higher.jpg</code></h3>
<table>
<caption>Test case with image <code>higher.jpg</code></caption>
<thead><tr><th>Testcase:</th><th>Result:</th></tr></thead>
<tbody>
<?php
foreach($testcase as $key => $val) {
  $url = "../img.php?src=higher.jpg{$val['query']}";
  echo "<tr><td id=h$key><a href=#h$key>$key</a></br>{$val['text']}</br><code><a href='$url'>".htmlentities($url)."</a></code></td><td><img src='$url' /></td></tr>";
}
?>
</tbody>
</table>


</body>
</html>