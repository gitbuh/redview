<?php

abstract class RedView_ABase implements RedView_Event_ISender, RedView_Event_IListener {

  public $tools;
  
  public function sendEvent ($name, $extra=null) {
    $c = $this->tools->events;
    $c::dispatch($name, $this, $extra);
  }
  
  public function listen ($eventName, $callbackName=null) {
    $callbackName || ($callbackName = $eventName);
    $c = $this->tools->events;
    $c::register($eventName, array($this, $callbackName));
  }
  
}

