<?php

/**
 RedView Events. Allows event listeners.
 */
class RedView_Event {

  /**
   * Array of class names
   *
   * @var array
   */
  public static $listeners;

  /**
   * Name of this event.
   *
   * @var string
   */
  public $name;
  /**
   * Object which sent this event.
   *
   * @var RedView_Event_ISender
   */
  public $sender;
  /**
   * Optional misc. data to send with event.
   *
   * @var mixed
   */
  public $extra;

  /**
   * Has this event been cancelled?
   *
   * @var bool
   */
  public $isCanceled=false;

  /**
   * Constructor. Use the static method RedView_Event::dispatch instead.
   *
   * @param string $eventName
   * 		Name of this event.
   * 
   * @param RedView_Event_ISender $sender
   * 		Object which sent this event.
   * 
   * @param mixed $extra
   * 		Optional misc. data to send with event.
   */
  public function __construct($eventName, RedView_Event_ISender $sender=null, $extra=null) {
    $this->name=$eventName;
    $this->sender=$sender;
    $this->extra=$extra;
  }

  /**
   * Cancel an event. No further processing of the event will occur, 
   * even if other classes are listening for it.
   */
  public function cancel () {
    $this->isCanceled = true; return -1;
  }

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
  public static function register ($name, $callback) {
    self::$listeners[$name][] = $callback;
  }

  /**
   * Dispatch an event. Use this instead of the constructor.
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
  public static function dispatch ($eventName, $sender=null, $extra=null) {
    if (!@self::$listeners[$eventName]) return;
    $Event = get_called_class();
    $evt = new $Event($eventName, $sender, $extra);
    foreach (self::$listeners[$eventName] as $callback) {
      call_user_func($callback, $evt);
      if ($evt->isCanceled) return $evt;
    }
    return $evt;
  }

}

