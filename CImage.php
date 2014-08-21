<?php
/**
 * Resize and crop images on the fly, store generated images in a cache.
 *
 * @author Mikael Roos mos@dbwebb.se
 * @example http://dbwebb.se/opensource/cimage
 * @link https://github.com/mosbth/cimage
 */
class CImage 
{

    /**
     * Constants type of PNG image
     */
    const PNG_GREYSCALE         = 0;
    const PNG_RGB               = 2;
    const PNG_RGB_PALETTE       = 3;
    const PNG_GREYSCALE_ALPHA   = 4;
    const PNG_RGB_ALPHA         = 6;
    


    /**
     * Constant for default image quality when not set
     */
    const JPEG_QUALITY_DEFAULT = 60;



    /**
     * Quality level for JPEG images.
     */
    private $quality;



    /**
     * Constant for default image quality when not set
     */
    const PNG_COMPRESSION_DEFAULT = -1;



    /**
     * Compression level for PNG images.
     */
    private $compress;



    /**
     * Where to save the target file.
     */
    private $saveFolder;



    /**
     * The working image object.
     */
    private $image;



    /**
     * The root folder of images (only used in constructor to create $pathToImage?).
     */
    private $imageFolder;



    /**
     * Image filename, may include subdirectory, relative from $imageFolder
     */
    private $imageSrc;



    /**
     * Actual path to the image, $imageFolder . '/' . $imageSrc
     */
    private $pathToImage;



    /**
     * Original file extension
     */
    private $fileExtension;



    /**
     * File extension to use when saving image
     */
    private $extension;



    /**
     * Verbose mode to print out a trace and display the created image
     */
    private $verbose = false;



    /**
     * Keep a log/trace on what happens
     */
    private $log = array(); 



    /**
     * Handle image as palette image
     */
    private $palette;



    /**
     * Target filename, with path, to save resulting image in.
     */
    private $cacheFileName;



    /**
     * Set a format to save image as, or null to use original format.
     */
    private $saveAs;


    /**
     * Path to command for filter optimize, for example optipng or null.
     */
    private $pngFilter;



    /**
     * Path to command for deflate optimize, for example pngout or null.
     */
    private $pngDeflate;



    /**
     * Path to command to optimize jpeg images, for example jpegtran or null.
     */
    private $jpegOptimize; 




  private $width; // Calculated from source image
  private $height; // Calculated from source image
  private $offset; 

  public $keepRatio;
  public $cropToFit;
  public $crop;




  /**
   * Properties
   */
  public $newWidth;
  public $newHeight;
  private $cropWidth;
  private $cropHeight;
  public $crop_x;
  public $crop_y;
  public $filters;
  private $type; // Calculated from source image
  private $attr; // Calculated from source image
  private $useCache; // Use the cache if true, set to false to ignore the cached file.
  private $useOriginal; // Use original image if possible




    /**
     * Constructor, can take arguments to init the object.
     *
     * @param string $imageSrc    filename which may contain subdirectory.
     * @param string $imageFolder path to root folder for images.
     * @param string $saveFolder  path to folder where to save the new file or null to skip saving.
     * @param string $saveName    name of target file when saveing.
     */
    public function __construct($imageSrc = null, $imageFolder = null, $saveFolder = null, $saveName = null) 
    {
        $this->setSource($imageSrc, $imageFolder);
        $this->setTarget($saveFolder, $saveName);
    }



    /**
     * Set verbose mode.
     *
     * @param boolean $mode true or false to enable and disable versbose mode, default is true.
     *
     * @return $this
     */
    public function setVerbose($mode = true) 
    {
        $this->verbose = $mode;
        return $this;
    }
  
  

    /**
     * Check if file extension is valid as a file extension.
     *
     * @param string $extension of image file.
     *
     * @return $this
     */
    private function checkFileExtension($extension) 
    {
        $valid = array('jpg', 'jpeg', 'png', 'gif');

        in_array($extension, $valid) 
            or $this->raiseError('Not a valid file extension.');

        return $this;
    }
  
  
  
    /**
     * Set src file.
     *
     * @param string $src of image.
     * @param string $dir as base directory where images are.
     *
     * @return $this
     */
    public function setSource($src = null, $dir = null)
    {
        if (!(isset($src) && isset($dir))) {
            return $this;
        }

        $this->imageSrc       = ltrim($src, '/');
        $this->imageFolder    = rtrim($dir, '/');
        $this->pathToImage    = $this->imageFolder . '/' . $this->imageSrc;
        $this->fileExtension  = pathinfo($this->pathToImage, PATHINFO_EXTENSION);
        $this->extension      = $this->fileExtension;
        
        $this->checkFileExtension($this->fileExtension);

        return $this;
    }
  
  
  
    /**
     * Set target file.
     *
     * @param string $src of target image.
     * @param string $dir as base directory where images are stored.
     *
     * @return $this
     */
    public function setTarget($src = null, $dir = null) 
    {
        if (!(isset($src) && isset($dir))) {
            return $this;
        }

        $this->saveFolder     = $dir;
        $this->cacheFileName  = $dir . '/' . $src;

        is_writable($this->saveFolder)
            or $this->raiseError('Target directory is not writable.');

        // Sanitize filename
        $this->cacheFileName = preg_replace('/^a-zA-Z0-9\.-_/', '', $this->cacheFileName);
        $this->log("The cache file name is: " . $this->cacheFileName);
        
        return $this;
    }
  
  
  
