<?php

class RedView_Mod_Markup_Tag_Slot extends RedView_Mod_Markup_Tag {

  public static $names;

  public function markup() {

    $pi = null;
    $pi2 = null;
    
    if (isset($this->attribs['get'])) {
      $slot = "RedView::\$slots[\"{$this->attribs['get']}\"]";
      $pi = "if (isset($slot)) echo $slot";
    }
    elseif (isset($this->attribs['set'])) {
      $pi = "ob_start(); ".__CLASS__."::\$names[]='{$this->attribs['set']}';";
      $pi2 = "RedView::\$slots[array_pop(".__CLASS__."::\$names)]=ob_get_clean();";
    }

    $this->toPhp($pi, $pi2);

  }
}
