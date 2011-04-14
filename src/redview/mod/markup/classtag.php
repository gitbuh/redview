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

    $dom    = $parser->currentDocument;
    $node   = $parser->currentNode;
    $class  = get_class($this);
  
    $attributes = array();
    if ($node->hasAttributes()) {
      foreach ($node->attributes as $attribute) {
        $attributes[$attribute->name] = $attribute->value;
      }
    }
    
    $atts   = var_export($this->attribs, true);
    
    $pi = $dom->createProcessingInstruction('php', 
			"$class::open(\$this, '{$this->name}', $atts);");
    
    $pi2 = $dom->createProcessingInstruction('php',
			"$class::close();");
  
    $node->parentNode->insertBefore($pi, $node);

    while ($node->childNodes->length) {
      $node->parentNode->insertBefore($node->childNodes->item(0), $node);
    }
    
    $node->parentNode->replaceChild($pi2, $node);
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