    /**
     * Set options to use when processing image.
     *
     * @param array $args used when processing image.
     *
     * @return $this
     */
    public function setOptions($args) 
    {
        $this->log("Set new options for processing image."); 

        $defaults = array(
            // Options for calculate dimensions
            'newWidth'    => null,
            'newHeight'   => null,
            'aspectRatio' => null,
            'keepRatio'   => true,
            'cropToFit'   => false,
            'crop'        => null, //array('width'=>null, 'height'=>null, 'start_x'=>0, 'start_y'=>0), 
            'area'        => null, //'0,0,0,0',

            // Options for caching or using original
            'useCache'    => true,
            'useOriginal' => true, 

            // Pre-processing, before resizing is done
            'scale'       => null, 

            // Post-processing, after resizing is done
            'palette'     => null,
            'filters'     => null,
            'sharpen'     => null,
            'emboss'      => null,
            'blur'        => null,

            // Options for saving
            //'quality'     => null,
            //'compress'    => null,
            //'saveAs'      => null,
        );

        // Convert crop settings from string to array
        if (isset($args['crop']) && !is_array($args['crop'])) {
            $pices = explode(',', $args['crop']);
            $args['crop'] = array(
                'width'   => $pices[0],
                'height'  => $pices[1],
                'start_x' => $pices[2],
                'start_y' => $pices[3],
            );
        }

        // Convert area settings from string to array
        if (isset($args['area']) && !is_array($args['area'])) {
                $pices = explode(',', $args['area']);
                $args['area'] = array(
                    'top'    => $pices[0],
                    'right'  => $pices[1],
                    'bottom' => $pices[2],
                    'left'   => $pices[3],
                );
        }

        // Convert filter settings from array of string to array of array
        if (isset($args['filters']) && is_array($args['filters'])) {
            foreach ($args['filters'] as $key => $filterStr) {
                $parts = explode(',', $filterStr);
                $filter = $this->mapFilter($parts[0]);
                $filter['str'] = $filterStr;
                for ($i=1;$i<=$filter['argc'];$i++) {
                    if (isset($parts[$i])) {
                        $filter["arg{$i}"] = $parts[$i];
                    } else {
                        throw new Exception('Missing arg to filter, review how many arguments are needed at http://php.net/manual/en/function.imagefilter.php');           
                    }
                }
                $args['filters'][$key] = $filter;
            }
        }

        // Merge default arguments with incoming and set properties.
        //$args = array_merge_recursive($defaults, $args);
        $args = array_merge($defaults, $args);
        foreach ($defaults as $key=>$val) {
            $this->{$key} = $args[$key];
        }

        return $this;
    }



    /**
     * Map filter name to PHP filter and id.
     *
     * @param string $name the name of the filter.
     *
     * @return array with filter settings
     * @throws Exception 
     */
    private function mapFilter($name) 
    {
        $map = array(
            'negate'          => array('id'=>0,  'argc'=>0, 'type'=>IMG_FILTER_NEGATE),    
            'grayscale'       => array('id'=>1,  'argc'=>0, 'type'=>IMG_FILTER_GRAYSCALE),
            'brightness'      => array('id'=>2,  'argc'=>1, 'type'=>IMG_FILTER_BRIGHTNESS),
            'contrast'        => array('id'=>3,  'argc'=>1, 'type'=>IMG_FILTER_CONTRAST),
            'colorize'        => array('id'=>4,  'argc'=>4, 'type'=>IMG_FILTER_COLORIZE),
            'edgedetect'      => array('id'=>5,  'argc'=>0, 'type'=>IMG_FILTER_EDGEDETECT),
            'emboss'          => array('id'=>6,  'argc'=>0, 'type'=>IMG_FILTER_EMBOSS),
            'gaussian_blur'   => array('id'=>7,  'argc'=>0, 'type'=>IMG_FILTER_GAUSSIAN_BLUR),
            'selective_blur'  => array('id'=>8,  'argc'=>0, 'type'=>IMG_FILTER_SELECTIVE_BLUR),
            'mean_removal'    => array('id'=>9,  'argc'=>0, 'type'=>IMG_FILTER_MEAN_REMOVAL),
            'smooth'          => array('id'=>10, 'argc'=>1, 'type'=>IMG_FILTER_SMOOTH),
            'pixelate'        => array('id'=>11, 'argc'=>2, 'type'=>IMG_FILTER_PIXELATE),
        );

        if (isset($map[$name]))
            return $map[$name];
        else {
            throw new Exception('No such filter.');
        }
    }
  
  

