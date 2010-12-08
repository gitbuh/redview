<?php

class RedView_Tag_Slot extends RedView_Tag_AClassTag {

  public static function register ($parser) {
    $parser->register('slot', __CLASS__);
  }
  public static $slots;
  
  protected static $names;

  public function open () {
    if (@$this->attribs['get']) {
      print @self::$slots[$this->attribs['get']];
      return;
    }
    if (@$this->attribs['set']) {
      ob_start();
      self::$names[]=$this->attribs['set'];
    }
  }
  
  public function close () {
    if (@$this->attribs['set']) {
      self::$slots[array_pop(self::$names)]=ob_get_clean();
    }
  }
  
}


