<?php

namespace Mos\CImage;

/**
 * A testclass
 *
 */
class CImageResizerStrategyKeepAspectRatioTest extends \PHPUnit_Framework_TestCase
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
            array(200, 100,  null, null,   200, 100,  0, 0, 200, 100),
            array(200, 100,  null,  200,   400, 200,  0, 0, 200, 100),
            array(200, 100,   400, null,   400, 200,  0, 0, 200, 100),
            array(200, 100,   400,  200,   400, 200,  0, 0, 200, 100),

            // Portrait
            array(100, 200,  null, null,   100, 200,  0, 0, 100, 200),
            array(100, 200,  null,  100,    50, 100,  0, 0, 100, 200),
            array(100, 200,    50, null,    50, 100,  0, 0, 100, 200),
            array(100, 200,    50,  100,    50, 100,  0, 0, 100, 200),

        );
    }



    /**
     * Test
     *
     * @dataProvider providerImages
     *
     * @return void
     */
    public function testResize1($sw, $sh, $tw, $th, $twa, $tha, $cx, $cy, $cw, $ch)
    {
        $img = new CImageResizer(/*'logger'/**/);

        $img->setSource($sw, $sh)
            ->setBaseWidthHeight($tw, $th)
            ->setResizeStrategy(CImageResizer::KEEP_RATIO)
            ->calculateTargetWidthAndHeight();

        $this->assertEquals($twa, $img->getTargetWidth(), "Target width not correct.");
        $this->assertEquals($tha, $img->getTargetHeight(), "Target height not correct.");

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
        return array(

            // Square
            array(100, 100,   100, 100,    100, 100,  0, 0, 100, 100),
            array(100, 100,  null, 200,    100, 100,  0, 0, 100, 100),
            array(100, 100,   200, null,   100, 100,  0, 0, 100, 100),
            array(100, 100,   200,  100,   100, 100,  0, 0, 100, 100),
            array(100, 100,   100,  200,   100, 100,  0, 0, 100, 100),
            array(100, 100,   200,  200,   100, 100,  0, 0, 100, 100),

            // Landscape
            //array(200, 100,  null, null,   200, 100,  0, 0, 200, 100),
            //array(200, 100,  null,  200,   400, 200,  0, 0, 200, 100),
            //array(200, 100,   400, null,   400, 200,  0, 0, 200, 100),
            //array(200, 100,   400,  200,   400, 200,  0, 0, 200, 100),

            // Portrait
            //array(100, 200,  null, null,   100, 200,  0, 0, 100, 200),
            //array(100, 200,  null,  100,    50, 100,  0, 0, 100, 200),
            //array(100, 200,    50, null,    50, 100,  0, 0, 100, 200),
            //array(100, 200,    50,  100,    50, 100,  0, 0, 100, 200),

        );
    }



    /**
     * Test
     *
     * @dataProvider providerImages2
     *
     * @return void
     */
    public function testResize2($sw, $sh, $tw, $th, $twa, $tha, $cx, $cy, $cw, $ch)
    {
        $img = new CImageResizer(/*'logger'/**/);

        $img->setSource($sw, $sh)
            ->setBaseWidthHeight($tw, $th)
            ->setResizeStrategy(CImageResizer::KEEP_RATIO)
            ->allowUpscale(false)
            ->calculateTargetWidthAndHeight();

        $this->assertEquals($twa, $img->getTargetWidth(), "Target width not correct.");
        $this->assertEquals($tha, $img->getTargetHeight(), "Target height not correct.");

        $this->assertEquals($cx, $img->getCropX(), "CropX not correct.");
        $this->assertEquals($cy, $img->getCropY(), "CropY not correct.");
        $this->assertEquals($cw, $img->getCropWidth(), "CropWidth not correct.");
        $this->assertEquals($ch, $img->getCropHeight(), "CropHeight not correct.");
    }
}
