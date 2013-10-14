<?php
/**
 * Resize and crop images on the fly. Store generated images in a cache.
 *
 * @author Mikael Roos mos@dbwebb.se
 * @example http://mikaelroos.se/cimage/test.php
 * @link https://github.com/mosbth/cimage
 */
class CImage {

  /**
   * Constants
   */
  const PNG_GREYSCALE = 0;
  const PNG_RGB = 2;
  const PNG_RGB_PALETTE = 3;
  const PNG_GREYSCALE_ALPHA = 4;
  const PNG_RGB_ALPHA = 6;
  const PNG_QUALITY_DEFAULT = -1;
  const JPEG_QUALITY_DEFAULT = 60;


  /**
   * Properties
   */
  private $image = null; // Object for open image
  public $imageFolder; // root folder of images
  public $imageName; // image filename, may include subdirectory, relative from $imageFolder
  public $pathToImage; // $imageFolder . '/' . $imageName;
  private $fileExtension;
  public $newWidth;
  public $newHeight;
  private $cropWidth;
  private $cropHeight;
  public $keepRatio;
  public $cropToFit;
  public $crop;
  public $crop_x;
  public $crop_y;
  private $quality;
  public $filters;
  public $saveFolder;
  public $newName;
  //private $newFileName; // OBSOLETE, using cacheFileName instead.
  private $mime; // Calculated from source image
  private $width; // Calculated from source image
  private $height; // Calculated from source image
  private $type; // Calculated from source image
  private $attr; // Calculated from source image
  private $validExtensions = array('jpg', 'jpeg', 'png', 'gif');
  private $verbose; // Print out a trace together with original and created image
  private $log; // Keep a log/trace on what happens.
  private $cacheFileName; // Filename of the new image in the cache.
  private $useCache; // Use the cache if true, set to false to ignore the cached file.
  private $useOriginal; // Use original image if possible
  private $saveAs; // Define a format to save image as, or null to use original format.
  private $extension; // Extension to save image as.

  // Specific for PNG
  private $pngType; // Find out which type of PNG image it is.
  private $pngFilter; // Path to command for filter optimize, for example optipng or null.
  private $pngDeflate;  // Path to command for deflate optimize, for example pngout or null.

  // Specific for JPEG
  private $jpegOptimize; // Path to command to optimize jpeg images, for example jpegtran or null.


  /**
   * Constructor, can take arguments to init the object.
   *
   * @param string $imageName filename which may contain subdirectory.
   * @param string $imageFolder path to root folder for images.
   * @param string $saveFolder path to folder where to save the new file or null to skip saving.
   * @param string $newName new filename or leave to null to autogenerate filename.
   */
  public function __construct($imageName=null, $imageFolder=null, $saveFolder=null, $newName=null) {
    $this->imageName      = ltrim($imageName, '/');
    $this->imageFolder    = rtrim($imageFolder, '/');
    $this->pathToImage    = $this->imageFolder . '/' . $this->imageName;
    $this->fileExtension  = pathinfo($this->pathToImage, PATHINFO_EXTENSION);
    $this->extension      = $this->fileExtension;
    $this->saveFolder     = $saveFolder;
    $this->newName        = $newName;
  }


  
  /**
   * Log an event.
   *
   * @param string $message to log.
   */
  public function Log($message) {
    if($this->verbose) {
      $this->log[] = $message; 
    }
  }
  
  
  
