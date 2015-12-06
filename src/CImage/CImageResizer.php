<?php
/**
 * Resize and crop images.
 *
 * @author  Mikael Roos mos@dbwebb.se
 * @example http://dbwebb.se/opensource/cimage
 * @link    https://github.com/mosbth/cimage
 */
class CImageResizer
{
    /**
     * Log function.
     */
    private $log;



    /**
     * Source image dimensions, calculated from loaded image.
     */
    private $srcWidth;
    private $srcHeight;



    /**
     * Set as expected target image/canvas dimensions.
     */
    private $targetWidth;
    private $targetHeight;



    /**
     * Where should the image go on the canvas.
     */
    private $destinationX;
    private $destinationY;
    private $destinationWidth;
    private $destinationHeight;



    /**
     * Which parts to crop from the source.
     */
    private $cropX;
    private $cropY;
    private $cropWidth;
    private $cropHeight;



    /**
     * Change target height & width when different dpr, dpr 2 means double
     * image dimensions.
     */
    private $dpr = null;



    /**
     * Set aspect ratio for the target image.
     */
    private $aspectRatio;



    /**
     * Array with details on how to crop.
     * Array contains xxxxxx
     */
    public $crop;
    public $cropOrig; // Save original value?



    /**
     * Area to use for target image, crop out parts not in area.
     * Array with top, right, bottom, left percentage values to crop out.
     */
    private $area;



    /**
     * Pixel offset in source image to decide which part of image is used.
     * Array with top, right, bottom, left percentage values to crop out.
     */
    private $offset;



    /**
     * Resize strategy, image should keep its original ratio.
     */
    const KEEP_RATIO = 1;



    /**
     * Resize strategy, image should crop and fill area.
     */
    const CROP_TO_FIT = 2;



    /**
     * Resize strategy, image should fit in area and fill remains.
     */
    const FILL_TO_FIT = 3;



    /**
     * Resize strategy, image should stretch to fit in area.
     */
    const STRETCH = 4;



    /**
     * The currently selected resize strategy.
     */
    private $resizeStrategy = self::KEEP_RATIO;



    /**
     * Allow upscale of smaller images by default, set to false to disallow.
     */
    private $upscale = true;



    /**
     * Constructor, set log function to use for verbose logging or null
     * to disable logging.
     *
     * @param callable $log function to call for logging.
     */
    public function __construct($log = null)
    {
        $this->log = $log;
    }



    /**
     * Log string using logger.
     *
     * @param string $str to log.
     */
    public function log($str)
    {
        if ($this->log) {
            call_user_func($this->log, $str);
        }
    }



    /**
     * Set source dimensions.
     *
     * @param integer $width  of source image.
     * @param integer $height of source image.
     *
     * @throws Exception
     *
     * @return $this
     */
    public function setSource($width, $height)
    {
        $this->srcWidth  = $width;
        $this->srcHeight = $height;
        $this->log("# Source image dimension: {$this->srcWidth}x{$this->srcHeight}.");

        return $this;
    }



    /**
     * Get resize strategy as string.
     *
     * @return string
     */
    public function getResizeStrategyAsString()
    {
        switch ($this->resizeStrategy) {
            case self::KEEP_RATIO:
                return "KEEP_RATIO";
                break;

            case self::CROP_TO_FIT:
                return "CROP_TO_FIT";
                break;

            case self::FILL_TO_FIT:
                return "FILL_TO_FIT";
                break;

            case self::STRETCH:
                return "STRETCH";
                break;

            default:
                return "UNKNOWN";
        }
    }



     /**
      * Set resize strategy as KEEP_RATIO, CROP_TO_FIT or FILL_TO_FIT.
      *
      * @param integer $strategy
      *
      * @return $this
      */
    public function setResizeStrategy($strategy)
    {
        $this->resizeStrategy = $strategy;
        $this->log("# Resize strategy is " . $this->getResizeStrategyAsString());

        return $this;
    }



