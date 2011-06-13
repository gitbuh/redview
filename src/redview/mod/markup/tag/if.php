<?php

class RedView_Mod_Markup_Tag_If extends RedView_Mod_Markup_Tag {

  public function markup() {
    
    $this->toPhp("if ({$this->attribs['value']}) {", '}');
    
  }

}

