<?php

/** 
    override form fields 
*/
class RedView_Tag_Field extends RedView_Tag_AClassTag {

  public $value;
  
  public static function register ($parser) {
    $parser->register('input', __CLASS__);
    $parser->register('select', __CLASS__);
    $parser->register('textarea', __CLASS__);
  }
  
  public function open () {
    ob_start();
  }
  
  public function close () {
    
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
      
      if ($tag=='select') {
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
    
  /**
      Put a node before this node when writing to cache
  */
  public function prefixNode ($parser) {
    $this->keepOuter = true;
    if (@$this->attribs['type']=='submit' || @$this->attribs['type']=='button') return;
    $this->keepOuter = false;
    return parent::prefixNode($parser);
  }
  
  /**
      Put a node after this node when writing to cache
  */
  public function suffixNode ($parser) {
    $this->keepOuter = true;
    if (@$this->attribs['type']=='submit' || @$this->attribs['type']=='button') return;
    $this->keepOuter = false;
    return parent::suffixNode($parser);
  }
    
    
  
}