    /**
     * Allow or disallow upscale smaller images.
     *
     * @param boolean $upscale
     *
     * @return $this
     */
    public function allowUpscale($upscale)
    {
        $this->upscale = $upscale;
        $this->log("# Allow upscale is $this->upscale.");

        return $this;
    }



    /**
     * Check if a value is upscaled or not by compare $current to $orig,
     * if $current is larger than $orig then check if upscaled is allowed.
     *
     * @param float &$current value to check and change.
     * @param float  $orig    value to check against.
     *
     * @return float as the value respected by the upscale setting.
     */
    public function respectUpscale(&$current, $orig)
    {
        if (!$this->upscale && $current > $orig) {
            $this->log("# Disallowed upscale of $orig to $current");
            $current = $orig;
        }
    }



    /**
     * Set base for requested width and height.
     *
     * @param numeric|null $width  as requested target width
     * @param numeric|null $height as requested target height
     *
     * @throws Exception
     *
     * @return $this
     */
    public function setBaseWidthHeight($width = null, $height = null)
    {
        $this->log("# Set base for width and height.");

        $this->targetWidth  = $width;
        $this->targetHeight = $height;

        // Width specified as %
        if ($this->targetWidth[strlen($this->targetWidth)-1] == '%') {
            $this->targetWidth = $this->srcWidth * substr($this->targetWidth, 0, -1) / 100;
            $this->log(" Setting new width based on $width to {$this->targetWidth}.");
        }

        // Height specified as %
        if ($this->targetHeight[strlen($this->targetHeight)-1] == '%') {
            $this->targetHeight = $this->srcHeight * substr($this->targetHeight, 0, -1) / 100;
            $this->log(" Setting new height based on $height to {$this->targetHeight}.");
        }

        if (!(is_null($this->targetWidth) || is_numeric($this->targetWidth))) {
            throw new Exception('Width not numeric');
        }

        if (!(is_null($this->targetHeight) || is_numeric($this->targetHeight))) {
            throw new Exception('Height not numeric');
        }

        $this->log(" Requested target dimension as: {$this->targetWidth}x{$this->targetHeight}.");

        return $this;
    }



     /**
      * Set base for requested aspect ratio.
      *
      * @param float|null $aspectRatio as requested aspect ratio
      *
      * @throws Exception
      *
      * @return $this
      */
    public function setBaseAspecRatio($aspectRatio = null)
    {
        $this->log("# Set base for aspect ratio.");

        $this->aspectRatio = $aspectRatio;

        if (!(is_null($this->aspectRatio) || is_numeric($this->aspectRatio))) {
            throw new Exception("Aspect ratio out of range");
        }

        $this->log(" Requested aspectRatio={$this->aspectRatio}.");

        return $this;
    }



    /**
     * Set base for requested device pixel ratio.
     *
     * @param float $dpr as requested density pixel rate
     *
     * @throws Exception
     *
     * @return $this
     */
    public function setBaseDevicePixelRate($dpr = null)
    {
        $this->log("# Set base for device pixel rate.");

        $this->dpr = $dpr;

        if (!(is_null($dpr) || (is_numeric($this->dpr) && $this->dpr > 0))) {
            throw new Exception("Device pixel rate out of range");
        }

        $this->log(" Requested dpr={$this->dpr}.");

        return $this;
    }



