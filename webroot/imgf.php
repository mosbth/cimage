<?php
/**
 * Fast track cache, read entries from the cache before processing image
 * the ordinary way.
 */
 // Include debug functions
function debug1($msg)
{
    $file = "/tmp/cimage";
    if (!is_writable($file)) {
        return;
    }
    $msg .= ":" . count(get_included_files());
    $msg .= ":" . round(memory_get_peak_usage()/1024/1024, 3) . "MB";
    $msg .= ":" . (string) round((microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']), 6) . "ms";
    file_put_contents($file, "$msg\n", FILE_APPEND);
}


$cachePath = __DIR__ . "/../cache/fasttrack";
$query = $_GET;

// Remove parts from querystring that should not be part of filename
$clear = array("nc", "no-cache");
foreach ($clear as $value) {
    unset($query[$value]);
}

arsort($query);
$queryAsString = http_build_query($query);

$filename = md5($queryAsString);
$filename = "$cachePath/$filename";
if (is_readable($filename)) {
    $item = json_decode(file_get_contents($filename), true);

    if (is_readable($item["source"])) {
        foreach ($item["header"] as $value) {
            header($value);
        }

        if (isset($_SERVER["HTTP_IF_MODIFIED_SINCE"])
            && strtotime($_SERVER["HTTP_IF_MODIFIED_SINCE"]) == $item["last-modified"]) {
            header("HTTP/1.0 304 Not Modified");
            debug1("really fast track 304");
            exit;
        }

        foreach ($item["header-output"] as $value) {
            header($value);
        }

        readfile($item["source"]);
        debug1("really fast track 200");
        exit;
    }
}


// No fast track cache, proceed as usual
include __DIR__ . "/img.php";