    /**
     * Init new width and height and do some sanity checks on constraints, before any 
     * processing can be done. 
     *
     * @return $this
     * @throws Exception
     */
    public function initDimensions() 
    {
        is_readable($this->pathToImage) 
            or $this->raiseError('Image file does not exist.');

        // Get details on image
        $info = list($this->width, $this->height, $this->type, $this->attr) = getimagesize($this->pathToImage);
        !empty($info) or $this->raiseError("The file doesn't seem to be an image.");

        if ($this->verbose) {
            $this->log("Image file: {$this->pathToImage}");
            $this->log("Image width x height (type): {$this->width} x {$this->height} ({$this->type}).");
            $this->log("Image filesize: " . filesize($this->pathToImage) . " bytes.");
        }

        // width as %
        if ($this->newWidth[strlen($this->newWidth)-1] == '%') {
            $this->newWidth = $this->width * substr($this->newWidth, 0, -1) / 100;
            $this->log("Setting new width based on % to {$this->newWidth}");
        }

        // height as %
        if ($this->newHeight[strlen($this->newHeight)-1] == '%') {
            $this->newHeight = $this->height * substr($this->newHeight, 0, -1) / 100;
            $this->log("Setting new height based on % to {$this->newHeight}");
        }

        is_null($this->aspectRatio) or is_numeric($this->aspectRatio) or $this->raiseError('Aspect ratio out of range');

        // width & height from aspect ratio
        if ($this->aspectRatio && is_null($this->newWidth) && is_null($this->newHeight)) {
            if ($this->aspectRatio >= 1) {
                $this->newWidth   = $this->width;
                $this->newHeight  = $this->width / $this->aspectRatio;
                $this->log("Setting new width & height based on width & aspect ratio (>=1) to (w x h) {$this->newWidth} x {$this->newHeight}");

            } else {
                $this->newHeight  = $this->height;
                $this->newWidth   = $this->height * $this->aspectRatio;
                $this->log("Setting new width & height based on width & aspect ratio (<1) to (w x h) {$this->newWidth} x {$this->newHeight}");
            }

        } elseif ($this->aspectRatio && is_null($this->newWidth)) {
            $this->newWidth   = $this->newHeight * $this->aspectRatio;
            $this->log("Setting new width based on aspect ratio to {$this->newWidth}");

        } elseif ($this->aspectRatio && is_null($this->newHeight)) {
            $this->newHeight  = $this->newWidth / $this->aspectRatio;
            $this->log("Setting new height based on aspect ratio to {$this->newHeight}");
        }

        // Check values to be within domain
        is_null($this->newWidth) 
            or is_numeric($this->newWidth) 
            or $this->raiseError('Width not numeric');

        is_null($this->newHeight) 
            or is_numeric($this->newHeight) 
            or $this->raiseError('Height not numeric');

        return $this;
    }



    /**
     * Calculate new width and height of image, based on settings.
     *
     * @return $this
     */
    public function calculateNewWidthAndHeight() 
    {
        // Crop, use cropped width and height as base for calulations
        $this->log("Calculate new width and height.");
        $this->log("Original width x height is {$this->width} x {$this->height}.");

        if (isset($this->area)) {
            $this->offset['top']    = round($this->area['top'] / 100 * $this->height);
            $this->offset['right']  = round($this->area['right'] / 100 * $this->width);
            $this->offset['bottom'] = round($this->area['bottom'] / 100 * $this->height);
            $this->offset['left']   = round($this->area['left'] / 100 * $this->width);
            $this->offset['width']  = $this->width - $this->offset['left'] - $this->offset['right'];
            $this->offset['height'] = $this->height - $this->offset['top'] - $this->offset['bottom'];
            $this->width  = $this->offset['width'];
            $this->height = $this->offset['height'];
            $this->log("The offset for the area to use is top {$this->area['top']}%, right {$this->area['right']}%, bottom {$this->area['bottom']}%, left {$this->area['left']}%.");
            $this->log("The offset for the area to use is top {$this->offset['top']}px, right {$this->offset['right']}px, bottom {$this->offset['bottom']}px, left {$this->offset['left']}px, width {$this->offset['width']}px, height {$this->offset['height']}px.");
        }

        $width  = $this->width;
        $height = $this->height;

        if ($this->crop) {
            $width  = $this->crop['width']  = $this->crop['width'] <= 0 ? $this->width + $this->crop['width'] : $this->crop['width'];
            $height = $this->crop['height'] = $this->crop['height'] <= 0 ? $this->height + $this->crop['height'] : $this->crop['height'];

            if ($this->crop['start_x'] == 'left') {
                $this->crop['start_x'] = 0;
            } elseif ($this->crop['start_x'] == 'right') {
                $this->crop['start_x'] = $this->width - $width;
            } elseif ($this->crop['start_x'] == 'center') {
                $this->crop['start_x'] = round($this->width / 2) - round($width / 2);
            } 

            if ($this->crop['start_y'] == 'top') {
                $this->crop['start_y'] = 0;
            } elseif ($this->crop['start_y'] == 'bottom') {
                $this->crop['start_y'] = $this->height - $height;
            } elseif ($this->crop['start_y'] == 'center') {
                $this->crop['start_y'] = round($this->height / 2) - round($height / 2);
            } 

            $this->log("Crop area is width {$width}px, height {$height}px, start_x {$this->crop['start_x']}px, start_y {$this->crop['start_y']}px.");
        }

        // Calculate new width and height if keeping aspect-ratio. 
        if ($this->keepRatio) {

            // Crop-to-fit and both new width and height are set.
            if ($this->cropToFit && isset($this->newWidth) && isset($this->newHeight)) {
                // Use newWidth and newHeigh as width/height, image should fit in box.
                ;
            } elseif (isset($this->newWidth) && isset($this->newHeight)) {
                // Both new width and height are set.
                // Use newWidth and newHeigh as max width/height, image should not be larger.
                $ratioWidth  = $width  / $this->newWidth;
                $ratioHeight = $height / $this->newHeight;
                $ratio = ($ratioWidth > $ratioHeight) ? $ratioWidth : $ratioHeight;
                $this->newWidth  = round($width  / $ratio);
                $this->newHeight = round($height / $ratio);
            } elseif (isset($this->newWidth)) {
                // Use new width as max-width
                $factor = (float)$this->newWidth / (float)$width;
                $this->newHeight = round($factor * $height);
            } elseif (isset($this->newHeight)) {
                // Use new height as max-hight
                $factor = (float)$this->newHeight / (float)$height;
                $this->newWidth = round($factor * $width);
            }
      
            // Use newWidth and newHeigh as defined width/height, image should fit the area.
            if ($this->cropToFit) {
                $ratioWidth  = $width  / $this->newWidth;
                $ratioHeight = $height / $this->newHeight;
                $ratio = ($ratioWidth < $ratioHeight) ? $ratioWidth : $ratioHeight;
                $this->cropWidth  = round($width  / $ratio);
                $this->cropHeight = round($height / $ratio);
            }      
        }

        // Crop, ensure to set new width and height
        if ($this->crop) {
            $this->newWidth = round(isset($this->newWidth) ? $this->newWidth : $this->crop['width']);
            $this->newHeight = round(isset($this->newHeight) ? $this->newHeight : $this->crop['height']);    
        }

        // No new height or width is set, use existing measures.
        $this->newWidth  = round(isset($this->newWidth) ? $this->newWidth : $this->width);
        $this->newHeight = round(isset($this->newHeight) ? $this->newHeight : $this->height);   
        $this->log("Calculated new width x height as {$this->newWidth} x {$this->newHeight}.");

        return $this;
    }



