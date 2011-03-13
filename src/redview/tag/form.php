<?php

/** 
    override forms 
*/
class RedView_Tag_Form extends RedView_ATag {
  
  public static function register ($parser) {
    $parser->register('form', __CLASS__);
  }
  
  /**
      Put a node before this node when writing to cache
  */
  public function markup ($parser) {
    
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
    
    $val = "RedView_Action::serializeCallbackObject(\$this, '$callback')";
    $pi = $doc->createProcessingInstruction('php', 
      "echo \"<input type='hidden' name='_rv:data' value='\".$val.\"' />\"");

    $node->appendChild($pi);
    
  }
  
}


