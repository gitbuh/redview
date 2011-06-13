<?php

class RedView_Mod_Markup_Tag_Default extends RedView_Mod_Markup_Tag {

  public function markup() {
    
    $this->toPhp('default:', 'break;');
  
  }

}

