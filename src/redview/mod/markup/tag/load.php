<?php

class RedView_Mod_Markup_Tag_Load extends RedView_Mod_Markup_Tag {
  
  public function markup() {
    if ($this->attribs['view']) {
      $doc = $this->node->ownerDocument;
      $file = $this->tools->cache->findLoader(trim($this->attribs['view'], '/').'.html');
      $params = array();
      // <load> can have parameters inside: <param name="..." value="..." />
      
      $objectParamString = "";
      
      if ($this->node->childNodes) {
        foreach ($this->node->childNodes as $child) if ($child->hasAttributes()) {
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
      
      $pi   = $doc->createProcessingInstruction('php', 
          "\$params=$paramString;$objectParamString require '$file';");
      $this->node->parentNode->replaceChild($pi, $this->node);
    }
  }
  
}


