<?php

class RedView_Mod_Thumb_Settings {
  
  public $width;
  
  public $height;
  
  public $clamp;
  
  public $desaturate;
  
  public $source;
  
  public $mask;
  
  public $topLeft;
  
  public $bottomRight;
  
  public $tile;
  
  public function __construct($p1, $p2=null) {
    if ($p2) $this->fromPath($p1, $p2);
    else $this->fromNode($p1);
  }
  
  public function fromNode (DOMNode $node) {
    $this->width = $node->getAttribute('width');
    $this->height = $node->getAttribute('height');
    $this->clamp = $node->getAttribute('clamp');
    $this->desaturate = $node->getAttribute('desaturate');
    $this->source = $node->getAttribute('src');
    $this->mask = $node->getAttribute('mask');
    $this->overlay = $node->getAttribute('overlay');
    $this->topLeft = $node->getAttribute('topleft');
    $this->bottomRight = $node->getAttribute('bottomright');
    $this->tile = $node->getAttribute('tile');
  }
  
  public function fromPath ($thumbPath, $path) {
    $l = strlen($thumbPath)+1;
    $path = substr($path, $l);
    $p = strpos($path, '/');
    
    $opts = substr($path, 0, $p);
    $this->source = substr($path, $p);
    $opts = explode('-', $opts);
    
    foreach ($opts as $opt) {
      list ($k, $v) = explode('_', $opt);
      switch ($k) {
        case 'w': 
          $this->width = $v;
          break;
        case 'h': 
          $this->height = $v;
          break;
        case 'c': 
          $this->clamp = $v;
          break;
        case 'd': 
          $this->desaturate = $v;
          break;
        case 'm': 
          $this->mask = str_replace('|', '/', $v);
          break;
        case 'o': 
          $this->overlay = str_replace('|', '/', $v);
          break;
        case 'tl': 
          $this->topLeft = $v;
          break;
        case 'br': 
          $this->bottomRight = $v;
          break;
        case 't': 
          $this->tile = $v;
          break;
      }
    }
    
  }

  public function toPath ($thumbPath) {
    return  "/$thumbPath/".
        'w_' . $this->width . '-h_' . $this->height .
        ($this->clamp ? '-c_' . $this->clamp : '') .
        ($this->desaturate ? '-d_' . $this->desaturate : '') . 
        ($this->mask ? '-m_' . str_replace('/', '|', $this->mask) : '') . 
        ($this->overlay ? '-o_' . str_replace('/', '|', $this->overlay) : '') . 
        ($this->topLeft ? '-tl_' . $this->topLeft : '') .
        ($this->bottomRight ? '-br_' . $this->bottomRight : '') .
        ($this->tile ? '-t_' . $this->tile : '') .
        // TODO: put slash here?
        $this->source;
  }
  
}
