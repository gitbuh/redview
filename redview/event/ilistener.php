<?php

/**
    RedView Event Listener Interface
*/
interface RedView_Event_IListener {

  public function listen ($eventName, $callbackName);

}
