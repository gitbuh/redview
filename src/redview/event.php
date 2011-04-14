<?php

/**
 RedView Events. Allows event listeners.
 */
class RedView_Event {

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
   * Cancel an event. No further processing of the event will occur, 
   * even if other objects are listening for it.
   */
  public function cancel () {
    $this->isCanceled = true;
  }


}

