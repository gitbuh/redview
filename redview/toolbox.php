<?php

/**
    RedView Toolbox. Used by other classes to access important things.
*/
class RedView_Toolbox {

  public $parser;
  public $router;
  public $crypto;
  public $misc;

  public function __construct ($parser=null, $router=null, $crypto=null, $settings=null) {
  
    $this->parser = new RedView_Parser();
    $this->router = new RedView_Router();
    $this->crypto = new RedView_Crypto();
    $this->misc = new RedView_Settings();
    
    if (!file_exists('app.ini')) return;
    
    $ini = parse_ini_file('app.ini', true);
    
    foreach ($ini as $k=>$v) if (@$this->$k) foreach ($v as $prop=>$val) {
      if (is_array($this->$k)) $this->$k[$prop] = $val; 
      else $this->$k->$prop = $val;
    }
    
  }
  
}


