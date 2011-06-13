<?php

class RedView_Mod_Markup_Tag_Switch extends RedView_Mod_Markup_Tag {

  public function markup() {

    $this->toPhp("switch ({$this->attribs['value']}) {", '}');
    
  }

}

