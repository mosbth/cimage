<?php
/**
 * A testclass
 *
 */
class CImageSRGBTest extends \PHPUnit_Framework_TestCase
{
    private $srgbDir = "srgb";
    private $cache;
    private $srgbColorProfile;

    
    
    /**
     * Setup before test
     *
     * @return void
     */
    protected function setUp()
    {
        $this->srgbColorProfile = __DIR__ . '/../icc/sRGB_IEC61966-2-1_black_scaled.icc';
        $this->cache = CACHE_PATH . "/" . $this->srgbDir;
        
        if (!is_writable($this->cache)) {
            mkdir($this->cache);
        }
    }



    /**
     * Test
     *
     * @return void
     */
    public function testCreate1()
    {
        $img = new CImage();

        $filename = $img->convert2sRGBColorSpace(
            'car.png',
            IMAGE_PATH,
            $this->cache,
            $this->srgbColorProfile
        );

        if (class_exists("Imagick")) {
            $this->assertEquals("srgb_car.png", basename($filename), "Filename not as expected on image.");
        } else {
            $this->assertFalse($filename, "ImageMagick not installed, silent fail");
        }
    }



    /**
     * Test
     *
     * @return void
     */
    public function testCreate2()
    {
        $img = new CImage();

        $filename = $img->convert2sRGBColorSpace(
            'car.jpg',
            IMAGE_PATH,
            $this->cache,
            $this->srgbColorProfile
        );

        $this->assertFalse($filename);
    }
}
