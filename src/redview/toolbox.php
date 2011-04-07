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
   * Array of loaded module objects, keyed by class name.
   * 
   * @var array<string, Redview_Mod>
   */
  public $mod;

  public function __construct () {
  
    $this->cache  = new RedView_Cache();
    $this->parser = new RedView_Parser();
    $this->router = new RedView_Router();
    
    $this->cache->tools  = $this;
    $this->parser->tools = $this;
    $this->router->tools = $this;
    
  }
  
}


