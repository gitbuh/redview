<?php

/**
 * RedView event listener interface
 */
interface RedView_Event_IListener {

  /**
   * Listen for an event.
   * 
   * @param string $eventName
   * @param string $callbackName
   */
  public function listen ($eventName, $callbackName);

}