  /**
   * Do verbose output and print out the log and the actual images.
   *
   */
  public function VerboseOutput() {
    $log = null;
    $this->Log("Memory peak: " . round(memory_get_peak_usage() /1024/1024) . "M");
    $this->Log("Memory limit: " . ini_get('memory_limit'));
    foreach($this->log as $val) {
      if(is_array($val)) {
        foreach($val as $val1) {
          $log .= htmlentities($val1) . '<br/>';
        }
      }
      else {
        $log .= htmlentities($val) . '<br/>';
      }
    }

    $object = null; //print_r($this, 1);

    echo <<<EOD
<!doctype html>
<html lang=en>
<meta charset=utf-8>
<title>CImage verbose output</title>
<style>body{background-color: #ddd}</style>
<h1>CImage Verbose Output</h1>
<pre>{$log}</pre>
<pre>{$object}</pre>
EOD;
  }
  
  
  /**
   * Raise error, enables to implement a selection of error methods.
   *
   * @param $message string the error message to display.
   */
  public function RaiseError($message) {
    throw new Exception($message);
  }


  
  /*
   * Create filename to save file in cache.
   */
  public function CreateFilename() {
    $parts = pathinfo($this->pathToImage);
    $cropToFit = $this->cropToFit ? '_cf' : null;
    $crop_x = $this->crop_x ? "_x{$this->crop_x}" : null;
    $crop_y = $this->crop_y ? "_y{$this->crop_y}" : null;
    $scale  = $this->scale ? "_s{$this->scale}" : null;
    $quality = $this->quality ? "_q{$this->quality}" : null;
    $offset = isset($this->offset) ? '_o' . $this->offset['top'] . '-' . $this->offset['right'] . '-' . $this->offset['bottom'] . '-' . $this->offset['left'] : null;
    $crop = $this->crop ? '_c' . $this->crop['width'] . '-' . $this->crop['height'] . '-' . $this->crop['start_x'] . '-' . $this->crop['start_y'] : null;
    $filters = null;
    if(isset($this->filters)) {
      foreach($this->filters as $filter) {
        if(is_array($filter)) {
          $filters .= "_f{$filter['id']}";
          for($i=1;$i<=$filter['argc'];$i++) {
            $filters .= ":".$filter["arg{$i}"];
          }
        }
      }
    }
    $sharpen = $this->sharpen ? 's' : null;
    $emboss = $this->emboss ? 'e' : null;
    $blur = $this->blur ? 'b' : null;
    $palette = $this->palette ? 'p' : null;

    $this->extension = isset($this->extension) ? $this->extension : $parts['extension'];
    
    // Check optimizing options
    $optimize = null;
    if($this->extension == 'jpeg' || $this->extension == 'jpg') {
     $optimize = $this->jpegOptimize ? 'o' : null;
    }
    else if($this->extension == 'png') {
      $optimize .= $this->pngFilter ? 'f' : null;
      $optimize .= $this->pngDeflate ? 'd' : null;  
    }

    $subdir = str_replace('/', '-', dirname($this->imageName));
    $subdir = ($subdir == '.') ? '_.' : $subdir;
    $this->cacheFileName = $this->saveFolder . '/' . $subdir . '_' . $parts['filename'] . '_' . round($this->newWidth) . '_' . round($this->newHeight) . $offset . $crop . $cropToFit . $crop_x . $crop_y . $quality . $filters . $sharpen . $emboss . $blur . $palette . $optimize . $scale . '.' . $this->extension;
    
    // Sanitize filename
    $this->cacheFileName = preg_replace('/^a-zA-Z0-9\.-_/', '', $this->cacheFileName);
    $this->Log("The cache file name is: " . $this->cacheFileName);
    return $this;
  }
  
  
  
  /**
   * Init and do some sanity checks before any processing is done. Throws exception if not valid.
   */
  public function Init() {
    // Get details on image
    $info = list($this->width, $this->height, $this->type, $this->attr) = getimagesize($this->pathToImage);
    !empty($info) or $this->RaiseError("The file doesn't seem to be an image.");
    $this->mime = $info['mime'];

    if($this->verbose) {
      $this->Log("Image file: {$this->pathToImage}");
      $this->Log("Image width x height (type): {$this->width} x {$this->height} ({$this->type}).");
      $this->Log("Image filesize: " . filesize($this->pathToImage) . " bytes.");
    }

    // width as %
    if($this->newWidth[strlen($this->newWidth)-1] == '%') {
      $this->newWidth = $this->width * substr($this->newWidth, 0, -1) / 100;
      $this->Log("Setting new width based on % to {$this->newWidth}");
    }

    // height as %
    if($this->newHeight[strlen($this->newHeight)-1] == '%') {
      $this->newHeight = $this->height * substr($this->newHeight, 0, -1) / 100;
      $this->Log("Setting new height based on % to {$this->newHeight}");
    }

    is_null($this->aspectRatio) or is_numeric($this->aspectRatio) or $this->RaiseError('Aspect ratio out of range');
    
    // width & height from aspect ratio
    if($this->aspectRatio && is_null($this->newWidth) && is_null($this->newHeight)) {
      // set new width and height based on current & aspect ratio, but base on largest dimension to only shrink image, not enlarge
        if($this->aspectRatio >= 1) {
          $this->newWidth   = $this->width;
          $this->newHeight  = $this->width / $this->aspectRatio;
          $this->Log("Setting new width & height based on width & aspect ratio (>=1) to (w x h) {$this->newWidth} x {$this->newHeight}");
        }
        else {
          $this->newHeight  = $this->height;
          $this->newWidth   = $this->height * $this->aspectRatio;
          $this->Log("Setting new width & height based on width & aspect ratio (<1) to (w x h) {$this->newWidth} x {$this->newHeight}");
        }
    }
    else if($this->aspectRatio && is_null($this->newWidth)) {
      $this->newWidth   = $this->newHeight * $this->aspectRatio;
      $this->Log("Setting new width based on aspect ratio to {$this->newWidth}");
    }
    else if($this->aspectRatio && is_null($this->newHeight)) {
      //$this->newHeight  = ($this->aspectRatio >= 0) ? ($this->newWidth / $this->aspectRatio) : ($this->newWidth * $this->aspectRatio);
      $this->newHeight  = $this->newWidth / $this->aspectRatio;
      $this->Log("Setting new height based on aspect ratio to {$this->newHeight}");
    }

    // Check values to be within domain
    is_null($this->newWidth) or is_numeric($this->newWidth) or $this->RaiseError('Width not numeric');
    is_null($this->newHeight) or is_numeric($this->newHeight) or $this->RaiseError('Height not numeric');
    is_null($this->quality) or (is_numeric($this->quality) and $this->quality > 0 and $this->quality <= 100) or $this->RaiseError('Quality not in range.');
    //is_numeric($this->crop_x) && is_numeric($this->crop_y) or $this->RaiseError('Quality not in range.');
    //filter
    is_readable($this->pathToImage) or $this->RaiseError('File does not exist.');
    in_array($this->fileExtension, $this->validExtensions) or $this->RaiseError('Not a valid file extension.');
    is_null($this->saveFolder) or is_writable($this->saveFolder) or $this->RaiseError('Save directory does not exist or is not writable.');

    return $this;
  }
  

  /**
   * Output image using caching.
   *
   */
  protected function Output($file) {
    if($this->verbose) {
      $this->Log("Outputting image: $file");
    }

    // Get details on image
    $info = list($width, $height, $type, $attr) = getimagesize($file);
    !empty($info) or $this->RaiseError("The file doesn't seem to be an image.");
    $mime = $info['mime'];
    $lastModified = filemtime($file);  
    $gmdate = gmdate("D, d M Y H:i:s", $lastModified);

    if(!$this->verbose) { header('Last-Modified: ' . $gmdate . " GMT"); }

    if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $lastModified){  
      if($this->verbose) {
        $this->Log("304 not modified");
        $this->VerboseOutput();
        exit;
      }
      header("HTTP/1.0 304 Not Modified");
    } else {  
      if($this->verbose) {
        $this->Log("Last modified: " . $gmdate . " GMT");
        $this->VerboseOutput();
        exit;
      }

      header('Content-type: ' . $mime);  
      readfile($file);
    }
    exit;
  }
 


