<?php

class RedView_Tag_If extends RedView_ATag {

  public static function register ($parser) {
    $parser->register('r:if', __CLASS__);
  }

  public function markup (RedView_Parser $parser) {

    $dom = $parser->currentDocument;
    $node = $parser->currentNode;

    $v=$this->attribs['value'];
    
    $pi = $dom->createProcessingInstruction('php', 
			"if ($v) {");
    
    $pi2 = $dom->createProcessingInstruction('php', "}");
  
    $node->parentNode->insertBefore($pi, $node);

    while ($node->childNodes->length) {
      $node->parentNode->insertBefore($node->childNodes->item(0), $node);
    }
    $node->parentNode->replaceChild($pi2, $node);
  }

}

