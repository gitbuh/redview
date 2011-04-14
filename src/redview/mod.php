<?php
/**
 *
 * Plugable module base class.
 *
 */
class RedView_Mod extends RedView_Base {
  
  public function __construct ($options=array(), RedView_Toolbox $tools=null) {
    $this->setup($options, $tools);
  }
  
  /**
   * Set up the plugin.
   * 
   * @param array $options
   *   		optional options
   *   
   * @param RedView_Toolbox $tools 
   * 		optional custom tools
   */
  public function setup ($options=array(), RedView_Toolbox $tools=null) {
    parent::setup($options, $tools);
    $this->tools->mods->set(get_class($this), $this);
  }
  
  /**
   * Initialize prerequisite plugin if needed
   * 
   * @param string $class
   */
  public function depends ($class) {
    if (!$this->tools->mods->get($class)) {
      $obj = new $class();
      $obj->setup($this->options, $this->tools);
    }
  }
  
}

