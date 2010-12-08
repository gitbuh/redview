<?php

/**
    RedView Events. Allows event listeners.
*/
class RedView_Event {

  public static $listeners = array();
  
  public static function listen ($sender, $eventName, $callback) {
    self::$listeners[$eventName][] = array($callback, $sender);
  }
  
  public static function send ($sender, $eventName, $data) {
    if (!($callbacks = @self::$listeners[$eventName])) return;
    foreach ($callbacks as $cb) call_user_func($cb, $sender, $eventName, $data);
  }
  
}


