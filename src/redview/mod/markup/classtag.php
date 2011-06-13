<?php

abstract class RedView_Mod_Markup_ClassTag extends RedView_Mod_Markup_Tag {

  
  public static $tags;

  /**
   * View object. 
   * 
   * @var RedView_View
   */
  public $view;

  public function markup (RedView_Core_Parser $parser) {
    
    $class  = get_class($this);
    
    $atts = var_export($this->attribs, true);
  
    $this->toPhp($parser->currentNode, 
        "$class::open(\$this, '{$this->name}', $atts);", 
        "$class::close();");
  }
  
  public static function open ($view, $name, $attribs) {
    $class = get_called_class();
    $tag = new $class();
    
    $tag->view     = $view;
    $tag->name     = $name;
    $tag->attribs  = $attribs;
    
    $tag->onOpen();
    
    self::$tags[] = $tag;
    
  }
  
  public static function close () {
    
    $tag = array_pop(self::$tags);
    $tag->onClose();
    
  }
  
}

