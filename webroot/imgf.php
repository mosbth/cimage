<?php
/**
 * Fast track cache, read entries from the cache before processing image
 * the ordinary way.
 */

// Load the config file or use defaults
$configFile = __DIR__
    . "/"
    . basename(__FILE__, ".php")
    . "_config.php";

if (is_file($configFile) && is_readable($configFile)) {
    $config = require $configFile;
} elseif (!isset($config)) {
    $config = array(
        "cache_path" =>  __DIR__ . "/../cache/",
    );
}

// Prepare to check if fast cache should be used
$cachePath = $config["cache_path"] . "/fasttrack";
$query = $_GET;

// Do not use cache when no-cache is active
$useCache = !(array_key_exists("no-cache", $query) || array_key_exists("nc", $query));

// Remove parts from querystring that should not be part of filename
$clear = array("nc", "no-cache");
foreach ($clear as $value) {
    unset($query[$value]);
}

// Create the cache filename
arsort($query);
$queryAsString = http_build_query($query);
$filename = md5($queryAsString);
$filename = "$cachePath/$filename";

// Check cached item, if any
if ($useCache && is_readable($filename)) {
    $item = json_decode(file_get_contents($filename), true);

    if (is_readable($item["source"])) {
        foreach ($item["header"] as $value) {
            header($value);
        }

        if (isset($_SERVER["HTTP_IF_MODIFIED_SINCE"])
            && strtotime($_SERVER["HTTP_IF_MODIFIED_SINCE"]) == $item["last-modified"]) {
            header("HTTP/1.0 304 Not Modified");
            exit;
        }

        foreach ($item["header-output"] as $value) {
            header($value);
        }

        readfile($item["source"]);
        exit;
    }
}

// No fast track cache, proceed as usual
include __DIR__ . "/img.php";