    /**
     * Calculate target width and height by considering the selected
     * aspect ratio.
     *
     * @throws Exception
     *
     * @return $this
     */
    public function prepareByConsiderAspectRatio()
    {
        $this->log(" Prepare by aspect ratio {$this->aspectRatio}.");

        if (is_null($this->aspectRatio)) {
            return $this;
        }

        // Both null, use source as base for target
        if (is_null($this->targetWidth) && is_null($this->targetHeight)) {

            $this->targetWidth = ($this->aspectRatio >= 1)
                ? $this->srcWidth
                : null;

            $this->targetHeight = ($this->aspectRatio >= 1)
                ? null
                : $this->srcHeight;

            $this->log("  Using source as base {$this->targetWidth}x{$this->targetHeight}");

        }

        // Both or either set, calculate the other
        if (isset($this->targetWidth) && isset($this->targetHeight)) {

            $this->targetWidth = ($this->aspectRatio >= 1)
                ? $this->targetWidth
                : $this->targetHeight * $this->aspectRatio;

            $this->targetHeight = ($this->aspectRatio >= 1)
                ? $this->targetWidth / $this->aspectRatio
                : $this->targetHeight;

            $this->log("  New target width height {$this->targetWidth}x{$this->targetHeight}");

        } elseif (isset($this->targetWidth)) {

            $this->targetHeight = $this->targetWidth / $this->aspectRatio;
            $this->log("  New target height x{$this->targetHeight}");

        } elseif (isset($this->targetHeight)) {

            $this->targetWidth = $this->targetHeight * $this->aspectRatio;
            $this->log("  New target width {$this->targetWidth}x");

        }

        return $this;
    }



    /**
     * Calculate target width and height by considering the selected
     * dpr.
     *
     * @throws Exception
     *
     * @return $this
     */
    public function prepareByConsiderDpr()
    {
        $this->log(" Prepare by dpr={$this->dpr}.");

        if (is_null($this->dpr)) {
            return $this;
        }

        // If both not set, use source as base
        if (is_null($this->targetWidth) && is_null($this->targetHeight)) {
            $this->targetWidth  = $this->srcWidth;
            $this->targetHeight = $this->srcHeight;
        }

        if (isset($this->targetWidth)) {
            $this->targetWidth = $this->targetWidth * $this->dpr;
            $this->log("  Update target width to {$this->targetWidth}.");
        }

        if (isset($this->targetHeight)) {
            $this->targetHeight = $this->targetHeight * $this->dpr;
            $this->log("  Update target height to {$this->targetHeight}.");
        }

        return $this;
    }



     /**
      * Calculate target width and height and do sanity checks on constraints.
      * After this method the $targetWidth and $targetHeight will have
      * the expected dimensions on the target image.
      *
      * @throws Exception
      *
      * @return $this
      */
    public function prepareTargetDimensions()
    {
        $this->log("# Prepare target dimension (before): {$this->targetWidth}x{$this->targetHeight}.");

        $this->prepareByConsiderAspectRatio()
             ->prepareByConsiderDpr();

        $this->log(" Prepare target dimension (after): {$this->targetWidth}x{$this->targetHeight}.");

        return $this;
    }