  /**
   * Get the type of PNG image.
   *
   */
  public function GetPngType() {
     $this->pngType = ord (file_get_contents ($this->pathToImage, false, null, 25, 1));
     if($this->pngType == self::PNG_GREYSCALE) {
       $this->Log("PNG is type 0, Greyscale.");
     }
     else if($this->pngType == self::PNG_RGB) {
       $this->Log("PNG is type 2, RGB");
     }
     else if($this->pngType == self::PNG_RGB_PALETTE) {
       $this->Log("PNG is type 3, RGB with palette");
     }
     else if($this->pngType == self::PNG_GREYSCALE_ALPHA) {
       $this->Log("PNG is type 4, Greyscale with alpha channel");
     }
     else if($this->pngType == self::PNG_RGB_ALPHA) {
       $this->Log("PNG is type 6, RGB with alpha channel (PNG 32-bit)");
     }
  
     return $this->pngType;
  }


 
  /**
   * Set quality of image
   *
   */
  protected function SetQuality() {
    if(!$this->quality) {
      switch($this->extension) {
        case 'jpeg':
        case 'jpg':
          $this->quality = self::JPEG_QUALITY_DEFAULT;
          break;

        case 'png':
          $this->quality = self::PNG_QUALITY_DEFAULT;
          break;

        default:
          $this->quality = null;
      }
    }
    $this->Log("Setting quality to {$this->quality}.");
    return $this;
  }



  /**
   * Set optmizing options.
   *
   */
  protected function SetOptimization() {
    if(defined('JPEG_OPTIMIZE')) {
      $this->jpegOptimize = JPEG_OPTIMIZE;
    }

    if(defined('PNG_FILTER')) {
      $this->pngFilter = PNG_FILTER;
    }

    if(defined('PNG_DEFLATE')) {
      $this->pngDeflate = PNG_DEFLATE;
    }
    return $this;
  }



  /** 
   * Calciulate number of colors in an image.
   *
   * @param resource $im the image.
   */
  protected function ColorsTotal($im) {
    if(imageistruecolor($im)) {
      $h = imagesy($im);
      $w = imagesx($im);
      $c = array();
      for($x=0; $x < $w; $x++) {
        for($y=0; $y < $h; $y++) {
          @$c['c'.imagecolorat($im, $x, $y)]++;
        }
      }
      return count($c);
    }
    else {
      return imagecolorstotal($im);
    }
  }



