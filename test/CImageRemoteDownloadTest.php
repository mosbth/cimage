<?php
/**
 * A testclass
 *
 */
class CImageRemoteDownloadTest extends \PHPUnit_Framework_TestCase
{
    /*
     * remote_whitelist
     */
    private $remote_whitelist = [
        '\.facebook\.com$',
        '^(?:images|photos-[a-z])\.ak\.instagram\.com$',
        '\.google\.com$',
    ];
    
    
    
    /**
     * Provider for valid remote sources.
     *
     * @return array
     */
    public function providerValidRemoteSource()
    {
        return [
            [
                "http://dbwebb.se/img.jpg",
                "https://dbwebb.se/img.jpg",
            ],
        ];
    }



    /**
     * Provider for invalid remote sources.
     *
     * @return array
     */
    public function providerInvalidRemoteSource()
    {
        return [
            [
                "ftp://dbwebb.se/img.jpg",
                "dbwebb.se/img.jpg",
                "img.jpg",
            ],
        ];
    }



    /**
     * Test
     *
     * @return void
     *
     * @dataProvider providerValidRemoteSource
     */
    public function testAllowRemoteDownloadDefaultPatternValid($source)
    {
        $img = new CImage();
        $img->setRemoteDownload(true, "");
        
        $res = $img->isRemoteSource($source);
        $this->assertTrue($res, "Should be a valid remote source: '$source'.");
    }



    /**
     * Test
     *
     * @return void
     *
     * @dataProvider providerInvalidRemoteSource
     */
    public function testAllowRemoteDownloadDefaultPatternInvalid($source)
    {
        $img = new CImage();
        $img->setRemoteDownload(true, "");
        
        $res = $img->isRemoteSource($source);
        $this->assertFalse($res, "Should not be a valid remote source: '$source'.");
    }



    /**
     * Provider for hostname matching the whitelist.
     *
     * @return array
     */
    public function providerHostnameMatch()
    {
        return [
            [
                "any.facebook.com",
                "images.ak.instagram.com",
                "google.com",
            ],
        ];
    }



    /**
     * Test
     *
     * @param string $hostname matches the whitelist
     *
     * @return void
     *
     * @dataProvider providerHostnameMatch
     *
     */
    public function testRemoteHostWhitelistMatch($hostname)
    {
        $img = new CImage();
        $img->setRemoteHostWhitelist($this->remote_whitelist);
        
        $res = $img->isRemoteSourceOnWhitelist("http://$hostname/img.jpg");
        $this->assertTrue($res, "Should be a valid hostname on the whitelist: '$hostname'.");
    }



    /**
     * Provider for hostname not matching the whitelist.
     *
     * @return array
     */
    public function providerHostnameNoMatch()
    {
        return [
            [
                "example.com",
                ".com",
                "img.jpg",
            ],
        ];
    }



    /**
     * Test
     *
     * @param string $hostname not matching the whitelist
     *
     * @return void
     *
     * @dataProvider providerHostnameNoMatch
     *
     */
    public function testRemoteHostWhitelistNoMatch($hostname)
    {
        $img = new CImage();
        $img->setRemoteHostWhitelist($this->remote_whitelist);
        
        $res = $img->isRemoteSourceOnWhitelist("http://$hostname/img.jpg");
        $this->assertFalse($res, "Should not be a valid hostname on the whitelist: '$hostname'.");
    }



    /**
     * Test
     *
     * @return void
     *
     */
    public function testRemoteHostWhitelistNotConfigured()
    {
        $img = new CImage();
        $res = $img->isRemoteSourceOnWhitelist(null);
        $this->assertTrue($res, "Should allow when whitelist not configured.");
    }
}