     /**
      * Calculate new width and height of image.
      *
      * @return $this
      */
    public function calculateTargetWidthAndHeight()
    {
        $this->log("# Calculate new width and height.");
        $this->log(" Source size {$this->srcWidth}x{$this->srcHeight}.");
        $this->log(" Target dimension (before) {$this->targetWidth}x{$this->targetHeight}.");

        // Set default values to crop area to be whole source image
        $sw = $this->srcWidth;
        $sh = $this->srcHeight;
        $ar = $sw / $sh;
        $tw = $this->targetWidth;
        $th = $this->targetHeight;
        $dx = 0;
        $dy = 0;
        $dw = null;
        $dh = null;
        $cx = 0;
        $cy = 0;
        $cw = $sw;
        $ch = $sh;
        $rs = $this->resizeStrategy;
        $both = isset($tw) && isset($th);
        $ratio = $both ? $tw / $th : null;

        if (is_null($tw) && is_null($th)) {

            // No tw/th use sw/sh
            $tw = $sw;
            $th = $sh;
            $this->log("  New tw x th {$tw}x{$th}");

        } elseif (isset($tw) && is_null($th)) {

            // Keep aspect ratio, make th based on tw
            $this->respectUpscale($tw, $sw);
            $th = $tw / $ar;
            $this->log("  New th x{$th}");

        } elseif (is_null($tw) && isset($th)) {

            // Keep aspect ratio, make tw based on th
            $this->respectUpscale($th, $sh);
            $tw = $th * $ar;
            $this->log("  New tw {$tw}x");

        } elseif ($rs === CImageResizer::KEEP_RATIO && $both) {

            // Keep aspect ratio, make fit in box not larger than tw/th
            $this->log("  Keep ratio, ratio target=$ratio, source=$ar");
            
            if ($ratio > $ar) {
                $this->respectUpscale($th, $sh);
                $tw = $th * $ar;
                $this->log("   New tw {$tw}x");
            } elseif ($ratio < $ar) {
                $this->respectUpscale($tw, $sw);
                $th = $tw / $ar;
                $this->log("   New th x{$th}");
            } else {
                $this->respectUpscale($tw, $sw);
                $this->respectUpscale($th, $sh);
            }

        } elseif ($rs === CImageResizer::STRETCH && $both) {

            // Stretch to fit, leave as is
            $this->log("  Stretch, leave as is");
            $this->respectUpscale($tw, $sw);
            $this->respectUpscale($th, $sh);

        } elseif ($rs === CImageResizer::CROP_TO_FIT && $both) {

            // Crop to fit image in box
            // Ignores respectUpscale by intention
            $this->log("  Crop to fit, ratio target=$ratio, source=$ar");

            if ($ratio > $ar) {
                $ch = $sw / $ratio;
                $cy = ($sh - $ch) / 2;
            } elseif ($ratio < $ar) {
                $cw = $sh * $ratio;
                $cx = ($sw - $cw) / 2;
            }

            $this->log("   Parts cx=$cx, cy=$cy, cw=$cw, ch=$ch");

        } elseif ($rs === CImageResizer::FILL_TO_FIT && $both) {

            // Fill to fit image in box
            $this->log("  Fill to fit, ratio target=$ratio, source=$ar");
            $dw = $tw;
            $dh = $th;

            if ($ratio > $ar) {
                $dw = $th * $ar;
                $dh = $th;
            } elseif ($ratio < $ar) {
                $dw = $tw;
                $dh = $tw / $ar;
            }
            
            $this->respectUpscale($dw, $sw);
            $this->respectUpscale($dh, $sh);
            $dx = ($tw - $dw) / 2;
            $dy = ($th - $dh) / 2;

            $this->log("   Destination area dx=$dx, dy=$dy, dw=$dw, dh=$dh");

        }

        // All done, sum it up
        $dw = is_null($dw) ? $tw : $dw;
        $dh = is_null($dh) ? $th : $dh;

        $this->targetWidth       = round($tw);
        $this->targetHeight      = round($th);
        $this->destinationX      = round($dx);
        $this->destinationY      = round($dy);
        $this->destinationWidth  = round($dw);
        $this->destinationHeight = round($dh);
        $this->cropX             = round($cx);
        $this->cropY             = round($cy);
        $this->cropWidth         = round($cw);
        $this->cropHeight        = round($ch);

        $this->log(" Target dimension (after) {$this->targetWidth}x{$this->targetHeight}.");
        $this->log("  Crop area {$this->cropX}x{$this->cropY} by {$this->cropWidth}x{$this->cropHeight}.");
        $this->log("  Destination area {$this->destinationX}x{$this->destinationY} by {$this->destinationWidth}x{$this->destinationHeight}.");


/*

        // Check if there is an area to crop off
        if (isset($this->area)) {
            $this->offset['top']    = round($this->area['top'] / 100 * $this->srcHeight);
            $this->offset['right']  = round($this->area['right'] / 100 * $this->srcWidth);
            $this->offset['bottom'] = round($this->area['bottom'] / 100 * $this->srcHeight);
            $this->offset['left']   = round($this->area['left'] / 100 * $this->srcWidth);
            $this->offset['width']  = $this->srcWidth - $this->offset['left'] - $this->offset['right'];
            $this->offset['height'] = $this->srcHeight - $this->offset['top'] - $this->offset['bottom'];
            $this->srcWidth  = $this->offset['width'];
            $this->srcHeight = $this->offset['height'];
            $this->log("The offset for the area to use is top {$this->area['top']}%, right {$this->area['right']}%, bottom {$this->area['bottom']}%, left {$this->area['left']}%.");
            $this->log("The offset for the area to use is top {$this->offset['top']}px, right {$this->offset['right']}px, bottom {$this->offset['bottom']}px, left {$this->offset['left']}px, width {$this->offset['width']}px, height {$this->offset['height']}px.");
        }


        // Check if crop is set
        if ($this->crop) {
            $width  = $this->crop['width']  = $this->crop['width'] <= 0 ? $this->srcWidth + $this->crop['width'] : $this->crop['width'];
            $height = $this->crop['height'] = $this->crop['height'] <= 0 ? $this->srcHeight + $this->crop['height'] : $this->crop['height'];

            if ($this->crop['start_x'] == 'left') {
                $this->crop['start_x'] = 0;
            } elseif ($this->crop['start_x'] == 'right') {
                $this->crop['start_x'] = $this->srcWidth - $width;
            } elseif ($this->crop['start_x'] == 'center') {
                $this->crop['start_x'] = round($this->srcWidth / 2) - round($width / 2);
            }

            if ($this->crop['start_y'] == 'top') {
                $this->crop['start_y'] = 0;
            } elseif ($this->crop['start_y'] == 'bottom') {
                $this->crop['start_y'] = $this->srcHeight - $height;
            } elseif ($this->crop['start_y'] == 'center') {
                $this->crop['start_y'] = round($this->srcHeight / 2) - round($height / 2);
            }

            $this->log(" Crop area is width {$width}px, height {$height}px, start_x {$this->crop['start_x']}px, start_y {$this->crop['start_y']}px.");
        }



        // Crop, ensure to set new width and height
        if ($this->crop) {
            $this->log(" Crop.");
            $this->targetWidth = round(isset($this->targetWidth)
               ? $this->targetWidth
               : $this->crop['width']);
            $this->targetHeight = round(isset($this->targetHeight)
               ? $this->targetHeight
               : $this->crop['height']);
        }

*/

        return $this;
    }