    /**
     * Set extension for filename to save as.
     *
     * @param string $saveas extension to save image as
     *
     * @return $this 
     */
    public function setSaveAsExtension($saveAs = null) 
    {
        if (isset($saveAs)) {
            $this->checkFileExtension($saveAs);
            $this->saveAs = $saveAs;
            $this->extension = $saveAs;
        }

        $this->log("Prepare to save image using as: " . $this->extension);

        return $this;
    }



    /**
     * Set JPEG quality to use when saving image
     *
     * @param int $quality as the quality to set.
     * 
     * @return $this
     */
    public function setJpegQuality($quality = null)
    {
        $this->quality = isset($quality)
            ? $quality
            : self::JPEG_QUALITY_DEFAULT;

        (is_numeric($this->quality) and $this->quality > 0 and $this->quality <= 100) 
            or $this->raiseError('Quality not in range.');

        $this->log("Setting JPEG quality to {$this->quality}.");

        return $this;
    }



    /**
     * Set PNG compressen algorithm to use when saving image
     *
     * @param int $compress as the algorithm to use.
     * 
     * @return $this
     */
    public function setPngCompression($compress = null)
    {
        $this->compress = isset($compress)
            ? $compress
            : self::PNG_COMPRESSION_DEFAULT;

        (is_numeric($this->compress) and $this->compress >= -1 and $this->compress <= 9) 
            or $this->raiseError('Quality not in range.');

        $this->log("Setting PNG compression level to {$this->compress}.");

        return $this;
    }



    /**
     * Use original image if possible, check options which affects image processing.
     *
     * @param boolean $useOrig default is to use original if possible, else set to false.
     *
     * @return $this
     */
    public function useOriginalIfPossible($useOrig = true) 
    {
        if ($useOrig 
            && ($this->newWidth == $this->width) 
            && ($this->newHeight == $this->height) 
            && !$this->area 
            && !$this->crop 
            && !$this->filters 
            && !$this->sharpen 
            && !$this->emboss 
            && !$this->blur 
            && !$this->palette
            && !$this->quality 
            && !$this->compress 
            && !$this->saveAs 
        ) {
            $this->log("Using original image.");
            $this->output($this->pathToImage);
        }
        return $this;
    }

 

