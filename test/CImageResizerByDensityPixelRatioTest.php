<?php
/**
 * A testclass
 *
 */
class CImageResizerByDevicePixelRatioTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Provider
     *
     * @return array
     */
    public function providerImages()
    {
        return array(

            // No value set
            array(null,  100, 100,  null, null,   null, null),

            // dpr 1
            array(1,  100, 100,  null, null,    100,  100),
            array(1,  100, 100,  null,  100,   null,  100),
            array(1,  100, 100,   100, null,    100, null),
            array(1,  100, 100,   100,  100,    100,  100),

            // dpr 2
            array(2,  100, 100,  null, null,    200,  200),
            array(2,  100, 100,  null,  200,   null,  400),
            array(2,  100, 100,   200, null,    400, null),
            array(2,  100, 100,   200,  200,    400,  400),

            // dpr 1/2
            array(1/2,  100, 100,  null, null,     50,   50),
            array(1/2,  100, 100,  null,  200,   null,  100),
            array(1/2,  100, 100,   200, null,    100, null),
            array(1/2,  100, 100,   200,  200,    100,  100),

        );
    }



    /**
     * Test
     *
     * @dataProvider providerImages
     *
     * @return void
     */
    public function testResize1($dpr, $srcWidth, $srcHeight, $targetWidth, $targetHeight, $expectedWidth, $expectedHeight)
    {
        $img = new CImageResizer(/*'logger'*/);
        //$img = new CImageResizer('logger');

        $img->setSource($srcWidth, $srcHeight)
            //->setResizeStrategy($img::KEEP_RATIO)
            ->setBaseWidthHeight($targetWidth, $targetHeight)
            ->setBaseDevicePixelRate($dpr)
            ->prepareTargetDimensions();
            //->calculateTargetWidthAndHeight();

        $this->assertEquals($expectedWidth, $img->getTargetWidth(), "Width not correct.");
        $this->assertEquals($expectedHeight, $img->getTargetHeight(), "Height not correct.");
    }
}
