<?php

class RedView_Tag_View extends RedView_Tag_AClassTag {

  public $view;

  public static function register ($parser) {
    $parser->register('r:view', __CLASS__);
  }
  public function open () {
  
    foreach ($this->attribs as $k=>$v) {
      $this->view->$k = $v;
    }
    
    $this->view->beforeRender();
    
  }
  
  public function close () {
    
    $this->view->render();
    
    $this->view->afterRender();
    
    $this->view->loadTemplate();
    
  }
  
  
}


