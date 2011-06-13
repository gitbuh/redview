<?php

/**
 * Base class, can send and listen for events.
 * @author owner
 *
 */
abstract class RedView_Base implements RedView_Event_ISender, RedView_Event_IListener {
  
  /**
   * @var RedView_Toolbox $tools
   */
  public $tools;
  
  /**
   * Send an event.
   * 
   * @param string $name
   * 		Name of event to send.
   * 
   * @param mixed $extra
   * 		Extra data to send with event.
   */
  final public function sendEvent ($name, $extra=null) {
    return $this->tools->events->dispatch($name, $this, $extra);
  }
  
  /**
   * Listen for an event.
   * 
   * @param string $eventName
   * 		Name of event to listen for.
   * 
   * @param string $callbackName
   * 		Name of callback method.
   */
  final public function listen ($eventName, $callbackName=null) {
    $callbackName || ($callbackName = $eventName);
    $callbackMethod = array($this, $callbackName);
    return $this->tools->events->register($eventName, $callbackMethod);
  }
  
  /**
   * Set up the object.
   *
   * @param RedView_Options $options
   *      optional options
   *
   * @param RedView_Toolbox $tools
   *    optional custom tools
   */
  public function setup (RedView_Options $options=null, RedView_Toolbox $tools=null) {
    if ($options) $this->applyOptions($options);
    $this->tools = $tools ? $tools : RedView::$tools;
  }

  /**
   * Apply options.
   * 
   * @param array $options
   * 		Options to apply.
   */
  public function applyOptions ($options) {
    // override me
  }
  
}

