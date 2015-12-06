<?php
/**
 * A testclass
 *
 */
class CImageResizerStrategyCropToFitTest extends \PHPUnit_Framework_TestCase
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
            array(100, 100,   200,  200,    0,  0, 100, 100),
            array(100, 100,   200,  100,    0, 25, 100,  50),
            array(100, 100,   100,  200,   25,  0,  50, 100),

            // Landscape
            array(200, 100,   400,  200,    0,  0, 200, 100),
            array(200, 100,    50,   50,   50,  0, 100, 100),
            array(200, 100,   400,  100,    0, 25, 200,  50),
            array(200, 100,   100,  400,   round(175/2),  0,  25, 100),

            // Portrait
            array(100, 200,    50, 100,    0,  0, 100, 200),
            array(100, 200,    50,  50,    0, 50, 100, 100),
            array(100, 200,   200,  50,    0, round(175/2), 100,  25),
            array(100, 200,   50, 200,    25,  0,  50, 200),

        );
    }



    /**
     * Test
     *
     * @dataProvider providerImages
     *
     * @return void
     */
    public function testResize1($sw, $sh, $tw, $th, $cx, $cy, $cw, $ch)
    {
        $img = new CImageResizer(/*'logger'/**/);

        $img->setSource($sw, $sh)
            ->setBaseWidthHeight($tw, $th)
            ->setResizeStrategy(CImageResizer::CROP_TO_FIT)
            ->calculateTargetWidthAndHeight();

        $this->assertEquals($tw, $img->getTargetWidth(), "Target width not correct.");
        $this->assertEquals($th, $img->getTargetHeight(), "Target height not correct.");

        $this->assertEquals($cx, $img->getCropX(), "CropX not correct.");
        $this->assertEquals($cy, $img->getCropY(), "CropY not correct.");
        $this->assertEquals($cw, $img->getCropWidth(), "CropWidth not correct.");
        $this->assertEquals($ch, $img->getCropHeight(), "CropHeight not correct.");
    }



     /**
      * Provider
      *
      * @return array
      */
    public function providerImages2()
    {
        return [

            // Square
            [100, 100,   200, 200,   50,  50, 100, 100],
            [100, 100,   400, 100,  150,   0, 100, 100],
            [100, 100,   100, 400,    0, 150, 100, 100],
            [100, 100,   400, 400,  150, 150, 100, 100],
            [491, 323,   600, 400,   55,  39, 491, 323],

            // Landscape

            // Portrait

        ];
    }



     /**
      * Test
      *
      * @dataProvider providerImages
      *
      * @return void
      */
    public function testResize2($sw, $sh, $tw, $th, $cx, $cy, $cw, $ch)
    {
        $img = new CImageResizer(/*'logger'/**/);

        $img->setSource($sw, $sh)
            ->setBaseWidthHeight($tw, $th)
            ->setResizeStrategy(CImageResizer::CROP_TO_FIT)
            ->allowUpscale(false)
            ->calculateTargetWidthAndHeight();

        $this->assertEquals($tw, $img->getTargetWidth(), "Target width not correct.");
        $this->assertEquals($th, $img->getTargetHeight(), "Target height not correct.");

        $this->assertEquals($cx, $img->getCropX(), "CropX not correct.");
        $this->assertEquals($cy, $img->getCropY(), "CropY not correct.");
        $this->assertEquals($cw, $img->getCropWidth(), "CropWidth not correct.");
        $this->assertEquals($ch, $img->getCropHeight(), "CropHeight not correct.");
    }
}
