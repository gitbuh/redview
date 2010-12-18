<?php

/**
    RedView Toolbox. Used by other classes to access important things.
*/
class RedView_Toolbox {

  public $events = 'RedView_Event';
  
  public $parser;
  public $router;
  public $crypto;
  public $action;
  public $misc;

  public function __construct () {
  
    $this->parser = new RedView_Parser();
    $this->router = new RedView_Router();
    $this->crypto = new RedView_Crypto();
    $this->action = new RedView_Action();
    $this->misc = new RedView_Settings();
    
    $this->router->tools = $this;
    $this->parser->tools = $this;
    
    
    if (!file_exists('app.ini')) return;
    
    $ini = parse_ini_file('app.ini', true);
    
    foreach ($ini as $k=>$v) if (@$this->$k) foreach ($v as $prop=>$val) {
      if (is_array($this->$k)) $this->$k[$prop] = $val; 
      else $this->$k->$prop = $val;
    }
    
  }
  
}


