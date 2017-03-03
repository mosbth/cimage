<?php

namespace Mos\CImage;

/**
 * A testclass
 *
 */
class CImageResizerByAspectRatioTest extends \PHPUnit_Framework_TestCase
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

            // Aspect ratio 1
            array(1,  100, 100,  null, null,   100, 100),
            array(1,  100, 100,  null,  100,   100, 100),
            array(1,  100, 100,   100, null,   100, 100),
            array(1,  100, 100,   100,  100,   100, 100),

            // Aspect ratio 2
            array(2,  100, 100,  null, null,   100,  50),
            array(2,  100, 100,  null,  100,   200, 100),
            array(2,  100, 100,   100, null,   100,  50),
            array(2,  100, 100,   100,  100,   100,  50),

            // Aspect ratio 0.5
            array(1/2,  100, 100,  null, null,    50, 100),
            array(1/2,  100, 100,  null,  100,    50, 100),
            array(1/2,  100, 100,   100, null,   100, 200),
            array(1/2,  100, 100,   100,  100,    50, 100),

        );
    }



    /**
     * Test
     *
     * @dataProvider providerImages
     *
     * @return void
     */
    public function testResize1($aspectRatio, $srcWidth, $srcHeight, $targetWidth, $targetHeight, $expectedWidth, $expectedHeight)
    {
        $img = new CImageResizer(/*'logger'*/);
        //$img = new CImageResizer('logger');

        $img->setSource($srcWidth, $srcHeight)
            ->setBaseWidthHeight($targetWidth, $targetHeight)
            ->setBaseAspecRatio($aspectRatio)
            ->prepareTargetDimensions();
//            ->calculateTargetWidthAndHeight();

        $this->assertEquals($expectedWidth, $img->getTargetWidth(), "Width not correct.");
        $this->assertEquals($expectedHeight, $img->getTargetHeight(), "Height not correct.");
    }
}
