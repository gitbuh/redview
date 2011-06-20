<?php

class RedView_Mod_Markup_Tag_Base extends RedView_Mod_Markup_Tag {

  public function markup() {

    $this->toPhp('echo \'<base href="\'.RedView::$tools->router->getUrlBase().\'">\'');
    
  }

}

