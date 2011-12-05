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
  public $maxSize = 800;
  
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
      $hiddenAttributes = array('src', 'clamp', 'desaturate', 'mask', 'overlay', 'topleft', 'bottomright');
      if (!$settings->clamp) {
        $hiddenAttributes[]='width';
        $hiddenAttributes[]='height'; 
      }
      if (!in_array($attrib->name, $hiddenAttributes)) {
        $img->setAttribute($attrib->name, $attrib->value);
      }
    }
    
    $x = str_replace('-&gt;', '->', $doc->saveXHTML($img));

    $pi = $doc->createProcessingInstruction('php', " echo <<<RV_THUMB\n$x\nRV_THUMB;\n");
    
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
    
    $adj_width = $def_width;
    $adj_height = $def_height;
    
    // Scale to fit w/ Aspect Ratio
    if($fitToScale) {
      if ($def_height > $def_width) {
        $adj_height = $height / ($width / $def_width);
      }
      else {
        $adj_width = $width / ($height / $def_height);
      }
    }
    
    if ($adj_width > $def_width) {
    	$scale = $def_width / $adj_width;
    	$adj_width = $def_width;
    	$adj_height *= $scale;
    }
    if ($adj_height > $def_height) {
    	$scale = $def_height / $adj_height;
    	$adj_height = $def_height;
    	$adj_width *= $scale;
    }
    

    $factor_w = $width / $adj_width;
    $factor_h = $height / $adj_height;

    
    if ($factor_w > $factor_h) {
      $new_height = floor($adj_height * $factor_h);
      $new_width = floor($adj_width  * $factor_h);
    } else {
      $new_height = floor($adj_height * $factor_w);
      $new_width = floor($adj_width  * $factor_w);
    }
    

    if ((!$clamp[0]) && $clamp[0]!=='0') $clamp[0] = 50;
    if ((!$clamp[1]) && $clamp[1]!=='0') $clamp[1] = 50;

    $src_x = ceil(($width  - $new_width)  * ($clamp[0] / 100));
    $src_y = ceil(($height - $new_height) * ($clamp[1] / 100));

    $dst = ImageCreateTrueColor($adj_width, $adj_height);
    
    imagesavealpha($dst, true);
    $trans_colour = imagecolorallocatealpha($dst, 0, 0, 0, 127);
    imagefill($dst, 0, 0, $trans_colour);
    
    @imagecopyresampled($dst, $src, 0, 0, $src_x, $src_y,
        $adj_width, $adj_height, $new_width, $new_height);

    if ($desaturate) {
      imagefilter($dst, IMG_FILTER_GRAYSCALE); 
    }
    
    if ($settings->mask) {
      $mask = imagecreatefrompng($_SERVER['DOCUMENT_ROOT'].'/'.$settings->mask);
      $this->imagealphamask($dst, $mask, $settings);
      $make_png = true;
    }
    
    if ($settings->overlay) {
      $overlay = imagecreatefrompng($_SERVER['DOCUMENT_ROOT'].'/'.$settings->overlay);
      $this->imageoverlay($dst, $overlay, $settings);
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
  
  
  protected function imagealphamask( &$picture, $mask, $settings ) {
    // Get sizes and set up new picture
    $xSize = imagesx( $picture );
    $ySize = imagesy( $picture );
    $newPicture = imagecreatetruecolor( $xSize, $ySize );
    imagesavealpha( $newPicture, true );
    imagefill( $newPicture, 0, 0, imagecolorallocatealpha( $newPicture, 0, 0, 0, 127 ) );
    
    // Resize mask
    $tempPic = imagecreatetruecolor( $xSize, $ySize );
    $this->imageoverlay($tempPic, $mask, $settings);
    imagedestroy($mask);
    $mask = $tempPic;

    // Perform pixel-based alpha map application
    for( $x = 0; $x < $xSize; $x++ ) {
      for( $y = 0; $y < $ySize; $y++ ) {
        $alpha = imagecolorsforindex( $mask, imagecolorat( $mask, $x, $y ) );
        $alpha = 127 - floor( $alpha[ 'red' ] / 2 );
        $color = imagecolorsforindex( $picture, imagecolorat( $picture, $x, $y ) );
        imagesetpixel( $newPicture, $x, $y, imagecolorallocatealpha( $newPicture, $color[ 'red' ], $color[ 'green' ], $color[ 'blue' ], $alpha ) );
      }
    }
    
    // Copy back to original picture
    imagedestroy( $picture );
    $picture = $newPicture;
  }
  
  
  protected function imageoverlay(&$background, $overlay, $settings) {
   
    // background image: width, height
    $iw = imagesx($background); 
    $ih = imagesy($background); 
    
    // overlay: width, height
    $ow = imagesx($overlay); 
    $oh = imagesy($overlay); 
   
   
    if ($settings->topLeft && !$settings->bottomRight)  {
      $settings->bottomRight = $settings->topLeft;
    }
    if ($settings->bottomRight && !$settings->topLeft)  {
      $settings->topLeft = $settings->bottomRight;
    }
    if (!$settings->topLeft)  {
      $w = round($ow/3);
      $w2 = $iw / 2 | 0;
      $h = round($oh/3);
      $h2 = $ih / 2 | 0;
      $settings->topLeft = ($w > $w2 ? $w2 : $w).'x'.($h > $h2 ? $h2 : $h);
      $settings->bottomRight = $settings->topLeft;
    }
    
    // corners: left width, top height
    list ($lw, $th) = isset($settings->topLeft) ? 
        explode("x", $settings->topLeft) : array(0, 0);
    // corners: right width, bottom height
    list ($rw, $bh) = isset($settings->bottomRight) ? 
        explode("x", $settings->bottomRight) : array(0, 0);
    
    // sides: top (and bottom) width
    $tw = $ow - $lw - $rw;
    // sides: bg image top (and bottom) width
    $itw = $iw - $lw - $rw;
    // sides: left (and right) height
    $lh = $oh - $th - $bh;
    // sides: bg image left (and right) height
    $ilh = $ih - $th - $bh;
    
    // Turn on alpha blending 
    imagealphablending($background, true); 
    
    // top left
    imagecopy($background, $overlay, 0, 0, 0, 0, $lw, $th); 
        
    // top right
    imagecopy($background, $overlay, 
        $iw - $rw, 0, $ow - $rw, 0, $rw, $th); 
        
    // bottom left
    imagecopy($background, $overlay, 
        0, $ih - $bh, 0, $oh - $bh, $lw, $bh); 
        
    // bottom right
    imagecopy($background, $overlay, 
        $iw - $rw, $ih - $bh, $ow - $rw, $oh - $bh, $rw, $bh); 
        
    // top side
    imagecopyresampled($background, $overlay, 
        $lw, 0, $lw, 0, $itw, $th, $tw, $th); 
        
    // bottom side
    imagecopyresampled($background, $overlay, 
        $lw, $ih - $bh, $lw, $oh - $bh, $itw, $bh, $tw, $bh); 
        
    // left side
    imagecopyresampled($background, $overlay, 
        0, $th, 0, $th, $lw, $ilh, $lw, $lh); 
        
    // right side
    imagecopyresampled($background, $overlay, 
        $iw - $rw, $th, $ow - $rw, $th, $rw, $ilh, $rw, $lh); 
        
    // inner
    imagecopyresampled($background, $overlay, 
        $rw, $th, $rw, $th, 
        $itw, $ilh, $tw, $lh); 
  }
  
}
