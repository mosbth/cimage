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
     * Target image dimensions.
     */
    private $targetWidth;
    //private $targetWidthOrig;  // Save original value
    private $targetHeight;
    //private $targetheightOrig; // Save original value



    /**
     * Change target height & width when different dpr, dpr 2 means double
     * image dimensions.
     */
    private $dpr;



    /**
     * Set aspect ratio for the target image.
     */
    private $aspectRatio;



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
            echo $str . "\n";
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
         $this->log("Source image dimension: {$this->srcWidth} x {$this->srcHeight}.");

         return $this;
     }



    /**
     * Set the basis to consider when calculating target dimensions.
     *
     * @param numeric|null $width       as requested target width 
     * @param numeric|null $height      as requested density pixel rate
     * @param float|null   $aspectRatio as requested aspect ratio
     * @param float        $dpr         as requested density pixel rate
     *
     * @throws Exception
     *
     * @return $this
     */
    public function setBaseForCalculateTarget($width = null, $height = null, $aspectRatio = null, $dpr = 1)
    {
        $this->targetWidth  = $width;
        $this->targetheight = $height;
        $this->aspectRatio  = $aspectRatio;
        $this->dpr          = $dpr;

        // Width specified as %
        if ($this->targetWidth[strlen($this->targetWidth)-1] == '%') {
            $this->targetWidth = $this->width * substr($this->targetWidth, 0, -1) / 100;
            $this->log("Setting new width based on $width% to {$this->targetWidth}px.");
        }

        // Height specified as %
        if ($this->targetheight[strlen($this->targetheight)-1] == '%') {
            $this->targetheight = $this->height * substr($this->targetheight, 0, -1) / 100;
            $this->log("Setting new height based on $height% to {$this->targetheight}px.");
        }

        // Width is valid
        if (!(is_null($this->targetWidth) || is_numeric($this->targetWidth))) {
            throw new Exception('Width not numeric');
        }

        // Height is valid
        if (!(is_null($this->targetheight) || is_numeric($this->targetheight))) {
            throw new Exception('Height not numeric');
        }

        // Aspect ratio is valid
        if (!(is_null($this->aspectRatio) || is_numeric($this->aspectRatio))) {
            throw new Exception('Aspect ratio out of range');
        }

        // Device pixel ratio is valid
        if (!(is_numeric($this->dpr) && $this->dpr > 0)) {
            throw new Exception('Device pixel rate out of range');
        }

        $this->log("Requested target dimension as: {$this->targetWidth} x {$this->targetheight} aspectRatio={$this->aspectRatio}, dpr={$this->dpr}.");

        return $this;
    }



     /**
      * Calculate target width and height and do sanity checks on constraints.
      * After this method the $targetWidth and $targetHeight will have
      * the expected dimensions on the target image.
      *
      * @param integer $width  of source image.
      * @param integer $height of source image.
      *
      * @throws Exception
      *
      * @return $this
      */
     public function prepareTargetDimensions()
     {
         $this->log("Prepate target dimension before calculate: {$this->targetWidth} x {$this->targetheight}.");

         // width & height from aspect ratio
         if ($this->aspectRatio && is_null($this->targetWidth) && is_null($this->targetheight)) {
             if ($this->aspectRatio >= 1) {
                 $this->targetWidth   = $this->width;
                 $this->targetheight  = $this->width / $this->aspectRatio;
                 $this->log("Setting new width & height based on width & aspect ratio (>=1) to (w x h) {$this->targetWidth} x {$this->targetheight}");

             } else {
                 $this->targetheight  = $this->height;
                 $this->targetWidth   = $this->height * $this->aspectRatio;
                 $this->log("Setting new width & height based on width & aspect ratio (<1) to (w x h) {$this->targetWidth} x {$this->targetheight}");
             }

         } elseif ($this->aspectRatio && is_null($this->targetWidth)) {
             $this->targetWidth   = $this->targetheight * $this->aspectRatio;
             $this->log("Setting new width based on aspect ratio to {$this->targetWidth}");

         } elseif ($this->aspectRatio && is_null($this->targetheight)) {
             $this->targetheight  = $this->targetWidth / $this->aspectRatio;
             $this->log("Setting new height based on aspect ratio to {$this->targetheight}");
         }

         // Change width & height based on dpr
         if ($this->dpr != 1) {
             if (!is_null($this->targetWidth)) {
                 $this->targetWidth  = round($this->targetWidth * $this->dpr);
                 $this->log("Setting new width based on dpr={$this->dpr} - w={$this->targetWidth}");
             }
             if (!is_null($this->targetheight)) {
                 $this->targetheight = round($this->targetheight * $this->dpr);
                 $this->log("Setting new height based on dpr={$this->dpr} - h={$this->targetheight}");
             }
         }

         $this->log("prepare target dimension after calculate: {$this->targetWidth} x {$this->targetheight}.");

         return $this;
     }



     /**
      * Get target width.
      *
      * @return $integer as target width
      */
     public function width()
     {
         return $this->targetWidth;
     }



     /**
      * Get target height.
      *
      * @return $integer as target height
      */
     public function height()
     {
         return $this->targetHeight;
     }



}
