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
   * Properties
   */
  private $image = null; // Object for open image
  public $pathToImage;
  private $fileExtension;
  public $newWidth;
  public $newHeight;
  private $cropWidth;
  private $cropHeight;
  public $keepRatio;
  public $crop;
  public $saveFolder;
  public $newName;
  private $newFileName;
  private $mime; // Calculated from source image
  private $width; // Calculated from source image
  private $height; // Calculated from source image
  private $type; // Calculated from source image
  private $attr; // Calculated from source image
  private $validExtensions = array('jpg', 'jpeg', 'png', 'gif');


  /**
   * Constructor, can take arguments to init the object.
   *
   * @param $pathToImage string the filepath to the image.
   * @param $newWidth integer the new width or null.
   * @param $newHeight integer the new width or null.
   * @param $keepRatio boolean true to keep aspect ratio else false.
   * @param $saveFolder string path to folder where to save the new file or null to skip saving.
   * @param $newName string new filename or leave to null to autogenerate filename.
   */
  public function __construct($pathToImage=null, $newWidth=null, $newHeight=null, 
                              $keepRatio=true, $crop=false, $saveFolder=null, $newName=null) {
    $this->pathToImage = $pathToImage;
    $this->fileExtension = pathinfo($this->pathToImage, PATHINFO_EXTENSION);
    $this->newWidth = $newWidth;
    $this->newHeight = $newHeight;
    $this->keepRatio = $keepRatio;
    $this->crop = $crop;
    $this->saveFolder = $saveFolder;
    $this->newName = $newName;
  }


  /**
   * Raise error, enables to implement a selection of error methods.
   *
   * @param $message string the error message to display.
   */
  public function RaiseError($message) {
    throw new Exception($message);
  }
  
  
  /**
   * Create filename to save file in cache.
   */
  public function CreateFilename() {
    $parts = pathinfo($this->pathToImage);
    $crop = $this->crop ? '_c_' : null;
    return $this->saveFolder . '/' . $parts['filename'] . '_' . round($this->newWidth) . '_' . round($this->newHeight) . $crop . '.' . $parts['extension'];
  }
  
  
  /**
   * Init and do some sanity checks before any processing is done. Throws exception if not valid.
   */
  public function Init() {
    is_null($this->newWidth) or is_numeric($this->newWidth) or $this->RaiseError('Width not numeric');
    is_null($this->newHeight) or is_numeric($this->newHeight) or $this->RaiseError('Height not numeric');
    is_readable($this->pathToImage) or $this->RaiseError('File does not exist.');
    in_array($this->fileExtension, $this->validExtensions) or $this->RaiseError('Not a valid file extension.');
    is_null($this->saveFolder) or is_writable($this->saveFolder) or $this->RaiseError('Save directory does not exist or is not writable.');

    // Get details on image
    $info = list($this->width, $this->height, $this->type, $this->attr) = getimagesize($this->pathToImage);
    !empty($info) or $this->RaiseError("The file doesn't seem to be an image.");
    $this->mime = $info['mime'];

    return $this;
  }
  

  /**
   * Output image using caching.
   *
   */
  protected function Output($file) {
    $time = filemtime($file);  
    if(isset($_SERVER['If-Modified-Since']) && strtotime($_SERVER['If-Modified-Since']) >= $time){  
      header("HTTP/1.0 304 Not Modified");
    } else {  
      header('Content-type: ' . $this->mime);  
      header('Last-Modified: ' . gmdate("D, d M Y H:i:s",$time) . " GMT");  
      readfile($file);
    }
    exit;
  }
  
 
  /**
   * Open image.
   *
   */
  protected function Open() {
    switch($this->fileExtension) {  
      case 'jpg':
      case 'jpeg': $this->image = @imagecreatefromjpeg($this->pathToImage); break;  
      case 'gif':  $this->image = @imagecreatefromgif($this->pathToImage); break;  
      case 'png':  $this->image = @imagecreatefrompng($this->pathToImage); break;  
      default: $this->image = false; $this->RaiseError('No support for this file extension.');
    }
    return $this;
  }
  
  
  /**
   * Calculate new width and height of image.
   */
  protected function CalculateNewWidthAndHeight() {
    // Only calculate new width and height if keeping aspect-ratio. 
    if($this->keepRatio) {

      // Both new width and height are set.
      if(isset($this->newWidth) && isset($this->newHeight)) {

        // Use newWidth and newHeigh as min width/height, image should fit the area.
        if($this->crop) {
          $ratioWidth = $this->width/$this->newWidth;
          $ratioHeight = $this->height/$this->newHeight;
          $ratio = ($ratioWidth < $ratioHeight) ? $ratioWidth : $ratioHeight;
          $this->cropWidth = $this->width / $ratio;
          $this->cropHeight = $this->height / $ratio;
        }      
      
        // Use newWidth and newHeigh as max width/height, image should not be larger.
        else {
          $ratioWidth = $this->width/$this->newWidth;
          $ratioHeight = $this->height/$this->newHeight;
          $ratio = ($ratioWidth > $ratioHeight) ? $ratioWidth : $ratioHeight;
          $this->newWidth = $this->width / $ratio;
          $this->newHeight = $this->height / $ratio;
        }
      } 
     
      // Use new width as max-width
      elseif(isset($this->newWidth)) {
        $factor = (float)$this->newWidth / (float)$this->width;
        $this->newHeight = $factor * $this->height;
      } 
    
      // Use new height as max-hight
      elseif(isset($this->newHeight)) {
        $factor = (float)$this->newHeight / (float)$this->height;
        $this->newWidth = $factor * $this->width;
      } 
    }
    
    // Do not keep aspect ratio, but both newWidth and newHeight must be set
    else {
      $this->newWidth = isset($this->newWidth) ? $this->newWidth : $this->width;
      $this->newHeight = isset($this->newHeight) ? $this->newHeight : $this->height;    
    }    
    return $this;
  }
  
  
  /**
   * Resize the image and optionally store/cache the new imagefile. Output the image.
   */
  public function ResizeAndOutput() {
    $this->Init()->CalculateNewWidthAndHeight();

    // Use original image?
    if(is_null($this->newWidth) && is_null($this->newHeight)) {
      $this->Output($this->pathToImage);
    }
    
    //echo "{$this->newWidth}:{$this->newHeight}";
    
    // Check cache before resizing.
    $this->newFileName = $this->CreateFilename();
    if(is_readable($this->newFileName)) {
      $fileTime = filemtime($this->pathToImage);
      $cacheTime = filemtime($this->newFileName);    
      if($fileTime <= $cacheTime) {
        $this->Output($this->newFileName);
      }
    }
    
    // Resize and output    
    $this->Open()->ResizeAndSave();
  }
  

  /**
   * Resize, crop and output the image.
   *
   * @param $imageQuality number the quality to use when saving the file, default is full quality.
   */
  public function ResizeAndSave($imageQuality="100") {
    if($this->crop) {
      $cropX = ($this->cropWidth/2) - ($this->newWidth/2);  
      $cropY = ($this->cropHeight/2) - ($this->newHeight/2);  
      $imgPreCrop = imagecreatetruecolor($this->cropWidth, $this->cropHeight);
      $imageResized = imagecreatetruecolor($this->newWidth, $this->newHeight);
      imagecopyresampled($imgPreCrop, $this->image, 0, 0, 0, 0, $this->cropWidth, $this->cropHeight, $this->width, $this->height);
      imagecopyresampled($imageResized, $imgPreCrop, 0, 0, $cropX, $cropY, $this->newWidth, $this->newHeight, $this->newWidth, $this->newHeight);
    } else {
      $imageResized = imagecreatetruecolor($this->newWidth, $this->newHeight);
      imagecopyresampled($imageResized, $this->image, 0, 0, 0, 0, $this->newWidth, $this->newHeight, $this->width, $this->height);
    }
      
    switch($this->fileExtension)  
    {  
      case 'jpg':  
      case 'jpeg':  
        if(imagetypes() & IMG_JPG) {
          if($this->saveFolder) {
            imagejpeg($imageResized, $this->newFileName, $imageQuality);
          }
          $imgFunction = 'imagejpeg';
        }  
      break;  
  
      case 'gif':  
        if (imagetypes() & IMG_GIF) {  
          if($this->saveFolder) {
            imagegif($imageResized, $this->newFileName);  
          }
          $imgFunction = 'imagegif';
        }  
      break;  
    
      case 'png':  
        // Scale quality from 0-100 to 0-9 and invert setting as 0 is best, not 9  
        $quality = 9 - round(($imageQuality/100) * 9);  
        if (imagetypes() & IMG_PNG) {  
          if($this->saveFolder) {
            imagepng($imageResized, $this->newFileName, $quality);  
          }
          $imgFunction = 'imagepng';
        }  
      break;  
      default:
        $this->RaiseError('No support for this file extension.');
      break;
    }  
    header('Content-type: ' . $this->mime);
    header('Last-Modified: ' . gmdate("D, d M Y H:i:s",time()) . " GMT");
    $imgFunction($imageResized);
    exit;
  }
  
  
}  
