<?php

class RedView_View {

  public $attribs;
  public $template;
  
  public function beforeRender () { 
  }
  
  public function render () {
  }
  
  public function afterRender () { 
  }
  
  public function loadTemplate () {
    $cache=null;
    $template=@$this->attribs['template'];
    if (!$template) $template=$this->template;
    if ($template) $cache = RedView::parse($template);
    if ($cache) require_once $cache;
  }
  
  public function loadMarkup ($file) {
    include $file;
  }
  
}