    /**
     * Generate filename to save file in cache.
     *
     * @param string $base as basepath for storing file.
     *
     * @return $this
     */
    public function generateFilename($base) 
    {
        $parts      = pathinfo($this->pathToImage);
        $cropToFit  = $this->cropToFit   ? '_cf'                     : null;
        $crop_x     = $this->crop_x      ? "_x{$this->crop_x}"       : null;
        $crop_y     = $this->crop_y      ? "_y{$this->crop_y}"       : null;
        $scale      = $this->scale       ? "_s{$this->scale}"        : null;
        $quality    = $this->quality     ? "_q{$this->quality}"      : null;
        $compress   = $this->compress    ? "_co{$this->compress}" : null;

        $offset = isset($this->offset) 
            ? '_o' . $this->offset['top'] . '-' . $this->offset['right'] . '-' . $this->offset['bottom'] . '-' . $this->offset['left'] 
            : null;

        $crop = $this->crop 
            ? '_c' . $this->crop['width'] . '-' . $this->crop['height'] . '-' . $this->crop['start_x'] . '-' . $this->crop['start_y']
            : null;
    
        $filters = null;
        if (isset($this->filters)) {
            foreach ($this->filters as $filter) {
                if (is_array($filter)) {
                    $filters .= "_f{$filter['id']}";
                    for ($i=1;$i<=$filter['argc'];$i++) {
                        $filters .= ":".$filter["arg{$i}"];
                    }
                }
            }
        }

        $sharpen = $this->sharpen ? 's' : null;
        $emboss  = $this->emboss  ? 'e' : null;
        $blur    = $this->blur    ? 'b' : null;
        $palette = $this->palette ? 'p' : null;

        $this->extension = isset($this->extension) 
            ? $this->extension 
            : $parts['extension'];

        // Check optimizing options
        $optimize = null;
        if ($this->extension == 'jpeg' || $this->extension == 'jpg') {
            $optimize = $this->jpegOptimize ? 'o' : null;
        } elseif ($this->extension == 'png') {
            $optimize .= $this->pngFilter  ? 'f' : null;
            $optimize .= $this->pngDeflate ? 'd' : null;  
        }

        $subdir = str_replace('/', '-', dirname($this->imageSrc));
        $subdir = ($subdir == '.') ? '_.' : $subdir;
        $file = $subdir . '_' . $parts['filename'] . '_' . round($this->newWidth) . '_' 
            . round($this->newHeight) . $offset . $crop . $cropToFit . $crop_x . $crop_y 
            . $quality . $filters . $sharpen . $emboss . $blur . $palette . $optimize 
            . $scale . '.' . $this->extension;

        return $this->setTarget($file, $base);
    }



    /**
     * Use cached version of image, if possible.
     * 
     * @param boolean $useCache is default true, set to false to avoid using cached object.
     *
     * @return $this
     */
    public function useCacheIfPossible($useCache = true) 
    {
        if ($useCache && is_readable($this->cacheFileName)) {
            $fileTime   = filemtime($this->pathToImage);
            $cacheTime  = filemtime($this->cacheFileName);
            
            if ($fileTime <= $cacheTime) {
                if ($this->useCache) {
                    if ($this->verbose) {
                        $this->log("Use cached file.");
                        $this->log("Cached image filesize: " . filesize($this->cacheFileName) . " bytes."); 
                    }
                    $this->output($this->cacheFileName);
                } else {
                    $this->log("Cache is valid but ignoring it by intention.");
                }
            } else {
                $this->log("Original file is modified, ignoring cache.");
            }
        } else {
            $this->log("Cachefile does not exists or ignoring it.");
        }

        return $this;
    }


    
    /**
     * Load image from disk.
     *
     * @param string $src of image.
     * @param string $dir as base directory where images are.
     *
     * @return $this
     *
     */
    public function load($src = null, $dir = null) 
    {
        if (isset($src)) {
            $this->setSource($src, $dir);
        }

        $this->log("Opening file as {$this->fileExtension}.");

        switch ($this->fileExtension) {  
            case 'jpg':
            case 'jpeg': 
                $this->image = imagecreatefromjpeg($this->pathToImage);
                break;  
      
            case 'gif':
                $this->image = imagecreatefromgif($this->pathToImage); 
                break;  
      
            case 'png':  
                $this->image = imagecreatefrompng($this->pathToImage); 
                $type = $this->getPngType();
                $hasFewColors = imagecolorstotal($this->image);
        
                if ($type == self::PNG_RGB_PALETTE || ($hasFewColors > 0 && $hasFewColors <= 256)) {
                    if ($this->verbose) {
                        $this->log("Handle this image as a palette image.");
                    }
                    $this->palette = true;
                }
                break;  

            default: 
                $this->image = false; 
                throw new Exception('No support for this file extension.');
        }

        if ($this->verbose) {
            $this->log("imageistruecolor() : " . (imageistruecolor($this->image) ? 'true' : 'false'));
            $this->log("imagecolorstotal() : " . imagecolorstotal($this->image));
            $this->log("Number of colors in image = " . $this->colorsTotal($this->image));
        }

        return $this;
    }



    /**
     * Get the type of PNG image.
     *
     * @return int as the type of the png-image
     *
     */
    private function getPngType() 
    {
        $pngType = ord (file_get_contents ($this->pathToImage, false, null, 25, 1));

        switch ($pngType) {
            
            case self::PNG_GREYSCALE:
                $this->log("PNG is type 0, Greyscale.");
                break;

            case self::PNG_RGB: 
                $this->log("PNG is type 2, RGB");
                break;

            case self::PNG_RGB_PALETTE: 
                $this->log("PNG is type 3, RGB with palette");
                break;

            case self::PNG_GREYSCALE_ALPHA:
                $this->Log("PNG is type 4, Greyscale with alpha channel");
                break;
            
            case self::PNG_RGB_ALPHA:
                $this->Log("PNG is type 6, RGB with alpha channel (PNG 32-bit)");
                break;

            default:
                $this->Log("PNG is UNKNOWN type, is it really a PNG image?");
        }

        return $pngType;
    }



