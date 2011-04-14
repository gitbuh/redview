<?php

/** 
    override forms 
*/
class RedView_Mod_Form_Tag_Form extends RedView_Mod_Markup_Tag {
  
  /**
      Put a node before this node when writing to cache
  */
  public function markup (RedView_Core_Parser $parser) {
    
    $doc    = $parser->currentDocument;
    $node   = $parser->currentNode;
    
    if (!isset($this->attribs['action'])) return;
    
    $callback = $this->attribs['action'];
    
    $a = $doc->createAttribute('method');
    $a->value = 'POST';
    $node->appendChild($a);
    
    $a = $doc->createAttribute('enctype');
    $a->value = 'multipart/form-data';
    $node->appendChild($a);
    
    $a = $doc->createAttribute('action');
    $a->value = '.';
    $node->appendChild($a);
    
    $val = "RedView_Mod_Form::serializeCallbackObject(\$this, '$callback')";
    $pi = $doc->createProcessingInstruction('php', 
      "echo \"<input type='hidden' name='_rv:data' value='\".$val.\"' />\"");

    $node->appendChild($pi);
    
  }
  
}


