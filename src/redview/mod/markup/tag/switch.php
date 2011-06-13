<?php

class RedView_Mod_Markup_Tag_Switch extends RedView_Mod_Markup_Tag {

  public function markup (RedView_Core_Parser $parser) {

    $this->toPhp($parser->currentNode, "switch ({$this->attribs['value']}) {", '}');
    
  }

}

