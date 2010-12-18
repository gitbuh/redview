<?php

/**
    RedView Event Sender Interface
*/
interface RedView_Event_ISender {

  public function sendEvent ($name, $extra);

}
