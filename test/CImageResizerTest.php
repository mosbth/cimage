<?php
/**
 * A testclass
 *
 */
function logger($str)
{
    echo "$str\n";
}

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

            // $strategy
            // $srcWidth, $srcHeight, $targetWidth, $targetHeight,
            // $aspectRatio, $dpr, 
            // $expectedWidth, $expectedHeight,
            // $expectedWidth2, $expectedHeight2
            
            // Same as source, does not set target

            // ===== Keep aspect ratio
/*
            array(1, 100, 100, null, null, null, 1, null, null,  100, 100),
            array(1, 100, 150, null, null, null, 1, null, null,  100, 150),
            array(1, 150, 100, null, null, null, 1, null, null,  150, 100),

            // Width & Height as %
            array(1, 100, 100, '200%', null, null, 1,  200, null,  200, 200),
            array(1, 100, 100,  null, '50%', null, 1, null,   50,   50,  50),

            // dpr
            //array(1, 100, 100, null, null, null, 2, null, null,  100, 100), // do dpr?
/*
            array(1, 100, 100,  100, null, null, 2,  200, null,  200, 200),
            array(1, 100, 100, null,  100, null, 2, null,  200,  200, 200),
            array(1, 100, 100,  100,  100, null, 2,  200,  200,  200, 200),
*/

            // ===== Need crop to fit or fill to fit
            // Aspect ratio
/*
            array(2, 100, 100, null, null, 2, 1, 100,  50,  100,  50),
            array(2, 100, 200, null, null, 4, 1, 100,  25,  100,  25),
            array(2, 200, 100, null, null, 4, 1, 200,  50,  200,  50),

            // Aspect ratio inverted
            array(2, 100, 100, null, null, 1/2, 1,  50, 100,  50, 100),
            array(2, 100, 200, null, null, 1/4, 1,  50, 200,  50, 200),
            array(2, 200, 100, null, null, 1/4, 1,  25, 100,  25, 100),

            // Aspect ratio & width
            array(2, 100, 100, 200, null, 2, 1, 200, 100,  200, 100),

            // Aspect ratio & height
            array(2, 100, 100, null, 200, 1/2, 1, 100, 200,  100, 200),
*/
        );
    }



    /**
     * Test
     *
     * @dataProvider providerImages
     *
     * @return void
     */
     /*
    public function testResize1($strategy, $srcWidth, $srcHeight, $targetWidth, $targetHeight, $aspectRatio, $dpr, $expectedWidth, $expectedHeight, $expectedWidth2, $expectedHeight2)
    {
        $img = new CImageResizer(/*'logger'*/ /*);
        //$img = new CImageResizer('logger');

        $img->setSource($srcWidth, $srcHeight)
            ->setResizeStrategy($strategy)
            ->setBaseWidthHeight($targetWidth, $targetHeight)
            ->setBaseAspecRatio($aspectRatio)
            ->setBaseDevicePixelRate($dpr)
            ->prepareTargetDimensions();

        $this->assertEquals($expectedWidth, $img->getTargetWidth(), "Width not correct.");
        $this->assertEquals($expectedHeight, $img->getTargetHeight(), "Height not correct.");
        
        $img->calculateTargetWidthAndHeight();
        $this->assertEquals($expectedWidth2, $img->getTargetWidth(), "Width not correct.");
        $this->assertEquals($expectedHeight2, $img->getTargetHeight(), "Height not correct.");

    }

*/

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
}
