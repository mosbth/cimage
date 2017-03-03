<?php

namespace Mos\CImage;

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
            array(100, 100,   200, 200,   0, 0, 200, 200),
            array(100, 100,   100,  50,   25,  0, 50,  50),
            array(100, 100,    50, 100,    0, 25, 50,  50),

            // Landscape
            array(200, 100,   400, 200,   0,  0, 400, 200),
            array(200, 100,   100, 100,   0, 25, 100,  50),
            array(200, 100,   400, 100,   100,  0, 200, 100),
            array(200, 100,    100, 400,   0, 175, 100,  50),

            // Portrait
            array(100, 200,   200, 400,   0, 0, 200, 400),
            array(100, 200,   100, 100,   25,  0,  50, 100),
            array(100, 200,   400, 100,     175, 0,  50, 100),
            array(100, 200,    100, 400,   0, 100, 100, 200),

        );
    }



    /**
     * Test
     *
     * @dataProvider providerImages
     *
     * @return void
     */
    public function testResize1($sw, $sh, $tw, $th, $dx, $dy, $dw, $dh)
    {
        $img = new CImageResizer(/*'logger'/**/);

        $img->setSource($sw, $sh)
            ->setBaseWidthHeight($tw, $th)
            ->setResizeStrategy(CImageResizer::FILL_TO_FIT)
            ->calculateTargetWidthAndHeight();

        $this->assertEquals($tw, $img->getTargetWidth(), "Target width not correct.");
        $this->assertEquals($th, $img->getTargetHeight(), "Target height not correct.");

        $this->assertEquals($dx, $img->getDestinationX(), "DestinationX not correct.");
        $this->assertEquals($dy, $img->getDestinationY(), "DestinationY not correct.");
        $this->assertEquals($dw, $img->getDestinationWidth(), "DestinationWidth not correct.");
        $this->assertEquals($dh, $img->getDestinationHeight(), "DestinationHeight not correct.");
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
     * @dataProvider providerImages2
     *
     * @return void
     */
    public function testResize2($sw, $sh, $tw, $th, $dx, $dy, $dw, $dh)
    {
        $img = new CImageResizer(/*'logger'/**/);

        $img->setSource($sw, $sh)
            ->setBaseWidthHeight($tw, $th)
            ->setResizeStrategy(CImageResizer::FILL_TO_FIT)
            ->allowUpscale(false)
            ->calculateTargetWidthAndHeight();

        $this->assertEquals($tw, $img->getTargetWidth(), "Target width not correct.");
        $this->assertEquals($th, $img->getTargetHeight(), "Target height not correct.");

        $this->assertEquals($dx, $img->getDestinationX(), "DestinationX not correct.");
        $this->assertEquals($dy, $img->getDestinationY(), "DestinationY not correct.");
        $this->assertEquals($dw, $img->getDestinationWidth(), "DestinationWidth not correct.");
        $this->assertEquals($dh, $img->getDestinationHeight(), "DestinationHeight not correct.");
    }
}
