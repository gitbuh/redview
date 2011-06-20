<?php

/**
 override form fields
 */
class RedView_Mod_Form_Tag_Field extends RedView_Mod_Markup_ClassTag {
  
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

    if (isset ($_SESSION['_rv']['fields'][$viewClass][$attribs['name']]) &&
        (!isset ($attribs['type']) || $attribs['type'] != 'password')) {
      $attribs['value']=$_SESSION['_rv']['fields'][$viewClass][$attribs['name']];
    }

    if (isset ($attribs['value'])) {

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

