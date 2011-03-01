<?php

class RedView_Tag_Load extends RedView_ATag {
  
  public static function register ($parser) {
    $parser->register('r:load', __CLASS__);
  }
  
  public function markup ($parser) {
    if ($this->attribs['file']) {
      $dom  = $parser->currentDocument;
      $node = $parser->currentNode;
      $file = $parser->findLoader($this->attribs['file']);
      $pi   = $dom->createProcessingInstruction('php', "require '$file'");
      $node->parentNode->replaceChild($pi, $node);
    }
  }
  
}


