<?php

class RedView_Tag_Format extends RedView_ATag {

  public static function register ($parser) {
    $parser->register('r:format', __CLASS__);
  }

  public function markup (RedView_Parser $parser) {

    $dom = $parser->currentDocument;
    $node = $parser->currentNode;


    if (isset($this->attribs['value'])) {
      $pi = $dom->createProcessingInstruction('php', 
      		"echo \"{$this->attribs['value']}\"");
    }
    else {
      $xml=$this->innerXml($parser);
      if ($xml) {
        $pi = $dom->createProcessingInstruction('php', 
        		"echo <<<RV_HEREDOC\n$xml\nRV_HEREDOC;\n");
      }
    }
    
    $node->parentNode->replaceChild($pi, $node);
  }

}


