<?php
/**
 *
 * Plugable module base class.
 *
 */
class RedView_Mod extends RedView_Base {

  /**
   * @var array $options
   */
  public $options = array();
  
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
    if (!$options) $options = array();
    $this->applyOptions($options);
    $this->tools = $tools ? $tools : RedView::$tools;
    $this->tools->mod[get_class($this)] = $this;
  }
  
  /**
   * Initialize prerequisite plugin if needed
   * 
   * @param unknown_type $class
   */
  public function depends ($class) {
    if (!$this->tools->mod[$class]) {
      $obj = new $class($this->options, $this->tools);
    }
  }
  
}