    /** 
     * Calculate number of colors in an image.
     *
     * @param resource $im the image.
     *
     * @return int 
     */
    private function colorsTotal($im) 
    {
        if (imageistruecolor($im)) {
            $h = imagesy($im);
            $w = imagesx($im);
            $c = array();
            for ($x=0; $x < $w; $x++) {
                for ($y=0; $y < $h; $y++) {
                    @$c['c'.imagecolorat($im, $x, $y)]++;
                }
            }
            return count($c);
        } else {
            return imagecolorstotal($im);
        }
    }



    /**
     * Preprocess image before rezising it.
     *
     * @return $this
     */
    public function preResize() 
    {
        $this->log("Pre-process before resizing");

        // Scale the original image before starting
        if (isset($this->scale)) {
            $this->log("Scale by {$this->scale}%");
            $newWidth  = $this->width * $this->scale / 100;
            $newHeight = $this->height * $this->scale / 100;
            $img = $this->CreateImageKeepTransparency($newWidth, $newHeight);
            imagecopyresampled($img, $this->image, 0, 0, 0, 0, $newWidth, $newHeight, $this->width, $this->height);
            $this->image = $img;
            $this->width = $newWidth;
            $this->height = $newHeight;
        } 

        return $this;   
    }



    /**
     * Resize and or crop the image.
     *
     * @return $this
     */
    public function resize() 
    {

        $this->log("Starting to Resize()");

        // Only use a specified area of the image, $this->offset is defining the area to use
        if (isset($this->offset)) {
            
            $this->log("Offset for area to use, cropping it width={$this->offset['width']}, height={$this->offset['height']}, start_x={$this->offset['left']}, start_y={$this->offset['top']}");
            $img = $this->CreateImageKeepTransparency($this->offset['width'], $this->offset['height']);
            imagecopy($img, $this->image, 0, 0, $this->offset['left'], $this->offset['top'], $this->offset['width'], $this->offset['height']);
            $this->image = $img;
            $this->width = $this->offset['width'];
            $this->height = $this->offset['height'];
        } 
        
        // SaveAs need to copy image to remove transparency, if any
        if ($this->saveAs) {
            
            $this->log("Copying image before saving as another format, loosing transparency, width={$this->width}, height={$this->height}.");
            $img = imagecreatetruecolor($this->width, $this->height);
            $bg = imagecolorallocate($img, 255, 255, 255);
            imagefill($img, 0, 0, $bg);
            imagecopy($img, $this->image, 0, 0, 0, 0, $this->width, $this->height);
            $this->image = $img;
        }

        // Do as crop, take only part of image
        if ($this->crop) {

            $this->log("Cropping area width={$this->crop['width']}, height={$this->crop['height']}, start_x={$this->crop['start_x']}, start_y={$this->crop['start_y']}");
            $img = $this->CreateImageKeepTransparency($this->crop['width'], $this->crop['height']);
            imagecopyresampled($img, $this->image, 0, 0, $this->crop['start_x'], $this->crop['start_y'], $this->crop['width'], $this->crop['height'], $this->crop['width'], $this->crop['height']);
            $this->image = $img;
            $this->width = $this->crop['width'];
            $this->height = $this->crop['height'];
        } 
    
        // Resize by crop to fit
        if ($this->cropToFit) {
            
            $this->log("Crop to fit");
            $cropX = round(($this->cropWidth/2) - ($this->newWidth/2));  
            $cropY = round(($this->cropHeight/2) - ($this->newHeight/2));  
            $imgPreCrop   = $this->CreateImageKeepTransparency($this->cropWidth, $this->cropHeight);
            $imageResized = $this->CreateImageKeepTransparency($this->newWidth, $this->newHeight);
            imagecopyresampled($imgPreCrop, $this->image, 0, 0, 0, 0, $this->cropWidth, $this->cropHeight, $this->width, $this->height);
            imagecopyresampled($imageResized, $imgPreCrop, 0, 0, $cropX, $cropY, $this->newWidth, $this->newHeight, $this->newWidth, $this->newHeight);
            $this->image = $imageResized;
            $this->width = $this->newWidth;
            $this->height = $this->newHeight;
        
        } else if (!($this->newWidth == $this->width && $this->newHeight == $this->height)) {
            
            // Resize it
            $this->log("Resizing, new height and/or width");
            $imageResized = $this->CreateImageKeepTransparency($this->newWidth, $this->newHeight);
            imagecopyresampled($imageResized, $this->image, 0, 0, 0, 0, $this->newWidth, $this->newHeight, $this->width, $this->height);
            //imagecopyresized($imageResized, $this->image, 0, 0, 0, 0, $this->newWidth, $this->newHeight, $this->width, $this->height);
            $this->image = $imageResized;
            $this->width = $this->newWidth;
            $this->height = $this->newHeight;
        }

        return $this;
    }



