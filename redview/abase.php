<?php

/**
 * Base class, can send and listen for events.
 * @author owner
 *
 */
abstract class RedView_ABase implements RedView_Event_ISender, RedView_Event_IListener {
  /**
   * @var RedView_Toolbox
   */
  public $tools;
  
  /**
   * Send an event.
   * 
   * @param $name
   * 		Name of event to send.
   * @param $extra
   * 		Extra data to send with event
   */
  public function sendEvent ($name, $extra=null) {
    $events = $this->tools->events;
    $events::dispatch($name, $this, $extra);
  }
  
  /**
   * Listen for an event.
   * 
   * @param string $eventName
   * 		Name of event to listen for.
   * @param string $callbackName
   * 		Name of callback method.
   */
  public function listen ($eventName, $callbackName=null) {
    $callbackName || ($callbackName = $eventName);
    $events = $this->tools->events;
    $events::register($eventName, array($this, $callbackName));
  }
  
}

