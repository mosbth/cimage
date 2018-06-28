<?php
/**
 * Enable a fast track cache with a json representation of the image delivery.
 *
 */
class CFastTrackCache
{
    /**
     * Cache is disabled to start with.
     */
    private $enabled = false;



    /**
     * Path to the cache directory.
     */
    private $path;



    /**
     * Filename of current cache item.
     */
    private $filename;



    /**
     * Container with items to store as cached item.
     */
    private $container;



    /**
     * Enable or disable cache.
     *
     * @param boolean $enable set to true to enable, false to disable
     *
     * @return $this
     */
    public function enable($enabled)
    {
        $this->enabled = $enabled;
        return $this;
    }



    /**
     * Set the path to the cache dir which must exist.
     *
     * @param string $path to the cache dir.
     *
     * @throws Exception when $path is not a directory.
     *
     * @return $this
     */
    public function setCacheDir($path)
    {
        if (!is_dir($path)) {
            throw new Exception("Cachedir is not a directory.");
        }

        $this->path = rtrim($path, "/");

        return $this;
    }



    /**
     * Set the filename to store in cache, use the querystring to create that
     * filename.
     *
     * @param array $clear items to clear in $_GET when creating the filename.
     *
     * @return string as filename created.
     */
    public function setFilename($clear)
    {
        $query = $_GET;

        // Remove parts from querystring that should not be part of filename
        foreach ($clear as $value) {
            unset($query[$value]);
        }

        arsort($query);
        $queryAsString = http_build_query($query);

        $this->filename = md5($queryAsString);

        if (CIMAGE_DEBUG) {
            $this->container["query-string"] = $queryAsString;
        }

        return $this->filename;
    }



    /**
     * Add header items.
     *
     * @param string $header add this as header.
     *
     * @return $this
     */
    public function addHeader($header)
    {
        $this->container["header"][] = $header;
        return $this;
    }



    /**
     * Add header items on output, these are not output when 304.
     *
     * @param string $header add this as header.
     *
     * @return $this
     */
    public function addHeaderOnOutput($header)
    {
        $this->container["header-output"][] = $header;
        return $this;
    }



    /**
     * Set path to source image to.
     *
     * @param string $source path to source image file.
     *
     * @return $this
     */
    public function setSource($source)
    {
        $this->container["source"] = $source;
        return $this;
    }



    /**
     * Set last modified of source image, use to check for 304.
     *
     * @param string $lastModified
     *
     * @return $this
     */
    public function setLastModified($lastModified)
    {
        $this->container["last-modified"] = $lastModified;
        return $this;
    }



    /**
     * Get filename of cached item.
     *
     * @return string as filename.
     */
    public function getFilename()
    {
        return $this->path . "/" . $this->filename;
    }



    /**
     * Write current item to cache.
     *
     * @return boolean if cache file was written.
     */
    public function writeToCache()
    {
        if (!$this->enabled) {
            return false;
        }

        if (is_dir($this->path) && is_writable($this->path)) {
            $filename = $this->getFilename();
            return file_put_contents($filename, json_encode($this->container)) !== false;
        }

        return false;
    }



    /**
     * Output current item from cache, if available.
     *
     * @return void
     */
    public function output()
    {
        $filename = $this->getFilename();
        if (!is_readable($filename)) {
            return;
        }

        $item = json_decode(file_get_contents($filename), true);

        if (!is_readable($item["source"])) {
            return;
        }

        foreach ($item["header"] as $value) {
            header($value);
        }

        if (isset($_SERVER["HTTP_IF_MODIFIED_SINCE"])
            && strtotime($_SERVER["HTTP_IF_MODIFIED_SINCE"]) == $item["last-modified"]) {
            header("HTTP/1.0 304 Not Modified");
            if (CIMAGE_DEBUG) {
                trace(__CLASS__ . " 304");
            }
            exit;
        }

        foreach ($item["header-output"] as $value) {
            header($value);
        }

        if (CIMAGE_DEBUG) {
            trace(__CLASS__ . " 200");
        }
        readfile($item["source"]);
        exit;
    }
}