    /**
     * Postprocess image after rezising image.
     *
     * @return $this
     */
    public function postResize() 
    {
        $this->log("Post-process after resizing");

        // Apply filters
        if (isset($this->filters) && is_array($this->filters)) {
            
            foreach ($this->filters as $filter) {
                $this->log("Applying filter $filter.");
            
                switch ($filter['argc']) {
            
                    case 0: 
                        imagefilter($this->image, $filter['type']); 
                        break;
            
                    case 1: 
                        imagefilter($this->image, $filter['type'], $filter['arg1']); 
                        break;
            
                    case 2: 
                        imagefilter($this->image, $filter['type'], $filter['arg1'], $filter['arg2']); 
                        break;
            
                    case 3: 
                        imagefilter($this->image, $filter['type'], $filter['arg1'], $filter['arg2'], $filter['arg3']); 
                        break;

                    case 4: 
                        imagefilter($this->image, $filter['type'], $filter['arg1'], $filter['arg2'], $filter['arg3'], $filter['arg4']); 
                        break;
                }
            }
        }

        // Convert to palette image
        if($this->palette) {
            $this->log("Converting to palette image.");
            $this->trueColorToPalette();
        }

        // Blur the image
        if($this->blur) {
            $this->log("Blur.");
            $this->blurImage();
        }

        // Emboss the image
        if($this->emboss) {
            $this->log("Emboss.");
            $this->embossImage();
        }

        // Sharpen the image
        if($this->sharpen) {
            $this->log("Sharpen.");
            $this->sharpenImage();
        }

        return $this;   
    }



    /**
     * Convert true color image to palette image, keeping alpha.
     * http://stackoverflow.com/questions/5752514/how-to-convert-png-to-8-bit-png-using-php-gd-library
     *
     * @return void
     */
    public function trueColorToPalette() 
    {
        $img = imagecreatetruecolor($this->width, $this->height);
        $bga = imagecolorallocatealpha($img, 0, 0, 0, 127);
        imagecolortransparent($img, $bga);
        imagefill($img, 0, 0, $bga);
        imagecopy($img, $this->image, 0, 0, 0, 0, $this->width, $this->height);
        imagetruecolortopalette($img, false, 255);
        imagesavealpha($img, true);

        if (imageistruecolor($this->image)) {
            $this->log("Matching colors with true color image.");
            imagecolormatch($this->image, $img);
        }

        $this->image = $img;
    }



    /**
     * Sharpen image as http://php.net/manual/en/ref.image.php#56144
     * http://loriweb.pair.com/8udf-sharpen.html
     * 
     * @return $this
     */
    public function sharpenImage() 
    {
        $matrix = array(
            array(-1,-1,-1,),
            array(-1,16,-1,),
            array(-1,-1,-1,),
        );

        $divisor = 8;
        $offset  = 0;
        
        imageconvolution($this->image, $matrix, $divisor, $offset);
        
        return $this;
    }



    /**
     * Emboss image as http://loriweb.pair.com/8udf-emboss.html
     * 
     * @return $this
     */
    public function embossImage() 
    {
        $matrix = array(
            array( 1, 1,-1,),
            array( 1, 3,-1,),
            array( 1,-1,-1,),
        );
    
        $divisor = 3;
        $offset  = 0;
    
        imageconvolution($this->image, $matrix, $divisor, $offset);
    
        return $this;
    }



    /**
     * Blur image as http://loriweb.pair.com/8udf-basics.html
     * 
     * @return $this
     */
    public function blurImage() 
    {
        $matrix = array(
            array( 1, 1, 1,),
            array( 1,15, 1,),
            array( 1, 1, 1,),
        );
    
        $divisor = 23;
        $offset  = 0;
    
        imageconvolution($this->image, $matrix, $divisor, $offset);
    
        return $this;
    }



    /**
     * Create a image and keep transparency for png and gifs.
     *
     * @param int $width of the new image.
     * @param int $height of the new image.
     * @return image resource.
    */
    private function createImageKeepTransparency($width, $height) 
    {
        $this->log("Creating a new working image width={$width}px, height={$height}px.");
        $img = imagecreatetruecolor($width, $height);
        imagealphablending($img, false);
        imagesavealpha($img, true);  

        /*
        $this->Log("Filling image with background color.");
        $bg = imagecolorallocate($img, 255, 255, 255);
        imagefill($img, 0, 0, $bg);
        */

        return $img;
    }

 

    /**
     * Set optimizing  and post-processing options. CHANGE FROM DEFINE TO INJECT INTO CLASS, TO BE ABLE TO SET OFF POSTPROCESSING
     *
     * @param array $options with config for postprocessing with external tools.
     *
     * @return $this
     */
    public function setPostProcessingOptions($options) 
    {
        if (isset($options['jpeg_optimize']) && $options['jpeg_optimize']) {
            $this->jpegOptimizeCmd = $options['jpeg_optimize_cmd'];
        } else {
            $this->jpegOptimizeCmd = null;            
        }

        if (isset($options['png_filter']) && $options['png_filter']) {
            $this->pngFilterCmd = $options['png_filter_cmd'];
        } else {
            $this->pngFilterCmd = null;
        }

        if (isset($options['png_deflate']) && $options['png_deflate']) {
            $this->pngDeflateCmd = $options['png_deflate_cmd'];
        } else {
            $this->pngDeflateCmd = null;
        }
    
        return $this;
    }