  /**
   * Open image.
   *
   */
  protected function Open() {
    $this->Log("Opening file as {$this->fileExtension}.");
    switch($this->fileExtension) {  
      case 'jpg':
      case 'jpeg': 
        $this->image = @imagecreatefromjpeg($this->pathToImage);
        break;  
      
      case 'gif':
        $this->image = @imagecreatefromgif($this->pathToImage); 
        break;  
      
      case 'png':  
        $this->image = @imagecreatefrompng($this->pathToImage); 
        $type = $this->GetPngType();
        $hasFewColors = imagecolorstotal($this->image);
        if($type == self::PNG_RGB_PALETTE || ($hasFewColors > 0 && $hasFewColors <= 256)) {
          if($this->verbose) {
            $this->Log("Handle this image as a palette image.");
          }
          $this->palette = true;
        }
        break;  

      default: $this->image = false; $this->RaiseError('No support for this file extension.');
    }

    if($this->verbose) {
      $this->Log("imageistruecolor() : " . (imageistruecolor($this->image) ? 'true' : 'false'));
      $this->Log("imagecolorstotal() : " . imagecolorstotal($this->image));
      $this->Log("Number of colors in image = " . $this->ColorsTotal($this->image));
    }

    return $this;
  }
  


  /**
   * Map filter name to PHP filter and id.
   *
   * @param string $name the name of the filter.
   */
  private function MapFilter($name) {
    $map = array(
      'negate'          => array('id'=>0, 'argc'=>0, 'type'=>IMG_FILTER_NEGATE),    
      'grayscale'       => array('id'=>1, 'argc'=>0, 'type'=>IMG_FILTER_GRAYSCALE),
      'brightness'      => array('id'=>2, 'argc'=>1, 'type'=>IMG_FILTER_BRIGHTNESS),
      'contrast'        => array('id'=>3, 'argc'=>1, 'type'=>IMG_FILTER_CONTRAST),
      'colorize'        => array('id'=>4, 'argc'=>4, 'type'=>IMG_FILTER_COLORIZE),
      'edgedetect'      => array('id'=>5, 'argc'=>0, 'type'=>IMG_FILTER_EDGEDETECT),
      'emboss'          => array('id'=>6, 'argc'=>0, 'type'=>IMG_FILTER_EMBOSS),
      'gaussian_blur'   => array('id'=>7, 'argc'=>0, 'type'=>IMG_FILTER_GAUSSIAN_BLUR),
      'selective_blur'  => array('id'=>8, 'argc'=>0, 'type'=>IMG_FILTER_SELECTIVE_BLUR),
      'mean_removal'    => array('id'=>9, 'argc'=>0, 'type'=>IMG_FILTER_MEAN_REMOVAL),
      'smooth'          => array('id'=>10, 'argc'=>1, 'type'=>IMG_FILTER_SMOOTH),
      'pixelate'        => array('id'=>11, 'argc'=>2, 'type'=>IMG_FILTER_PIXELATE),
    );
    if(isset($map[$name]))
      return $map[$name];
    else {
      $this->RaiseError('No such filter.');
    }
  }
  
  

