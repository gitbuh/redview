<?php

class RedView_Tag_Load extends RedView_ATag {
  
  public static function register ($parser) {
    $parser->register('r:load', __CLASS__);
  }
  
  public function markup (RedView_Parser $parser) {
    if ($this->attribs['view']) {
      $dom  = $parser->currentDocument;
      $node = $parser->currentNode;
      $file = $parser->findLoader(trim($this->attribs['view'], '/').'.html');
      $params = array();
      if ($node->childNodes) {
        foreach ($node->childNodes as $child) {
          $name = $child->getAttribute('name');
          $value = $child->getAttribute('value');
          if ($name) $params[$name]=$value;
        }
      }
      $_params = var_export($params, true);
      $pi   = $dom->createProcessingInstruction('php', "\$_params=$_params; require '$file'");
      $node->parentNode->replaceChild($pi, $node);
    }
  }
  
}


