<?php

namespace Mos\CImage;

/**
 * A testclass
 *
 */
class CImageResizerStrategyStretchTest extends \PHPUnit_Framework_TestCase
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
            array(100, 100,   200,  200),
            array(100, 100,   200,  100),
            array(100, 100,   100,  200),

            // Landscape
            array(200, 100,   400,  200),
            array(200, 100,   100,  200),

            // Portrait
            array(100, 200,    50,  100),
            array(100, 200,   100,  100),

        );
    }



    /**
     * Test
     *
     * @dataProvider providerImages
     *
     * @return void
     */
    public function testResize1($sw, $sh, $tw, $th)
    {
        $img = new CImageResizer(/*'logger'/**/);

        $img->setSource($sw, $sh)
            ->setBaseWidthHeight($tw, $th)
            ->setResizeStrategy(CImageResizer::STRETCH)
            ->calculateTargetWidthAndHeight();

        $this->assertEquals($tw, $img->getTargetWidth(), "Target width not correct.");
        $this->assertEquals($th, $img->getTargetHeight(), "Target height not correct.");

    }
}
