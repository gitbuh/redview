<?php
/**
 *
 * Responsible for registering and dispatching events.
 *
 */
class RedView_Core_Events extends RedView_Core {

  /**
   * Array of class names
   *
   * @var array
   */
  public $listeners;
  
  /**
   * Register a callback function for an event name.
   * 
   * @param string $name
   * 		Name of event to listen for.
   * 
   * @param mixed $callback
   * 		Anything call_user_func considers a callback function.
   * 		The first argument passed is this event object.
   */
  public function register ($name, $callback) {
    $this->listeners[$name][] = $callback;
  }

  /**
   * Dispatch an event.
   * 
   * @param string $eventName
   * 		Name of this event.
   * 
   * @param RedView_Event_ISender $sender
   * 		Object which sent this event.
   * 
   * @param mixed $extra
   * 		Optional misc. data to send with event.
   * 
   * @return RedView_Event 
   * 		Return the newly-created event object.
   */
  public function dispatch ($eventName, $sender=null, $extra=null) {
    if (!isset($this->listeners[$eventName])) return null;
    
    $evt = new RedView_Event();
    
    $evt->name = $eventName;
    $evt->sender = $sender;
    $evt->extra = $extra;
    
    foreach ($this->listeners[$eventName] as $callback) {
      call_user_func($callback, $evt);
      if ($evt->isCanceled) return $evt;
    }
    
    return $evt;
  }


}

