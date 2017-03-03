<?php

namespace Mos\CImage;

/**
 * A testclass for img.php
 *
 */
class CImgTest extends \PHPUnit_Framework_TestCase
{



    /**
     * Provider
     *
     * @return array
     */
    public function providerQueryString()
    {
        return [
        
            //
            [[
                "src" => "car.png",
                "json" => true,
                "rotate" => 90,
            ]],
        ];
    }



    /**
     * Test
     *
     * @-preserveGlobalState disabled
     * @runInSeparateProcess
     *
     * @dataProvider providerQueryString
     *
     * @return void
     */
    public function testResize($query)
    {
        //$_GET = $query;

        #ob_start();
        //$res = require "webroot/img.php";
        #$res = ob_get_clean();
        
        //echo "MOPED $res";
    }
}
