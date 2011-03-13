<?php

/**
 * RedView Toolbox. Used by other classes to access important things.
 */
class RedView_Toolbox {

  /**
   * @var string
   */
  public $events = 'RedView_Event';
  
  /**
   * @var RedView_Parser
   */
  public $parser;
  /**
   * @var RedView_Router
   */
  public $router;
  /**
   * @var RedView_Cache
   */
  public $cache;
  /**
   * @var RedView_Crypto
   */
  public $crypto;
  /**
   * @var RedView_Action
   */
  public $action;
  /**
   * @var RedView_Settings
   */
  public $misc;

  public function __construct () {
  
    $this->cache = new RedView_Cache();
    $this->parser = new RedView_Parser();
    $this->router = new RedView_Router();
    $this->crypto = new RedView_Crypto();
    $this->action = new RedView_Action();
    $this->misc = new RedView_Settings();
    
    $this->cache->tools = $this;
    $this->parser->tools = $this;
    $this->router->tools = $this;
    
    if (!file_exists('app.ini')) return;
    
    $ini = parse_ini_file('app.ini', true);
    
    foreach ($ini as $k=>$v) if (@$this->$k) foreach ($v as $prop=>$val) {
      if (is_array($this->$k)) {
        $this->$k[$prop] = $val; 
      }
      else {
        $this->$k->$prop = $val;
      }
    }
    
  }
  
}


