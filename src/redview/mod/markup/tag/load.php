<?php

class RedView_Mod_Markup_Tag_Load extends RedView_Mod_Markup_Tag {
  
  public function markup (RedView_Core_Parser $parser) {
    if ($this->attribs['view']) {
      $dom  = $parser->currentDocument;
      $node = $parser->currentNode;
      $file = $parser->tools->cache->findLoader(trim($this->attribs['view'], '/').'.html');
      $params = array();
      // <load> can have parameters inside: <param name="..." value="..." />
      
      $objectParamString = "";
      
      if ($node->childNodes) {
        foreach ($node->childNodes as $child) if ($child->hasAttributes()) {
          $name = $child->getAttribute('name');
          $value = $child->getAttribute('value');
          $object = $child->getAttribute('object');
          if ($name) {
            if ($object) {
              $objectParamString .= " \$params['$name']=$object;";
            } else {
              $params[$name]=$value;
            }
          }
        }
      }
      $paramString = var_export($params, true);
      
      $pi   = $dom->createProcessingInstruction('php', 
          "\$params=$paramString;$objectParamString require '$file';");
      $node->parentNode->replaceChild($pi, $node);
    }
  }
  
}


