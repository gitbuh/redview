<?php

class RedView_Mod_Thumb_Settings {
  
  public $width;
  
  public $height;
  
  public $clamp;
  
  public $desaturate;
  
  public $source;
  
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
      }
    }
    
  }

  public function toPath ($thumbPath) {
    return  "/$thumbPath/".
            'w_' . $this->width . '-h_' . $this->height .
            ($this->clamp ? '-c_' . $this->clamp : '') .
            ($this->desaturate ? '-d_' . $this->desaturate : '') . 
            // TODO: put slash here?
            $this->source;
  }
  
}