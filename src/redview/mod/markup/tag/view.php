<?php

class RedView_Mod_Markup_Tag_View extends RedView_Mod_Markup_Tag {

  public function markup() {
    
    $pi = null;

    if (isset($this->attribs['template'])) {
      $pi = "if (!\$this->template) \$this->template=\"{$this->attribs['template']}\"";
    }
    $pi2 = "\$this->afterRender(); \$this->loadTemplate();";

    $this->toPhp($pi, $pi2);

  }
}


