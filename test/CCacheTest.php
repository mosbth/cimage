<?php
/**
 * A testclass
 *
 */
class CCacheTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test
     *
     * @return void
     */
    public function testSetCacheDir()
    {
        $cache = new CCache();
        $cache->setDir(CACHE_PATH);

        $exp = "exists, writable";
        $res = $cache->getStatusOfSubdir("");
        $this->assertEquals($exp, $res, "Status of cache dir missmatch.");
    }



    /**
     * Test
     *
     * @expectedException Exception
     *
     * @return void
     */
    public function testSetWrongCacheDir()
    {
        $cache = new CCache();
        $cache->setDir(CACHE_PATH . "/NO_EXISTS");
    }



    /**
     * Test
     *
     * @return void
     */
    public function testCreateSubdir()
    {
        $cache = new CCache();
        $cache->setDir(CACHE_PATH);
        
        $subdir = "__test__";
        $cache->removeSubdir($subdir);
        
        $exp = "does not exist";
        $res = $cache->getStatusOfSubdir($subdir, false);
        $this->assertEquals($exp, $res, "Subdir should not be created.");
        
        $res = $cache->getPathToSubdir($subdir);
        $exp = realpath(CACHE_PATH . "/$subdir");
        $this->assertEquals($exp, $res, "Subdir path missmatch.");

        $exp = "exists, writable";
        $res = $cache->getStatusOfSubdir($subdir);
        $this->assertEquals($exp, $res, "Subdir should exist.");

        $res = $cache->removeSubdir($subdir);
        $this->assertTrue($res, "Remove subdir.");
    }
}
