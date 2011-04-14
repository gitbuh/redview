<?php

class RedView_Mod_Markup_Tag_Else extends RedView_Mod_Markup_Tag {

  public function markup (RedView_Core_Parser $parser) {

    $dom = $parser->currentDocument;
    $node = $parser->currentNode;

    $pi = $dom->createProcessingInstruction('php', 
			"} else { ");
    
  
    $node->parentNode->insertBefore($pi, $node);

    while ($node->childNodes->length) {
      $node->parentNode->insertBefore($node->childNodes->item(0), $node);
    }

    $node->parentNode->removeChild($node);
  }

}

