<?php

class RedView_Mod_Markup_Tag_Case extends RedView_Mod_Markup_Tag {

  public function markup (RedView_Core_Parser $parser) {

    $dom = $parser->currentDocument;
    $node = $parser->currentNode;

    $v=$this->attribs['value'];
    
    $pi = $dom->createProcessingInstruction('php', 
			"case $v:");
    
    $pi2 = $dom->createProcessingInstruction('php', "break;");
  
    $node->parentNode->insertBefore($pi, $node);

    while ($node->childNodes->length) {
      $node->parentNode->insertBefore($node->childNodes->item(0), $node);
    }
    $node->parentNode->replaceChild($pi2, $node);
  }

}