  /**
   * Calculate new width and height of image.
   */
  protected function CalculateNewWidthAndHeight() {
    // Crop, use cropped width and height as base for calulations
    $this->Log("Calculate new width and height.");
    $this->Log("Original width x height is {$this->width} x {$this->height}.");
  
    if(isset($this->area)) {
      $this->offset['top']    = round($this->area['top'] / 100 * $this->height);
      $this->offset['right']  = round($this->area['right'] / 100 * $this->width);
      $this->offset['bottom'] = round($this->area['bottom'] / 100 * $this->height);
      $this->offset['left']   = round($this->area['left'] / 100 * $this->width);
      $this->offset['width']  = $this->width - $this->offset['left'] - $this->offset['right'];
      $this->offset['height'] = $this->height - $this->offset['top'] - $this->offset['bottom'];
      $this->width  = $this->offset['width'];
      $this->height = $this->offset['height'];
      $this->Log("The offset for the area to use is top {$this->area['top']}%, right {$this->area['right']}%, bottom {$this->area['bottom']}%, left {$this->area['left']}%.");
      $this->Log("The offset for the area to use is top {$this->offset['top']}px, right {$this->offset['right']}px, bottom {$this->offset['bottom']}px, left {$this->offset['left']}px, width {$this->offset['width']}px, height {$this->offset['height']}px.");
    }
  
    $width  = $this->width;
    $height = $this->height;

    if($this->crop) {
      $width  = $this->crop['width']  = $this->crop['width'] <= 0 ? $this->width + $this->crop['width'] : $this->crop['width'];
      $height = $this->crop['height'] = $this->crop['height'] <= 0 ? $this->height + $this->crop['height'] : $this->crop['height'];

      if($this->crop['start_x'] == 'left') {
        $this->crop['start_x'] = 0;
      } 
      elseif($this->crop['start_x'] == 'right') {
        $this->crop['start_x'] = $this->width - $width;
      } 
      elseif($this->crop['start_x'] == 'center') {
        $this->crop['start_x'] = round($this->width / 2) - round($width / 2);
      } 

      if($this->crop['start_y'] == 'top') {
        $this->crop['start_y'] = 0;
      }
      elseif($this->crop['start_y'] == 'bottom') {
        $this->crop['start_y'] = $this->height - $height;
      } 
      elseif($this->crop['start_y'] == 'center') {
        $this->crop['start_y'] = round($this->height / 2) - round($height / 2);
      } 

      $this->Log("Crop area is width {$width}px, height {$height}px, start_x {$this->crop['start_x']}px, start_y {$this->crop['start_y']}px.");

      /*if(empty($this->crop['width'])) {
        $this->crop['width'] = $this->width - $this->crop['start_x'];
      }
      if(empty($this->crop['height'])) {
        $this->crop['height'] = $this->height - $this->crop['start_y'];
      }*/
    }
  
    // Calculate new width and height if keeping aspect-ratio. 
    if($this->keepRatio) {
    
      // Crop-to-fit and both new width and height are set.
      if($this->cropToFit && isset($this->newWidth) && isset($this->newHeight)) {
        // Use newWidth and newHeigh as width/height, image should fit in box.
        ;
      } 
     
      // Both new width and height are set.
      elseif(isset($this->newWidth) && isset($this->newHeight)) {
        // Use newWidth and newHeigh as max width/height, image should not be larger.
        $ratioWidth  = $width  / $this->newWidth;
        $ratioHeight = $height / $this->newHeight;
        $ratio = ($ratioWidth > $ratioHeight) ? $ratioWidth : $ratioHeight;
        $this->newWidth  = round($width  / $ratio);
        $this->newHeight = round($height / $ratio);
      } 
     
      // Use new width as max-width
      elseif(isset($this->newWidth)) {
        $factor = (float)$this->newWidth / (float)$width;
        $this->newHeight = round($factor * $height);
      } 
    
      // Use new height as max-hight
      elseif(isset($this->newHeight)) {
        $factor = (float)$this->newHeight / (float)$height;
        $this->newWidth = round($factor * $width);
      }
      
      // Use newWidth and newHeigh as defined width/height, image should fit the area.
      if($this->cropToFit) {
        /*
        if($cropToFit && $newWidth && $newHeight) {
          $targetRatio = $newWidth / $newHeight;
          $cropWidth   = $targetRatio > $aspectRatio ? $width : round($height * $targetRatio);
          $cropHeight  = $targetRatio > $aspectRatio ? round($width  / $targetRatio) : $height;
          }
        */
        $ratioWidth  = $width  / $this->newWidth;
        $ratioHeight = $height / $this->newHeight;
        $ratio = ($ratioWidth < $ratioHeight) ? $ratioWidth : $ratioHeight;
        $this->cropWidth  = round($width  / $ratio);
        $this->cropHeight = round($height / $ratio);
      }      
    }
    
    // Crop, ensure to set new width and height
    if($this->crop) {
      $this->newWidth = round(isset($this->newWidth) ? $this->newWidth : $this->crop['width']);
      $this->newHeight = round(isset($this->newHeight) ? $this->newHeight : $this->crop['height']);    
    }

    // No new height or width is set, use existing measures.
    $this->newWidth  = round(isset($this->newWidth) ? $this->newWidth : $this->width);
    $this->newHeight = round(isset($this->newHeight) ? $this->newHeight : $this->height);   
    $this->Log("Calculated new width x height as {$this->newWidth} x {$this->newHeight}.");

    return $this;
  }



  /**
   * Set extension for filename to save as.
   * 
   */
  private function SetSaveAsExtension() {
    if($this->saveAs) {
      switch(strtolower($this->saveAs)) {
        case 'jpg':
          $this->extension = 'jpg';
          break;

        case 'png':
          $this->extension = 'png';
          break;

        case 'gif':
          $this->extension = 'gif';
          break;

        default:
          $this->extension = null;
      }
      $this->Log("Saving image as: " . $this->extension);
    }
    return $this;
  }



  /**
   * Use original image if possible.
   *
   */
  protected function UseOriginalIfPossible() {
    if($this->useOriginal && 
      ($this->newWidth == $this->width) && 
      ($this->newHeight == $this->height) && 
      !$this->quality && 
      !$this->area && 
      !$this->crop && 
      !$this->filters &&
      !$this->saveAs &&
      !$this->sharpen &&
      !$this->emboss &&
      !$this->blur &&
      !$this->palette) {
      $this->Log("Using original image.");
      $this->Output($this->pathToImage);
    }
  }


  
  /**
   * Use cached version of image, if possible
   * 
   */
  protected function UseCacheIfPossible() {
    if(is_readable($this->cacheFileName)) {
      $fileTime   = filemtime($this->pathToImage);
      $cacheTime  = filemtime($this->cacheFileName);
      if($fileTime <= $cacheTime) {
        if($this->useCache) {
          if($this->verbose) {
            $this->Log("Use cached file.");
            $this->Log("Cached image filesize: " . filesize($this->cacheFileName) . " bytes."); 
          }
          $this->Output($this->cacheFileName);
        }
        else {
          $this->Log("Cache is valid but ignoring it by intention.");
        }
      }
      else {
        $this->Log("Original file is modified, ignoring cache.");
      }
    }
    else {
      $this->Log("Cachefile does not exists.");
    }
    return $this;
  }


    
  /**
   * Sharpen image as http://php.net/manual/en/ref.image.php#56144
   * http://loriweb.pair.com/8udf-sharpen.html
   * 
   */
  protected function SharpenImage() {
    $matrix = array(
      array(-1,-1,-1,),
      array(-1,16,-1,),
      array(-1,-1,-1,)
    );
    $divisor = 8;
    $offset = 0;
    imageconvolution($this->image, $matrix, $divisor, $offset);
    return $this;
  }



