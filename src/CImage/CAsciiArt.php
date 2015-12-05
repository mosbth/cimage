<?php
/**
 * Create an ASCII version of an image.
 *
 */
class CAsciiArt
{
    /**
     * Character set to use.
     */
    private $characterSet = array(
        'one' => "#0XT|:,.' ",
        'two' => "@%#*+=-:. ",
        'three' => "$@B%8&WM#*oahkbdpqwmZO0QLCJUYXzcvunxrjft/\|()1{}[]?-_+~<>i!lI;:,\"^`'. "
    );



    /**
     * Current character set.
     */
    private $characters = null;



    /**
     * Length of current character set.
     */
    private $charCount = null;



    /**
     * Scale of the area to swap to a character.
     */
    private $scale = null;



    /**
     * Strategy to calculate luminance.
     */
    private $luminanceStrategy = null;



    /**
     * Constructor which sets default options.
     */
    public function __construct()
    {
        $this->setOptions();
    }



    /**
     * Add a custom character set.
     *
     * @param string $key   for the character set.
     * @param string $value for the character set.
     *
     * @return $this
     */
    public function addCharacterSet($key, $value)
    {
        $this->characterSet[$key] = $value;
        return $this;
    }



    /**
     * Set options for processing, defaults are available.
     *
     * @param array $options to use as default settings.
     *
     * @return $this
     */
    public function setOptions($options = array())
    {
        $default = array(
            "characterSet" => 'two',
            "scale" => 14,
            "luminanceStrategy" => 3,
            "customCharacterSet" => null,
        );
        $default = array_merge($default, $options);
        
        if (!is_null($default['customCharacterSet'])) {
            $this->addCharacterSet('custom', $default['customCharacterSet']);
            $default['characterSet'] = 'custom';
        }
        
        $this->scale = $default['scale'];
        $this->characters = $this->characterSet[$default['characterSet']];
        $this->charCount = strlen($this->characters);
        $this->luminanceStrategy = $default['luminanceStrategy'];
        
        return $this;
    }



    /**
     * Create an Ascii image from an image file.
     *
     * @param string $filename of the image to use.
     *
     * @return string $ascii with the ASCII image.
     */
    public function createFromFile($filename)
    {
        $img = imagecreatefromstring(file_get_contents($filename));
        list($width, $height) = getimagesize($filename);

        $ascii = null;
        $incY = $this->scale;
        $incX = $this->scale / 2;
        
        for ($y = 0; $y < $height - 1; $y += $incY) {
            for ($x = 0; $x < $width - 1; $x += $incX) {
                $toX = min($x + $this->scale / 2, $width - 1);
                $toY = min($y + $this->scale, $height - 1);
                $luminance = $this->luminanceAreaAverage($img, $x, $y, $toX, $toY);
                $ascii .= $this->luminance2character($luminance);
            }
            $ascii .= PHP_EOL;
        }

        return $ascii;
    }



    /**
     * Get the luminance from a region of an image using average color value.
     *
     * @param string  $img the image.
     * @param integer $x1  the area to get pixels from.
     * @param integer $y1  the area to get pixels from.
     * @param integer $x2  the area to get pixels from.
     * @param integer $y2  the area to get pixels from.
     *
     * @return integer $luminance with a value between 0 and 100.
     */
    public function luminanceAreaAverage($img, $x1, $y1, $x2, $y2)
    {
        $numPixels = ($x2 - $x1 + 1) * ($y2 - $y1 + 1);
        $luminance = 0;
        
        for ($x = $x1; $x <= $x2; $x++) {
            for ($y = $y1; $y <= $y2; $y++) {
                $rgb   = imagecolorat($img, $x, $y);
                $red   = (($rgb >> 16) & 0xFF);
                $green = (($rgb >> 8) & 0xFF);
                $blue  = ($rgb & 0xFF);
                $luminance += $this->getLuminance($red, $green, $blue);
            }
        }
        
        return $luminance / $numPixels;
    }



    /**
     * Calculate luminance value with different strategies.
     *
     * @param integer $red   The color red.
     * @param integer $green The color green.
     * @param integer $blue  The color blue.
     *
     * @return float $luminance with a value between 0 and 1.
     */
    public function getLuminance($red, $green, $blue)
    {
        switch ($this->luminanceStrategy) {
            case 1:
                $luminance = ($red * 0.2126 + $green * 0.7152 + $blue * 0.0722) / 255;
                break;
            case 2:
                $luminance = ($red * 0.299 + $green * 0.587 + $blue * 0.114) / 255;
                break;
            case 3:
                $luminance = sqrt(0.299 * pow($red, 2) + 0.587 * pow($green, 2) + 0.114 * pow($blue, 2)) / 255;
                break;
            case 0:
            default:
                $luminance = ($red + $green + $blue) / (255 * 3);
        }

        return $luminance;
    }



    /**
     * Translate the luminance value to a character.
     *
     * @param string $position a value between 0-100 representing the
     *                         luminance.
     *
     * @return string with the ascii character.
     */
    public function luminance2character($luminance)
    {
        $position = (int) round($luminance * ($this->charCount - 1));
        $char = $this->characters[$position];
        return $char;
    }
}
