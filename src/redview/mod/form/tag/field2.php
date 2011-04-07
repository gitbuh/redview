<?php

/**
 override form fields
 */
class RedView_Mod_Form_Tag_Field extends RedView_Mod_Markup_Tag {
  
  /**
   * Field tag stack
   * 
   * @var array<RedView_ATag>
   */
  public static $tags = array();

  public static function register ($parser) {
    $parser->register('input', __CLASS__);
    $parser->register('select', __CLASS__);
    $parser->register('textarea', __CLASS__);
  }
  
  public function markup (RedView_Parser $parser) {

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
  
  /**
   * Open tag.
   * 
   * @param string $name
   * @param array $attribs
   */
  public function onOpen () {
    ob_start();
  }

  public function onClose () {

    $tag=$this->name;
    $attribs=$this->attribs;
    $content = ob_get_clean();
    $selfclose=true;
    $atts='';
    $viewClass = get_class($this->view);

    if (preg_match('/^(textarea|select)$/i',$tag)) {
      $selfclose=false;
    }

    if (@$_SESSION['_rv']['fields'][$viewClass][$attribs['name']]) {
      $attribs['value']=$_SESSION['_rv']['fields'][$viewClass][$attribs['name']];
    }

    if (@$attribs['value']) {

      if ($tag=='textarea') {
        $content = $attribs['value'];
        unset ($attribs['value']);
      }

      elseif  ($tag=='select') {
        $xml = RedView::toXml($content);
        foreach ($xml as $option) {
          $xmlAttribs = $option->attributes();
          if ($xmlAttribs['value'] == $attribs['value']) {
            $option->addAttribute('selected','selected');
            break;
          }
        }
        $content=RedView::fromXml($xml);
        unset ($attribs['value']);
      }

    }

    $outer = RedView::toXml("<{$tag}".($selfclose ? " />" : ">$content</{$tag}>"));

    foreach ($attribs as $k=>&$v) {
      $outer->addAttribute($k, $v);
    }

    echo RedView::fromXml($outer);

  }
  
}

