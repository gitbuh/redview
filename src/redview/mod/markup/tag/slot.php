<?php


class RedView_Mod_Markup_Tag_Slot extends RedView_Mod_Markup_Tag {

  public static $names;

  public function markup (RedView_Core_Parser $parser) {

    $get = "";
    $set = "";
    $pi = null;
    $pi2 = null;
    
    if (isset($this->attribs['get'])) $get = $this->attribs['get'];
    if (isset($this->attribs['set'])) $set = $this->attribs['set'];
    
    if ($get) {
      $slot = "RedView::\$slots[\"$get\"]";
      $pi = "if (isset($slot)) echo $slot";
    }
    elseif ($set) {
      $pi = "ob_start(); ".__CLASS__."::\$names[]='$set';";
      $pi2 = "RedView::\$slots[array_pop(".__CLASS__."::\$names)]=ob_get_clean();";
    }

    $this->toPhp($parser->currentNode, $pi, $pi2);

  }
}




/*

class RedView_Tag_Slot extends RedView_Tag_AClassTag {

  public static function register ($parser) {
    $parser->register('r:slot', __CLASS__);
  }
  public static $slots;

  protected static $names;

  public function open () {
    if (isset($this->attribs['get'])) {
      if (isset(RedView_Tag_Slot::$slots[$this->attribs['get']])) {
        print RedView_Tag_Slot::$slots[$this->attribs['get']];
      }
      return;
    }
    if (isset($this->attribs['set'])) {
      ob_start();
      RedView_Tag_Slot::$names[]=$this->attribs['set'];
    }
  }

  public function close () {
    if (isset($this->attribs['set'])) {
      RedView_Tag_Slot::$slots[array_pop(RedView_Tag_Slot::$names)]=ob_get_clean();
    }
  }

}

*/