    /**
     * Save image.
     *
     * @param string $src  as target filename.
     * @param string $base as base directory where to store images.
     *
     * @return $this or false if no folder is set.
     */
    public function save($src = null, $base = null)
    {
        if (isset($src)) {
            $this->setTarget($src, $base);
        }

        switch($this->extension) {
            
            case 'jpeg':
            case 'jpg':
                $this->Log("Saving image as JPEG to cache using quality = {$this->quality}.");
                imagejpeg($this->image, $this->cacheFileName, $this->quality);
          
                // Use JPEG optimize if defined
                if ($this->jpegOptimizeCmd) {
                    if ($this->verbose) { 
                        clearstatcache(); 
                        $this->log("Filesize before optimize: " . filesize($this->cacheFileName) . " bytes."); 
                    }
                    $res = array();
                    $cmd = $this->jpegOptimizeCmd . " -outfile $this->cacheFileName $this->cacheFileName";
                    exec($cmd, $res);
                    $this->log($cmd);
                    $this->log($res);
                }
                break;  

            case 'gif':
                if ($this->saveFolder) {
                    $this->Log("Saving image as GIF to cache.");
                    imagegif($this->image, $this->cacheFileName);  
                }
                break;  

            case 'png':  
                $this->Log("Saving image as PNG to cache using compression = {$this->compress}.");

                // Turn off alpha blending and set alpha flag
                imagealphablending($this->image, false);
                imagesavealpha($this->image, true);
                imagepng($this->image, $this->cacheFileName, $this->compress);  
              
                // Use external program to filter PNG, if defined
                if ($this->pngFilterCmd) {
                    if ($this->verbose) { 
                        clearstatcache(); 
                        $this->Log("Filesize before filter optimize: " . filesize($this->cacheFileName) . " bytes."); 
                    }
                    $res = array();
                    $cmd = $this->pngFilterCmd . " $this->cacheFileName";
                    exec($cmd, $res);
                    $this->Log($cmd);
                    $this->Log($res);
                }

                // Use external program to deflate PNG, if defined
                if ($this->pngDeflateCmd) {
                    if ($this->verbose) { 
                        clearstatcache(); 
                        $this->Log("Filesize before deflate optimize: " . filesize($this->cacheFileName) . " bytes."); 
                    }
                    $res = array();
                    $cmd = $this->pngDeflateCmd . " $this->cacheFileName";
                    exec($cmd, $res);
                    $this->Log($cmd);
                    $this->Log($res);
                }
                break;  

            default:
                $this->RaiseError('No support for this file extension.');
                break;
        }  

        if ($this->verbose) {
            clearstatcache();
            $this->log("Cached image filesize: " . filesize($this->cacheFileName) . " bytes."); 
            $this->log("imageistruecolor() : " . (imageistruecolor($this->image) ? 'true' : 'false'));
            $this->log("imagecolorstotal() : " . imagecolorstotal($this->image));
            $this->log("Number of colors in image = " . $this->ColorsTotal($this->image));
        }

        return $this;
    }



    /**
     * Output image to browser using caching.
     *
     * @param string $file to read and output, default is to use $this->cacheFileName
     *
     * @return void
     */
    public function output($file = null)
    {
        if (is_null($file)) {
            $file = $this->cacheFileName;
        }

        $this->log("Outputting image: $file");

        // Get details on image
        $info = list($width, $height, $type, $attr) = getimagesize($file);
        !empty($info) or $this->raiseError("The file doesn't seem to be an image.");
        $mime = $info['mime'];
        $lastModified = filemtime($file);
        $gmdate = gmdate("D, d M Y H:i:s", $lastModified);

        if (!$this->verbose) {
            header('Last-Modified: ' . $gmdate . " GMT");
        }

        if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $lastModified) {
            
            if ($this->verbose) {
                $this->log("304 not modified");
                $this->verboseOutput();
                exit;
            }
            
            header("HTTP/1.0 304 Not Modified");
        
        } else {
        
            if ($this->verbose) {
                $this->log("Last modified: " . $gmdate . " GMT");
                $this->verboseOutput();
                exit;
            }

            header('Content-type: ' . $mime);
            readfile($file);
        }
        
        exit;
    }



    /**
     * Log an event if verbose mode.
     *
     * @param string $message to log.
     *
     * @return this
     */
    public function log($message)
    {
        if ($this->verbose) {
            $this->log[] = $message;
        }

        return $this;
    }
  
  
  
    /**
     * Do verbose output and print out the log and the actual images.
     *
     * @return void
     */
    private function verboseOutput()
    {
        $log = null;
        $this->log("Memory peak: " . round(memory_get_peak_usage() /1024/1024) . "M");
        $this->log("Memory limit: " . ini_get('memory_limit'));

        $included = get_included_files();
        $this->log("Included files: " . count($included));
    
        foreach ($this->log as $val) {
            if (is_array($val)) {
                foreach ($val as $val1) {
                    $log .= htmlentities($val1) . '<br/>';
                }
            } else {
                $log .= htmlentities($val) . '<br/>';
            }
        }

        echo <<<EOD
<!doctype html>
<html lang=en>
<meta charset=utf-8>
<title>CImage verbose output</title>
<style>body{background-color: #ddd}</style>
<h1>CImage Verbose Output</h1>
<pre>{$log}</pre>
EOD;
    }



    /**
     * Raise error, enables to implement a selection of error methods.
     *
     * @param string $message the error message to display.
     *
     * @return void 
     * @throws Exception
     */
    private function raiseError($message)
    {
        throw new Exception($message);
    }
}
