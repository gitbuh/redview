<?php

class RedView_Mod_Markup_Tag_Each extends RedView_Mod_Markup_Tag {

  public static function register ($parser) {
    $parser->register('r:each', __CLASS__);
  }

  public function markup (RedView_Parser $parser) {

    $dom = $parser->currentDocument;
    $node = $parser->currentNode;

    $in=$this->attribs['in'];
    $k=$this->attribs['key'];
    $v=$this->attribs['value'];
    
    $pi;
    if (!$k) {
      $pi = $dom->createProcessingInstruction('php', 
    		"foreach ($in as $v) { ");
    } else {
      $pi = $dom->createProcessingInstruction('php', 
      		"foreach ($in as $k=>$v) { ");
    }
    
    $pi2 = $dom->createProcessingInstruction('php', " } ");
  
    $node->parentNode->insertBefore($pi, $node);

    while ($node->childNodes->length) {
      $node->parentNode->insertBefore($node->childNodes->item(0), $node);
    }

    $node->parentNode->replaceChild($pi2, $node);
  }

}


