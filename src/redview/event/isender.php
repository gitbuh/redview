<?php

/**
 * RedView event sender interface
 */
interface RedView_Event_ISender {

  /**
   * Send an event.
   * 
   * @param string $name
   * @param mixed $extra
   * @return RedView_Event
   */
  public function sendEvent ($name, $extra);

}
