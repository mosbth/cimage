<!doctype html>
<head>
  <meta charset='utf-8'/>
  <title>Testing img resizing using CImage.php</title>
</head>
<body>
<h1>Testing <code>CImage.php</code> through <code>img.php</code></h1>
<p>You can test any variation of resizing the images through <a href='img.php?src=wider.jpg&amp;w=200&amp;h=200'>img.php?src=wider.jpg&amp;w=200&amp;h=200</a> or <a href='img.php?src=higher.jpg&amp;w=200&amp;h=200'>img.php?src=higher.jpg&amp;w=200&amp;h=200</a></p>
<p>Supported arguments throught the querystring are:</p>
<ul>
<li>h, height: h=200 sets the height to 200px.
<li>w, width: w=200 sets the width to 200px.
<li>crop: together with both h & w makes the image fit in the box.
<li>no-ratio: do not keep aspect ratio.
</ul>

<table>
<caption>Test cases</caption>
<thead><tr><th>Testcase:</th><th>Result:</th></tr></thead>
<tbody>
  <tr><td>Original image of wider.jpg.</td><td><img src='img.php?src=wider.jpg' /></td></tr>
  <tr><td>wider.jpg max width 200.</td><td><img src='img.php?src=wider.jpg&amp;w=200' /></td></tr>
  <tr><td>wider.jpg max height 200.</td><td><img src='img.php?src=wider.jpg&amp;h=200' /></td></tr>
  <tr><td>wider.jpg max width 200 and max height 200.</td><td><img src='img.php?src=wider.jpg&amp;w=200&amp;h=200' /></td></tr>
  <tr><td>wider.jpg max width 200 and max height 200 and no-ratio.</td><td><img src='img.php?src=wider.jpg&amp;w=200&amp;h=200&amp;no-ratio' /></td></tr>
  <tr><td>wider.jpg max width 200 and max height 200 and cropped.</td><td><img src='img.php?src=wider.jpg&amp;w=200&amp;h=200&amp;crop' /></td></tr>
  <tr><td>wider.jpg max width 200 and max height 100 and cropped.</td><td><img src='img.php?src=wider.jpg&amp;w=200&amp;h=100&amp;crop' /></td></tr>
  <tr><td>Original image of higher.jpg.</td><td><img src='img.php?src=higher.jpg' /></td></tr>
  <tr><td>higher.jpg max width 200.</td><td><img src='img.php?src=higher.jpg&amp;w=200' /></td></tr>
  <tr><td>higher.jpg max height 200.</td><td><img src='img.php?src=higher.jpg&amp;h=200' /></td></tr>
  <tr><td>higher.jpg max width 200 and max height 200.</td><td><img src='img.php?src=higher.jpg&amp;w=200&amp;h=200' /></td></tr>
  <tr><td>higher.jpg max width 200 and max height 200 and no-ratio.</td><td><img src='img.php?src=higher.jpg&amp;w=200&amp;h=200&amp;no-ratio' /></td></tr>
  <tr><td>higher.jpg max width 200 and max height 200 and cropped.</td><td><img src='img.php?src=higher.jpg&amp;w=200&amp;h=200&amp;crop' /></td></tr>
  <tr><td>higher.jpg max width 200 and max height 100 and cropped.</td><td><img src='img.php?src=higher.jpg&amp;w=200&amp;h=100&amp;crop' /></td></tr>
</tbody>
</table>

</body>
</html>