<?php

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

require __DIR__ . "/assert.php";

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context, SnippetAcceptingContext
{
    private $url = null;

    private $headers = [];
    private $imageString = null;
    private $image = null;
    private $imageJSON = null;


    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
    }



    /**
     * @Given Set mode :arg1
     */
    public function setMode($arg1 = null)
    {
        $this->url = "http://localhost/git/cimage/webroot/";
        switch ($arg1) {
            case "development":
                $this->url .= "imgd.php";
                break;
            case "production":
                $this->url .= "imgp.php";
                break;
            case "strict":
                $this->url .= "imgs.php";
                break;
            default:
                $this->url .= "img.php";
        }
    }



    /**
     * @Given Set src :arg1
     */
    public function setSrc($arg1)
    {
        if (is_null($this->url)) {
            $this->setMode();
        }

        $this->url .= "?src=$arg1";
    }



    /**
     * @When Get image
     */
    public function getImage()
    {
        //echo $this->url;
        $res = file_get_contents($this->url);
        assertNotEquals(false, $res);

        $this->imageString = $res;
        $this->headers = $http_response_header;
        
        if (is_null($this->imageJSON)) {
            $this->getImageAsJson();
        }
    }



    /**
     * @When Get image as JSON
     */
    public function getImageAsJson()
    {
        $res = file_get_contents($this->url . "&json");
        assertNotEquals(false, $res);

        $res = json_decode($res, true);
        assertNotEquals(null, $res);

        $this->imageJSON = $res;
    }



    /**
     * @When Get headers for image
     */
    public function getHeadersForImage()
    {
        //echo $this->url;
        $res = get_headers($this->url);
        assertNotEquals(false, $res);

        $this->headers = $http_response_header;
    }



    /**
     * @Then Returns status code :arg1
     */
    public function returnsStatusCode($arg1)
    {
        assertNotEquals(
            false,
            strpos($this->headers[0], $arg1)
        );
    }


    /**
     *
     */
    private function compareImageJsonToHeaders()
    {
        $contentLength = "Content-Length: " . $this->imageJSON["size"];
        assertContains(
            $contentLength,
            $this->headers
        );

        $contentType = "Content-Type: " . $this->imageJSON["mimeType"];
        assertContains(
            $contentType,
            $this->headers
        );

        $lastModified = "Last-Modified: " . $this->imageJSON["cacheGmdate"] . " GMT";
        assertContains(
            $lastModified,
            $this->headers
        );
    }



    /**
     *
     */
    private function compareImageJsonToSavedJson($file)
    {
        $res = file_get_contents("$file.json");
        assertNotEquals(false, $res);

        $res = json_decode($res, true);
        assertNotEquals(null, $res);

        $keys = [
            "mimeType",
            "width",
            "height",
            "size",
            "colors",
            "pngType",
        ];
        foreach ($keys as $key) {
            if (array_key_exists($key, $res)
                && array_key_exists($key, $this->imageJSON)
            ) {
                assertEquals(
                    $res[$key],
                    $this->imageJSON[$key]
                );
            }
        }
    }



    /**
     * @Then Compares to image :arg1
     */
    public function comparesToImage($arg1)
    {
        $base = __DIR__ . "/../img";
        $res = file_get_contents("$base/$arg1");
        assertNotEquals(false, $res);

        assertEquals($this->imageString, $res);

        $this->compareImageJsonToHeaders();
        $this->compareImageJsonToSavedJson("$base/$arg1");
    }
}