  /**
   * Emboss image as http://loriweb.pair.com/8udf-emboss.html
   * 
   */
  protected function EmbossImage() {
    $matrix = array(
      array( 1, 1,-1,),
      array( 1, 3,-1,),
      array( 1,-1,-1,)
    );
    $divisor = 3;
    $offset = 0;
    imageconvolution($this->image, $matrix, $divisor, $offset);
    return $this;
  }



  /**
   * Blur image as http://loriweb.pair.com/8udf-basics.html
   * 
   */
  protected function BlurImage() {
    $matrix = array(
      array( 1, 1, 1,),
      array( 1,15, 1,),
      array( 1, 1, 1,)
    );
    $divisor = 23;
    $offset = 0;
    imageconvolution($this->image, $matrix, $divisor, $offset);
    return $this;
  }


  /**
   * Resize the image and optionally store/cache the new imagefile. Output the image.
   *
   * @param array $args used when processing image.
   */
  public function ResizeAndOutput($args) {
    $defaults = array(
      'newWidth'  => null,
      'newHeight' => null,
      'aspectRatio' => null,
      'keepRatio' => true,
      'area'      => null, //'0,0,0,0',
      'scale'     => null, 
      'cropToFit' => false,
      'quality'   => null,
      'deflate'   => null,
      'crop'      => null, //array('width'=>null, 'height'=>null, 'start_x'=>0, 'start_y'=>0), 
      'filters'   => null,
      'verbose'   => false,
      'useCache'  => true,
      'useOriginal' => true, 
      'saveAs'    => null,
      'sharpen'   => null,
      'emboss'    => null,
      'blur'      => null,
      'palette'   => null,
    );

    // Convert crop settins from string to array
    if(isset($args['crop']) && !is_array($args['crop'])) {
      $pices = explode(',', $args['crop']);
      $args['crop'] = array(
        'width'   => $pices[0],
        'height'  => $pices[1],
        'start_x' => $pices[2],
        'start_y' => $pices[3],
      );
    }
    
    // Convert area settins from string to array
    if(isset($args['area']) && !is_array($args['area'])) {
      $pices = explode(',', $args['area']);
      $args['area'] = array(
        'top'    => $pices[0],
        'right'  => $pices[1],
        'bottom' => $pices[2],
        'left'   => $pices[3],
      );
    }
    
    // Convert filter settins from array of string to array of array
    if(isset($args['filters']) && is_array($args['filters'])) {
      foreach($args['filters'] as $key => $filterStr) {
        $parts = explode(',', $filterStr);
        $filter = $this->MapFilter($parts[0]);
        $filter['str'] = $filterStr;
        for($i=1;$i<=$filter['argc'];$i++) {
          if(isset($parts[$i])) {
            $filter["arg{$i}"] = $parts[$i];
          } else {
            $this->RaiseError('Missing arg to filter, review how many arguments are needed at http://php.net/manual/en/function.imagefilter.php');           
          }
        }
        $args['filters'][$key] = $filter;
      }
    }
    
    // Quick solution when introducing parameter deflate, just map it to quality. Should re-engineer usage of quality and deflate.
    if(!isset($this->quality) && isset($this->deflate)) {
      $this->quality = $this->deflate;
    }

    // Merge default arguments with incoming and set properties.
    //$args = array_merge_recursive($defaults, $args);
    $args = array_merge($defaults, $args);
    foreach($defaults as $key=>$val) {
      $this->{$key} = $args[$key];
    }
    $this->Log("Resize and output image."); 
    
    // Init the object and do sanity checks on arguments
    $this->Init()
         ->CalculateNewWidthAndHeight()
         ->UseOriginalIfPossible();

    // Check cache before resizing.
    $this->SetSaveAsExtension()
         ->SetQuality()
         ->SetOptimization()
         ->CreateFilename()
         ->UseCacheIfPossible();
    
    // Resize and output
    $this->Open()
         ->Resize()
         ->SaveToCache()
         ->Output($this->cacheFileName);
  }
  


