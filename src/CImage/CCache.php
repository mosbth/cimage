<?php
/**
 * Deal with the cache directory and cached items.
 *
 */
class CCache
{
    /**
     * Path to the cache directory.
     */
    private $path;



    /**
     * Set the path to the cache dir which must exist.
     *
     * @param string path to the cache dir.
     *
     * @throws Exception when $path is not a directory.
     *
     * @return $this
     */
    public function setDir($path)
    {
        if (!is_dir($path)) {
            throw new Exception("Cachedir is not a directory.");
        }

        $this->path = $path;

        return $this;
    }



    /**
     * Get the path to the cache subdir and try to create it if its not there.
     *
     * @param string $subdir name of subdir
     * @param array  $create default is to try to create the subdir
     *
     * @return string | boolean as real path to the subdir or
     *                          false if it does not exists
     */
    public function getPathToSubdir($subdir, $create = true)
    {
        $path = realpath($this->path . "/" . $subdir);

        if (is_dir($path)) {
            return $path;
        }

        if ($create && is_writable($this->path)) {
            $path = $this->path . "/" . $subdir;

            if (mkdir($path)) {
                return realpath($path);
            }
        }

        return false;
    }



    /**
     * Get status of the cache subdir.
     *
     * @param string $subdir name of subdir
     *
     * @return string with status
     */
    public function getStatusOfSubdir($subdir)
    {
        $path = realpath($this->path . "/" . $subdir);

        $exists = is_dir($path);
        $res  = $exists ? "exists" : "does not exist";
        
        if ($exists) {
            $res .= is_writable($path) ? ", writable" : ", not writable";
        }

        return $res;
    }



    /**
     * Remove the cache subdir.
     *
     * @param string $subdir name of subdir
     *
     * @return null | boolean true if success else false, null if no operation
     */
    public function removeSubdir($subdir)
    {
        $path = realpath($this->path . "/" . $subdir);

        if (is_dir($path)) {
            return rmdir($path);
        }

        return null;
    }
}