    /**
     * Get source width.
     *
     * @return integer as source width
     */
    public function getSourceWidth()
    {
        return $this->srcWidth;
    }



    /**
     * Get source height.
     *
     * @return integer as source height
     */
    public function getSourceHeight()
    {
        return $this->srcHeight;
    }



    /**
     * Get target width.
     *
     * @return integer as target width
     */
    public function getTargetWidth()
    {
        return $this->targetWidth;
    }



    /**
     * Get target height.
     *
     * @return integer as target height
     */
    public function getTargetHeight()
    {
        return $this->targetHeight;
    }



    /**
     * Get destination x.
     *
     * @return integer as destination x
     */
    public function getDestinationX()
    {
        return $this->destinationX;
    }



    /**
     * Get destination y.
     *
     * @return integer as destination y
     */
    public function getDestinationY()
    {
        return $this->destinationY;
    }



    /**
     * Get destination width.
     *
     * @return integer as destination width
     */
    public function getDestinationWidth()
    {
        return $this->destinationWidth;
    }



    /**
     * Get destination height.
     *
     * @return integer as destination height
     */
    public function getDestinationHeight()
    {
        return $this->destinationHeight;
    }



    /**
     * Get crop position x.
     *
     * @return integer
     */
    public function getCropX()
    {
        return $this->cropX;
    }



    /**
    * Get crop position y.
    *
    * @return integer
    */
    public function getCropY()
    {
        return $this->cropY;
    }



    /**
    * Get crop width.
    *
    * @return integer
    */
    public function getCropWidth()
    {
        return $this->cropWidth;
    }



    /**
     * Get crop height.
     *
     * @return integer
     */
    public function getCropHeight()
    {
        return $this->cropHeight;
    }
}
