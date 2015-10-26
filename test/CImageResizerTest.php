<?php
/**
 * A testclass
 *
 */
class CImageResizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Provider
     *
     * @return array
     */
    public function providerImages()
    {
        return array(
            // Same as source, does not set target
            array(100, 100, null, null, null, 1, null, null),
            array(100, 150, null, null, null, 1, null, null),
            array(150, 100, null, null, null, 1, null, null),
        );
    }



    /**
     * Test
     *
     * @dataProvider providerImages
     *
     * @return void
     */
    public function testResize1($srcWidth, $srcHeight, $targetWidth, $targetHeigth, $aspectRatio, $dpr, $expectedWidth, $expectedHeight)
    {
        $img = new CImageResizer();

        $img->setSource($srcWidth, $srcHeight)
            ->setBaseForCalculateTarget($targetWidth, $targetHeigth, $aspectRatio, $dpr)
            ->prepareTargetDimensions();

        $this->assertEquals($expectedWidth, $img->width(), "Width not correct.");
        $this->assertEquals($expectedHeight, $img->height(), "Heigth not correct.");
    }
}
