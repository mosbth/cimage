<?php

namespace Mos\CImage;

/**
 * A testclass
 *
 */
function logger($str)
{
    echo "$str\n";
}

function loggerDummy($str)
{
    ;
}

class CImageResizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Provider
     *
     * @return array
     */
    public function providerFaultImages()
    {
        return array(
            array('xx',  100, null, 1),
            array( 100, 'yy', null, 1),
            array( 100,  100, 'zz', 1),
            array( 100,  100, null, -1),
        );
    }



    /**
     * Test
     *
     * @dataProvider providerFaultImages
     *
     * @expectedException Exception
     *
     * @return void
     */
    public function testResizeFaults($targetWidth, $targetHeight, $aspectRatio, $dpr)
    {
        $img = new CImageResizer(/*'logger'*/);

        $img->setBaseWidthHeight($targetWidth, $targetHeight)
            ->setBaseAspecRatio($aspectRatio)
            ->setBaseDevicePixelRate($dpr);
    }



    /**
     * Test
     *
     * @return void
     */
    public function testLogger()
    {
        $img = new CImageResizer('Mos\CImage\loggerDummy');

        $img->setBaseWidthHeight(100, 100);
    }




    /**
     * Provider
     *
     * @return array
     */
    public function providerResizeStrategy()
    {
        return array(
            array(CImageResizer::KEEP_RATIO,  "KEEP_RATIO"),
            array(CImageResizer::CROP_TO_FIT, "CROP_TO_FIT"),
            array(CImageResizer::FILL_TO_FIT, "FILL_TO_FIT"),
            array(CImageResizer::STRETCH,     "STRETCH"),
            array(-1, "UNKNOWN"),
        );
    }



    /**
     * Test
     *
     * @dataProvider providerResizeStrategy
     *
     * @return void
     */
    public function testResizeStrategy($strategy, $str)
    {
        $img = new CImageResizer(/*'logger'*/);

        $img->setResizeStrategy($strategy);
        $res = $img->getResizeStrategyAsString();
        
        $this->assertEquals($str, $res, "Strategy not matching.");
    }



    /**
     * Provider
     *
     * @return array
     */
    public function providerPercent()
    {
        return array(
            array(100, 100, "100%", "100%", 100, 100),
            array(100, 100, "50%", "50%", 50, 50),
        );
    }



    /**
     * Test
     *
     * @dataProvider providerPercent
     *
     * @return void
     */
    public function testPercent($sw, $sh, $tw, $th, $w, $h)
    {
        $img = new CImageResizer(/*'logger'*/);

        $img->setSource($sw, $sh)
            ->setBaseWidthHeight($tw, $th);

        $this->assertEquals($w, $img->getTargetWidth(), "Target width not correct.");
        $this->assertEquals($h, $img->getTargetHeight(), "Target height not correct.");
    }



    /**
     * Test
     *
     * @return void
     */
    public function testGetSource()
    {
        $img = new CImageResizer(/*'logger'*/);

        $w = 100;
        $h = 100;

        $img->setSource($w, $h);

        $this->assertEquals($w, $img->getSourceWidth(), "Source width not correct.");
        $this->assertEquals($h, $img->getSourceHeight(), "Source height not correct.");
    }



    /**
     * Provider
     *
     * @return array
     */
    public function providerImages()
    {
        return [

            // car.png
            [CImageResizer::KEEP_RATIO, 491, 324,   500, 200,   303, 200,   0, 0, 491, 324],
            [CImageResizer::KEEP_RATIO, 491, 324,   500, 500,   500, 330,   0, 0, 491, 324],

        ];
    }



    /**
     * Test
     *
     * @dataProvider providerImages
     *
     * @return void
     */
    public function testResize($strat, $sw, $sh, $tw, $th, $twa, $tha, $cx, $cy, $cw, $ch)
    {
        $img = new CImageResizer(/*'logger'/**/);

        $img->setSource($sw, $sh)
            ->setBaseWidthHeight($tw, $th)
            ->setResizeStrategy($strat)
            ->calculateTargetWidthAndHeight();

        $this->assertEquals($twa, $img->getTargetWidth(), "Target width not correct.");
        $this->assertEquals($tha, $img->getTargetHeight(), "Target height not correct.");

        $this->assertEquals($cx, $img->getCropX(), "CropX not correct.");
        $this->assertEquals($cy, $img->getCropY(), "CropY not correct.");
        $this->assertEquals($cw, $img->getCropWidth(), "CropWidth not correct.");
        $this->assertEquals($ch, $img->getCropHeight(), "CropHeight not correct.");
    }
}
