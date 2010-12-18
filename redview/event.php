<?php

/**
    RedView Events. Allows event listeners.
*/
class RedView_Event {

  public static $listeners;

  public $name;
  public $sender;
  public $extra;
  public $isCanceled=false;
  
  public function __construct($name, $sender=null, $extra=null) {
    $this->name=$name;
    $this->sender=$sender;
    $this->extra=$extra;
  }
  
  public function cancel () {
    $this->isCanceled = true; return -1;
  }

  public static function register ($name, $callback) {
    self::$listeners[$name][] = $callback;
  }
  
  public static function dispatch ($name, $sender=null, $extra=null) {
    if (!@self::$listeners[$name]) return;
    $c = get_called_class();
    $evt = new $c($name, $sender, $extra);
    foreach (self::$listeners[$name] as $callback) {
      call_user_func($callback, $evt);
      if ($evt->isCanceled) return $evt;
    }
    return $evt;
  }

}

