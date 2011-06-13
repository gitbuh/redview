<?php

/**
 * RedView Toolbox. Used by other classes to access important things.
 */
class RedView_Toolbox {

  /**
   * @var RedView_Core_Cache
   */
  public $cache;
  
  /**
   * @var RedView_Core_Mod
   */
  public $mods;
  
  /**
   * @var RedView_Core_Events
   */
  public $events;
  
  /**
   * @var RedView_Core_Parser
   */
  public $parser;
  
  /**
   * @var RedView_Core_Router
   */
  public $router;
  
  /**
   * @var RedView_Core_Xml
   */
  public $xml;
  
  

  /**
   * Toolbox constructor
   * 
   * @param RedView_Options $options
   */
  public function __construct (RedView_Options $options=null) {
  
    $this->cache  = new RedView_Core_Cache();
    $this->mods   = new RedView_Core_Mods();
    $this->events = new RedView_Core_Events();
    $this->parser = new RedView_Core_Parser();
    $this->router = new RedView_Core_Router();
    $this->xml    = new RedView_Core_Xml();
    
    $this->cache->setup($options, $this);
    $this->mods->setup($options, $this);
    $this->events->setup($options, $this);
    $this->parser->setup($options, $this);
    $this->router->setup($options, $this);
    $this->xml->setup($options, $this);
    
  }
  
}


