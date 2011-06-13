<?php

class RedView_Mod_Markup_Tag_Case extends RedView_Mod_Markup_Tag {

  public function markup() {
    
    $this->toPhp("case {$this->attribs['value']}:", 'break;');
    
  }

}

