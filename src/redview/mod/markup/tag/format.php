<?php

class RedView_Mod_Markup_Tag_Format extends RedView_Mod_Markup_Tag {

  public function markup() {

    $doc = $this->node->ownerDocument;

    // use the tag's 'value' attribute if present.
    if (isset($this->attribs['value'])) {
      $pi = $doc->createProcessingInstruction('php', 
      		"echo \"{$this->attribs['value']}\"");
    }
    // otherwise use its inner xml content as a string.
    else {
      $xpath = new DOMXpath($doc);
      $list = $xpath->evaluate("node()", $this->node);
      $xml='';
      foreach ($list as $child) {
        // TODO: HACK. 
        $xml .= str_replace('&gt;', '>', $doc->saveXHTML($child));
      }
      if ($xml) {
        $pi = $doc->createProcessingInstruction('php', 
        		"echo <<<RV_HEREDOC\n$xml\nRV_HEREDOC;\n");
      }
    }
    
    $this->node->parentNode->replaceChild($pi, $this->node);
  }

}


