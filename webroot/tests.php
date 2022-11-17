<?php

$links = [
    "img.php?src=car.png&v",
    "img.php?src=car.png&w=700&v",
];

?><!doctype html>
<html>
    <head>
        <title>Links to use for testing</title>
    </head>
    <body>
        <h1>Links useful for testing</h1>
        <p>A collection of linkt to use to test various aspects of the cimage process.</p>
        <ul>
        <?php foreach ($links as $link) : ?>
            <li><a href="<?= $link ?>"><?= $link ?></a></li>
        <?php endforeach; ?>
        </ul>
    </body>
</html>