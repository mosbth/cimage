<?php
/**
 * A testclass
 *
 */
class CImageDummyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test
     *
     * @return void
     */
    public function testCreate1()
    {
        $img = new CImage();

        $img->setSaveFolder(CACHE_PATH . "/dummy");
        $img->setSource('dummy', CACHE_PATH . "/dummy");
        $img->createDummyImage();
        $img->generateFilename(null, false);
        $img->save(null, null, false);

        $filename = $img->getTarget();

        $this->assertEquals(basename($filename), "dummy_100_100", "Filename not as expected on dummy image.");
    }



    /**
     * Test
     *
     * @return void
     */
    public function testCreate2()
    {
        $img = new CImage();

        $img->setSaveFolder(CACHE_PATH . "/dummy");
        $img->setSource('dummy', CACHE_PATH . "/dummy");
        $img->createDummyImage(200, 400);
        $img->generateFilename(null, false);
        $img->save(null, null, false);

        $filename = $img->getTarget();

        $this->assertEquals(basename($filename), "dummy_200_400", "Filename not as expected on dummy image.");
    }
}