  /**
   * Convert true color image to palette image, keeping alpha.
   * http://stackoverflow.com/questions/5752514/how-to-convert-png-to-8-bit-png-using-php-gd-library
   */
  protected function TrueColorToPalette() {
    $img = imagecreatetruecolor($this->width, $this->height);
    $bga = imagecolorallocatealpha($img, 0, 0, 0, 127);
    imagecolortransparent($img, $bga);
    imagefill($img, 0, 0, $bga);
    imagecopy($img, $this->image, 0, 0, 0, 0, $this->width, $this->height);
    imagetruecolortopalette($img, false, 255);
    imagesavealpha($img, true);

    if(imageistruecolor($this->image)) {
      $this->Log("Matching colors with true color image.");
      imagecolormatch($this->image, $img);
    }

    $this->image = $img;
  }



  /**
   * Create a image and keep transparency for png and gifs.
   *
   * $param int $width of the new image.
   * @param int $height of the new image.
   * @returns image resource.
   */
  public function CreateImageKeepTransparency($width, $height) {
    $this->Log("Creating a new working image width={$width}px, height={$height}px.");
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
   * Resize and or crop the image.
   *
   */
  public function Resize() {

    $this->Log("Starting to Resize()");

    // Scale the original image before starting
    if(isset($this->scale)) {
      $this->Log("Scale by {$this->scale}%");
      $newWidth  = $this->width * $this->scale / 100;
      $newHeight = $this->height * $this->scale / 100;
      $img = $this->CreateImageKeepTransparency($newWidth, $newHeight);
      imagecopyresampled($img, $this->image, 0, 0, 0, 0, $newWidth, $newHeight, $this->width, $this->height);
      $this->image = $img;
      $this->width = $newWidth;
      $this->height = $newHeight;
    } 
    
    // Only use a specified area of the image, $this->offset is defining the area to use
    if(isset($this->offset)) {
      $this->Log("Offset for area to use, cropping it width={$this->offset['width']}, height={$this->offset['height']}, start_x={$this->offset['left']}, start_y={$this->offset['top']}");
      $img = $this->CreateImageKeepTransparency($this->offset['width'], $this->offset['height']);
      imagecopy($img, $this->image, 0, 0, $this->offset['left'], $this->offset['top'], $this->offset['width'], $this->offset['height']);
      $this->image = $img;
      $this->width = $this->offset['width'];
      $this->height = $this->offset['height'];
    } 
    
    // SaveAs need to copy image to remove transparency, if any
    if($this->saveAs) {
      $this->Log("Copying image before saving as another format, loosing transparency, width={$this->width}, height={$this->height}.");
      $img = imagecreatetruecolor($this->width, $this->height);
      $bg = imagecolorallocate($img, 255, 255, 255);
      imagefill($img, 0, 0, $bg);
      imagecopy($img, $this->image, 0, 0, 0, 0, $this->width, $this->height);
      $this->image = $img;
    }

    // Do as crop, take only part of image
    if($this->crop) {
      $this->Log("Cropping area width={$this->crop['width']}, height={$this->crop['height']}, start_x={$this->crop['start_x']}, start_y={$this->crop['start_y']}");
      $img = $this->CreateImageKeepTransparency($this->crop['width'], $this->crop['height']);
      imagecopyresampled($img, $this->image, 0, 0, $this->crop['start_x'], $this->crop['start_y'], $this->crop['width'], $this->crop['height'], $this->crop['width'], $this->crop['height']);
      $this->image = $img;
      $this->width = $this->crop['width'];
      $this->height = $this->crop['height'];
    } 
    
    // Resize by crop to fit
    if($this->cropToFit) {
      /*
      $cropX = round(($width - $cropWidth) / 2);  
      $cropY = round(($height - $cropHeight) / 2);    
      $imageResized = createImageKeepTransparency($newWidth, $newHeight);
      imagecopyresampled($imageResized, $image, 0, 0, $cropX, $cropY, $newWidth, $newHeight, $cropWidth, $cropHeight);
      $image = $imageResized;
      $width = $newWidth;
      $height = $newHeight;
      */
      $this->Log("Crop to fit");
      $cropX = round(($this->cropWidth/2) - ($this->newWidth/2));  
      $cropY = round(($this->cropHeight/2) - ($this->newHeight/2));  
      $imgPreCrop   = $this->CreateImageKeepTransparency($this->cropWidth, $this->cropHeight);
      $imageResized = $this->CreateImageKeepTransparency($this->newWidth, $this->newHeight);
      imagecopyresampled($imgPreCrop, $this->image, 0, 0, 0, 0, $this->cropWidth, $this->cropHeight, $this->width, $this->height);
      imagecopyresampled($imageResized, $imgPreCrop, 0, 0, $cropX, $cropY, $this->newWidth, $this->newHeight, $this->newWidth, $this->newHeight);
      $this->image = $imageResized;
      $this->width = $this->newWidth;
      $this->height = $this->newHeight;
    } 
    
    // Resize it
    else if(!($this->newWidth == $this->width && $this->newHeight == $this->height)) {
      $this->Log("Resizing, new height and/or width");
      $imageResized = $this->CreateImageKeepTransparency($this->newWidth, $this->newHeight);
      imagecopyresampled($imageResized, $this->image, 0, 0, 0, 0, $this->newWidth, $this->newHeight, $this->width, $this->height);
      //imagecopyresized($imageResized, $this->image, 0, 0, 0, 0, $this->newWidth, $this->newHeight, $this->width, $this->height);
      $this->image = $imageResized;
      $this->width = $this->newWidth;
      $this->height = $this->newHeight;
    }
    
    // Apply filters
    if(isset($this->filters) && is_array($this->filters)) {
      foreach($this->filters as $filter) {
        $this->Log("Applying filter $filter.");
        switch($filter['argc']) {
          case 0: imagefilter($this->image, $filter['type']); break;
          case 1: imagefilter($this->image, $filter['type'], $filter['arg1']); break;
          case 2: imagefilter($this->image, $filter['type'], $filter['arg1'], $filter['arg2']); break;
          case 3: imagefilter($this->image, $filter['type'], $filter['arg1'], $filter['arg2'], $filter['arg3']); break;
          case 4: imagefilter($this->image, $filter['type'], $filter['arg1'], $filter['arg2'], $filter['arg3'], $filter['arg4']); break;
        }
      }
    }

    // Convert to palette image
    if($this->palette) {
      $this->Log("Converting to palette image.");
      $this->TrueColorToPalette();
    }

    // Blur the image
    if($this->blur) {
      $this->Log("Blur.");
      $this->BlurImage();
    }

    // Emboss the image
    if($this->emboss) {
      $this->Log("Emboss.");
      $this->EmbossImage();
    }

    // Sharpen the image
    if($this->sharpen) {
      $this->Log("Sharpen.");
      $this->SharpenImage();
    }

    return $this;
  }



  /**
   * Save image to cache
   *
   */
  protected function SaveToCache() {
    switch($this->extension) {
      case 'jpeg':  
      case 'jpg':  
        if($this->saveFolder) {
          $this->Log("Saving image as JPEG to cache using quality = {$this->quality}.");
          imagejpeg($this->image, $this->cacheFileName, $this->quality);
          
          // Use JPEG optimize if defined
          if($this->jpegOptimize) {
            if($this->verbose) { clearstatcache(); $this->Log("Filesize before optimize: " . filesize($this->cacheFileName) . " bytes."); }
            $res = array();
            $cmd = $this->jpegOptimize . " -outfile $this->cacheFileName $this->cacheFileName";
            exec($cmd, $res);
            $this->Log($cmd);
            $this->Log($res);
          }
        }
        break;  
  
      case 'gif':  
        if($this->saveFolder) {
          $this->Log("Saving image as GIF to cache.");
          imagegif($this->image, $this->cacheFileName);  
        }
        break;  
    
      case 'png':  
        if($this->saveFolder) {
          $this->Log("Saving image as PNG to cache using quality = {$this->quality}.");

          // Turn off alpha blending and set alpha flag
          imagealphablending($this->image, false);
          imagesavealpha($this->image, true);

          imagepng($this->image, $this->cacheFileName, $this->quality);  
          
          // Use external program to filter PNG, if defined
          if($this->pngFilter) {
            if($this->verbose) { clearstatcache(); $this->Log("Filesize before filter optimize: " . filesize($this->cacheFileName) . " bytes."); }
            $res = array();
            $cmd = $this->pngFilter . " $this->cacheFileName";
            exec($cmd, $res);
            $this->Log($cmd);
            $this->Log($res);
          }

          // Use external program to deflate PNG, if defined
          if($this->pngDeflate) {
            if($this->verbose) { clearstatcache(); $this->Log("Filesize before deflate optimize: " . filesize($this->cacheFileName) . " bytes."); }
            $res = array();
            $cmd = $this->pngDeflate . " $this->cacheFileName";
            exec($cmd, $res);
            $this->Log($cmd);
            $this->Log($res);
          }
        }
        break;  

      default:
        $this->RaiseError('No support for this file extension.');
        break;
    }  

    if($this->verbose) {
      clearstatcache();
      $this->Log("Cached image filesize: " . filesize($this->cacheFileName) . " bytes."); 
      $this->Log("imageistruecolor() : " . (imageistruecolor($this->image) ? 'true' : 'false'));
      $this->Log("imagecolorstotal() : " . imagecolorstotal($this->image));
      $this->Log("Number of colors in image = " . $this->ColorsTotal($this->image));
    }

    return $this;
  }
  
  
}  
