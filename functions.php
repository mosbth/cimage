<?php
/**
 * General functions to use in img.php.
 */



/**
 * Trace and log execution to logfile, useful for debugging and development.
 *
 * @param string $msg message to log to file.
 *
 * @return void
 */
function trace($msg)
{
    $file = CIMAGE_DEBUG_FILE;
    if (!is_writable($file)) {
        return;
    }

    $timer = number_format((microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"]), 6);
    $details  = "{$timer}ms";
    $details .= ":" . round(memory_get_peak_usage()/1024/1024, 3) . "MB";
    $details .= ":" . count(get_included_files());
    file_put_contents($file, "$details:$msg\n", FILE_APPEND);
}



/**
 * Display error message.
 *
 * @param string $msg to display.
 * @param int $type of HTTP error to display.
 *
 * @return void
 */
function errorPage($msg, $type = 500)
{
    global $mode;

    switch ($type) {
        case 403:
            $header = "403 Forbidden";
            break;
        case 404:
            $header = "404 Not Found";
            break;
        default:
            $header = "500 Internal Server Error";
    }

    if ($mode == "strict") {
        $header = "404 Not Found";
    }

    header("HTTP/1.0 $header");

    if ($mode == "development") {
        die("[img.php] $msg");
    }

    error_log("[img.php] $msg");
    die("HTTP/1.0 $header");
}



/**
 * Get input from query string or return default value if not set.
 *
 * @param mixed $key     as string or array of string values to look for in $_GET.
 * @param mixed $default value to return when $key is not set in $_GET.
 *
 * @return mixed value from $_GET or default value.
 */
function get($key, $default = null)
{
    if (is_array($key)) {
        foreach ($key as $val) {
            if (isset($_GET[$val])) {
                return $_GET[$val];
            }
        }
    } elseif (isset($_GET[$key])) {
        return $_GET[$key];
    }
    return $default;
}



/**
 * Get input from query string and set to $defined if defined or else $undefined.
 *
 * @param mixed $key       as string or array of string values to look for in $_GET.
 * @param mixed $defined   value to return when $key is set in $_GET.
 * @param mixed $undefined value to return when $key is not set in $_GET.
 *
 * @return mixed value as $defined or $undefined.
 */
function getDefined($key, $defined, $undefined)
{
    return get($key) === null ? $undefined : $defined;
}



/**
 * Get value of input from query string or else $undefined.
 *
 * @param mixed $key       as string or array of string values to look for in $_GET.
 * @param mixed $undefined value to return when $key has no, or empty value in $_GET.
 *
 * @return mixed value as or $undefined.
 */
function getValue($key, $undefined)
{
    $val = get($key);
    if (is_null($val) || $val === "") {
        return $undefined;
    }
    return $val;
}



/**
 * Get value from config array or default if key is not set in config array.
 *
 * @param string $key    the key in the config array.
 * @param mixed $default value to be default if $key is not set in config.
 *
 * @return mixed value as $config[$key] or $default.
 */
function getConfig($key, $default)
{
    global $config;
    return isset($config[$key])
        ? $config[$key]
        : $default;
}



/**
 * Log when verbose mode, when used without argument it returns the result.
 *
 * @param string $msg to log.
 *
 * @return void or array.
 */
function verbose($msg = null, $arg = "")
{
    global $verbose, $verboseFile;
    static $log = array();

    if (!($verbose || $verboseFile)) {
        return;
    }

    if (is_null($msg)) {
        return $log;
    }

    if (is_null($arg)) {
        $arg = "null";
    } elseif ($arg === false) {
        $arg = "false";
    } elseif ($arg === true) {
        $arg = "true";
    }

    $log[] = $msg . $arg;
}



/**
 * Log when verbose mode, when used without argument it returns the result.
 *
 * @param string $msg to log.
 *
 * @return void or array.
 */
function checkExternalCommand($what, $enabled, $commandString)
{
    $no = $enabled ? null : 'NOT';
    $text = "Post processing $what is $no enabled.<br>";

    list($command) = explode(" ", $commandString);
    $no = is_executable($command) ? null : 'NOT';
    $text .= "The command for $what is $no an executable.<br>";

    return $text;
}
