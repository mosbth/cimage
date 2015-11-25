<?php
/**
 * A testclass
 *
 */
class CImageResizerStrategyFillToFitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Provider
     *
     * @return array
     */
    public function providerImages()
    {
        return array(

            // Square
            array(100, 100,  null, null,   100, 100,  0, 0, 100, 100),
            array(100, 100,  null,  200,   200, 200,  0, 0, 100, 100),
            array(100, 100,   200, null,   200, 200,  0, 0, 100, 100),
            array(100, 100,   200,  200,   200, 200,  0, 0, 100, 100),

            // Landscape
            array(100, 200,  null, null,   100, 200,  0, 0, 100, 200),
            array(100, 200,  null,  100,    50, 100,  0, 0, 100, 200),
            array(100, 200,    50, null,    50, 100,  0, 0, 100, 200),
            array(100, 200,    50,  100,    50, 100,  0, 0, 100, 200),

            // Portrait
            array(200, 100,  null, null,   200, 100,  0, 0, 200, 100),
            array(200, 100,  null,  200,   400, 200,  0, 0, 200, 100),
            array(200, 100,   400, null,   400, 200,  0, 0, 200, 100),
            array(200, 100,   400,  200,   400, 200,  0, 0, 200, 100),

        );
    }



    /**
     * Test
     *
     * @dataProvider providerImages
     *
     * @return void
     */
    public function testResize1($srcWidth, $srcHeight, $targetWidth, $targetHeight, $targetWidthAfter, $targetHeightAfter, $cropX, $cropY, $cropWidth, $cropHeight)
    {
        $img = new CImageResizer(/*'logger'/**/);

        $img->setSource($srcWidth, $srcHeight)
            ->setBaseWidthHeight($targetWidth, $targetHeight)
            ->setResizeStrategy(CImageResizer::FILL_TO_FIT)
            ->calculateTargetWidthAndHeight();

        $this->assertEquals($targetWidthAfter, $img->getTargetWidth(), "Target width not correct.");
        $this->assertEquals($targetHeightAfter, $img->getTargetHeight(), "Target height not correct.");

        $this->assertEquals($cropX, $img->getCropX(), "CropX not correct.");
        $this->assertEquals($cropY, $img->getCropY(), "CropY not correct.");
        $this->assertEquals($cropWidth, $img->getCropWidth(), "CropWidth not correct.");
        $this->assertEquals($cropHeight, $img->getCropHeight(), "CropHeight not correct.");
    }
}
