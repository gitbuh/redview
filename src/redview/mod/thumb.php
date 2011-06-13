<?php
/**
 *
 * Image thumbnail plugin.
 *
 */

class RedView_Mod_Thumb extends RedView_Mod {
  
  /**
   * Thumbnail path
   * 
   * @var string
   */
  public $thumbPath = 'thumb';
  
  /**
   * Maximum image width/height
   * 
   * @var int
   */
  public $maxSize = 400;
  
  /**
   * Initialize the plugin.
   *
   * @param RedView_Options $options
   *      optional options
   *
   * @param RedView_Toolbox $tools
   *    optional custom tools
   */
  public function setup (RedView_Options $options=null, RedView_Toolbox $tools=null) {

    parent::setup($options, $tools);

    $this->listen('parseNode');
    $this->listen('onRequest');

  }
  
  /**
   * parseNode
   *
   * Event handler for parseNode events.
   *
   * @param RedView_Event $event
   * 		Event object
   */
  public function parseNode(RedView_Event $event) {

    $parser   = $event->sender;
    $node     = $parser->currentNode;
    $doc     = $parser->currentDocument;
    
    if ($node->nodeName != 'r:thumb') return;
    
    $settings = new RedView_Mod_Thumb_Settings($node);
    
    $img = $doc->createElement('img');
  
    $img->setAttribute('src', $settings->toPath($this->thumbPath));
    
    
    foreach ($node->attributes as $attrib) {
      $hiddenAttributes = array('src', 'clamp', 'desaturate');
      if (!$settings->clamp) {
        $hiddenAttributes[]='width';
        $hiddenAttributes[]='height'; 
      }
      if (!in_array($attrib->name, $hiddenAttributes)) {
        $img->setAttribute($attrib->name, $attrib->value);
      }
    }
    
    $x = str_replace('-&gt;', '->', $doc->saveXHTML($img));

    $pi = $doc->createProcessingInstruction('php', " echo <<<RV_CACHE\n$x\nRV_CACHE;\n");
    
    $node->parentNode->replaceChild($pi, $node);
  
    // cancel the event, the current node has been destroyed.
    $event->cancel();
  }
  
  /**
   * onRequest
   *
   * Event handler for onRequest events.
   *
   * @param RedView_Event $event
   *    Event object
   */
  public function onRequest(RedView_Event $event) {
    $router = $event->sender;
    
    if (strpos($router->requestUrl, "{$this->thumbPath}/")!==0) return;
    
    $settings = new RedView_Mod_Thumb_Settings($this->thumbPath, $router->requestUrl);
    
    $upfile = $_SERVER['DOCUMENT_ROOT'] .'/'. $settings->source;
    $def_width  = isset ($settings->width) ? $settings->width : 0;
    $def_height = isset ($settings->height) ? $settings->height : 0;  
    $desaturate = isset ($settings->desaturate) && $settings->desaturate;
    $fitToScale = !$settings->clamp;

    if ($def_width > $this->maxSize) $def_width = $this->maxSize;
    if ($def_height > $this->maxSize) $def_height = $this->maxSize;
    if ($def_width < 0) $def_width = 0;
    if ($def_height < 0) $def_height = 0;

    $clamp = isset($settings->clamp) ? explode("x",$settings->clamp) : null;
    $make_png = substr($settings->source,-4) == ".png";
     
    // image path
    if (!file_exists($upfile)) die();

    if (!($def_width && $def_height)) {
      $def_width = '100';
      $def_height = '100';
    }

    // name of cached file
    $thumb_file = "{$_SERVER['DOCUMENT_ROOT']}/{$router->requestUrl}";
     
    $ext = strtolower(substr($upfile, -4));

    ini_set('memory_limit', '64M');

    if ($ext==".gif") {
      $src = @imagecreatefromgif($upfile);
    }
    elseif (($ext==".jpg") || ($ext=="jpeg")) {
      $src = @imagecreatefromjpeg($upfile);
    }
    elseif ($ext==".png") {
      $src = @imagecreatefrompng($upfile);
    }
     
    $size = getimagesize($upfile);
    $width = $size[0];
    $height = $size[1];

    if (!$def_width) {
      $factor_h = $height / $def_height;
      $def_width = $width / $factor_h;
    }
    if (!$def_height) {
      $factor_w = $width / $def_width;
      $def_height = $height / $factor_w;
    }
    
    // Scale to fit w/ Aspect Ratio
    if($fitToScale) {
      if ($def_height > $def_width) {
        $def_height = $height / ($width / $def_width);
      }
      else {
        $def_width = $width / ($height / $def_height);
      }
    }

    $factor_w = $width / $def_width;
    $factor_h = $height / $def_height;

    if ($factor_w > $factor_h) {
      $new_height = floor($def_height * $factor_h);
      $new_width = floor($def_width  * $factor_h);
    } else {
      $new_height = floor($def_height * $factor_w);
      $new_width = floor($def_width  * $factor_w);
    }
    

    if ((!$clamp[0]) && $clamp[0]!=='0') $clamp[0] = 50;
    if ((!$clamp[1]) && $clamp[1]!=='0') $clamp[1] = 50;

    $src_x = ceil(($width  - $new_width)  * ($clamp[0] / 100));
    $src_y = ceil(($height - $new_height) * ($clamp[1] / 100));

    $dst = ImageCreateTrueColor($def_width, $def_height);
    
    imagesavealpha($dst, true);
    $trans_colour = imagecolorallocatealpha($dst, 0, 0, 0, 127);
    imagefill($dst, 0, 0, $trans_colour);
    
    @imagecopyresampled($dst, $src, 0, 0, $src_x, $src_y,
        $def_width, $def_height, $new_width, $new_height);

    if ($desaturate) {
      imagefilter($dst, IMG_FILTER_GRAYSCALE); 
    }
    
    $dir = substr($thumb_file, 0, strrpos($thumb_file, '/'));
    
    $old = umask(0);
    mkdir($dir, 0755, true);
    umask($old);
    
    
    Header("Content-type: image/".($make_png ? 'png' : 'jpeg'));

    if ($make_png) {
      
      imagepng($dst);
      imagepng($dst, $thumb_file);
      
    } else {
      
      imagejpeg($dst, null, 95);
      imagejpeg($dst, $thumb_file, 95);
    }

    @imagedestroy($src);
    @imagedestroy($dst);

    exit();
    
  }
  
}
