<?php

class RedView_Tag_Slot extends RedView_Tag_AClassTag {

  public static function register ($parser) {
    $parser->register('slot', __CLASS__);
  }
  public static $slots;
  
  protected static $names;

  public function open () {
    if (@$this->attribs['get']) {
      print @RedView_Tag_Slot::$slots[$this->attribs['get']];
      return;
    }
    if (@$this->attribs['set']) {
      ob_start();
      RedView_Tag_Slot::$names[]=$this->attribs['set'];
    }
  }
  
  public function close () {
    if (@$this->attribs['set']) {
      RedView_Tag_Slot::$slots[array_pop(RedView_Tag_Slot::$names)]=ob_get_clean();
    }
  }
  
}


