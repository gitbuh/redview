<?php

class RedView_Mod_Markup_Tag_Load extends RedView_Mod_Markup_Tag {
  
  public function markup (RedView_Core_Parser $parser) {
    if ($this->attribs['view']) {
      $dom  = $parser->currentDocument;
      $node = $parser->currentNode;
      $file = $parser->tools->cache->findLoader(trim($this->attribs['view'], '/').'.html');
      $params = array();
      // <load> can have parameters inside: <param name="..." value="..." />
      if ($node->childNodes) {
        foreach ($node->childNodes as $child) if ($child->hasAttributes()) {
          $name = $child->getAttribute('name');
          $value = $child->getAttribute('value');
          if ($name) $params[$name]=$value;
        }
      }
      $_params = var_export($params, true);
      $pi   = $dom->createProcessingInstruction('php', "\$params=$_params; require '$file';");
      $node->parentNode->replaceChild($pi, $node);
    }
  }
  
}


