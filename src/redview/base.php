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
   * 		Extra data to send with event
   */
  public function sendEvent ($name, $extra=null) {
    $events = $this->tools->events;
    return $events::dispatch($name, $this, $extra);
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
  public function listen ($eventName, $callbackName=null) {
    $callbackName || ($callbackName = $eventName);
    $events = $this->tools->events;
    $events::register($eventName, array($this, $callbackName));
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

