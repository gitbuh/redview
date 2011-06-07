<?php

class RedView_Mod_Markup_Tag_Format extends RedView_Mod_Markup_Tag {

  public function markup (RedView_Core_Parser $parser) {

    $dom = $parser->currentDocument;
    $node = $parser->currentNode;

    // use the tag's 'value' attribute if present.
    if (isset($this->attribs['value'])) {
      $pi = $dom->createProcessingInstruction('php', 
      		"echo \"{$this->attribs['value']}\"");
    }
    // otherwise use its inner xml content as a string.
    else {
      $xpath = new DOMXpath($dom);
      $list = $xpath->evaluate("node()", $node);
      $xml='';
      foreach ($list as $child) {
        // TODO: HACK. 
        $xml .= str_replace('&gt;', '>', $dom->saveXHTML($child));
      }
      if ($xml) {
        $pi = $dom->createProcessingInstruction('php', 
        		"echo <<<RV_HEREDOC\n$xml\nRV_HEREDOC;\n");
      }
    }
    
    $node->parentNode->replaceChild($pi, $node);
  }

}


