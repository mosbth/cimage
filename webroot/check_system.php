<?php

$no = extension_loaded('gd') ? null : 'NOT';
echo "Extension gd is $no loaded.";
