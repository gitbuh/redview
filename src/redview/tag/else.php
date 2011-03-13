<?php

class RedView_Tag_Else extends RedView_ATag {

  public static function register ($parser) {
    $parser->register('r:else', __CLASS__);
  }

  public function markup (RedView_Parser $parser) {

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
