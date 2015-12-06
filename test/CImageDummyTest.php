<?php
/**
 * A testclass
 *
 */
class CImageDummyTest extends \PHPUnit_Framework_TestCase
{
    const DUMMY = "__dummy__";
    private $cachepath;



    /**
     * Setup environment
     *
     * @return void
     */
    protected function setUp()
    {
        $cache = new CCache();
        $cache->setDir(CACHE_PATH);
        $this->cachepath = $cache->getPathToSubdir(self::DUMMY);
    }



    /**
     * Clean up cache dir content.
     *
     * @return void
     */
    protected function removeFilesInCacheDir()
    {
        $files = glob($this->cachepath . "/*");
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }



    /**
     * Teardown environment
     *
     * @return void
     */
    protected function tearDown()
    {
        $cache = new CCache();
        $cache->setDir(CACHE_PATH);
        $this->removeFilesInCacheDir();
        $cache->removeSubdir(self::DUMMY);
    }



    /**
     * Test
     *
     * @return void
     */
    public function testCreate1()
    {
        $img = new CImage();

        $img->setSaveFolder($this->cachepath);
        $img->setSource(self::DUMMY, $this->cachepath);
        $img->createDummyImage();
        $img->generateFilename(null, false);
        $img->save(null, null, false);

        $filename = $img->getTarget();

        $this->assertEquals(basename($filename), self::DUMMY . "_100_100", "Filename not as expected on dummy image.");
    }



    /**
     * Test
     *
     * @return void
     */
    public function testCreate2()
    {
        $img = new CImage();

        $img->setSaveFolder($this->cachepath);
        $img->setSource(self::DUMMY, $this->cachepath);
        $img->createDummyImage(200, 400);
        $img->generateFilename(null, false);
        $img->save(null, null, false);

        $filename = $img->getTarget();

        $this->assertEquals(basename($filename), self::DUMMY . "_200_400", "Filename not as expected on dummy image.");
    }
}
