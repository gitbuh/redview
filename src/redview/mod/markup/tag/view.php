<?php

class RedView_Mod_Markup_Tag_View extends RedView_Mod_Markup_Tag {

  public function markup (RedView_Core_Parser $parser) {
    
    $pi = null;

    if (isset($this->attribs['template'])) {
      $pi = "\$this->template=\"{$this->attribs['template']}\"";
    }
    $pi2 = "\$this->afterRender(); \$this->loadTemplate();";

    $this->toPhp($parser->currentNode, $pi, $pi2);

  }
}


