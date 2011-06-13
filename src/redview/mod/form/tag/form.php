<?php

/** 
    override forms 
*/
class RedView_Mod_Form_Tag_Form extends RedView_Mod_Markup_Tag {
  
  /**
      Put a node before this node when writing to cache
  */
  public function markup() {
    
    $doc = $this->node->ownerDocument;
    
    if (!isset($this->attribs['action'])) return;
    
    $callback = $this->attribs['action'];
    
    $a = $doc->createAttribute('method');
    $a->value = 'post';
    $this->node->appendChild($a);
    
    $a = $doc->createAttribute('enctype');
    $a->value = 'multipart/form-data';
    $this->node->appendChild($a);
    
    $a = $doc->createAttribute('action');
    $a->value = '.';
    $this->node->appendChild($a);
    
    $val = "RedView_Mod_Form::serializeCallbackObject(\$this, '$callback')";
    $pi = $doc->createProcessingInstruction('php', 
      "echo \"<div style='display:none'><input type='hidden' name='_rv:data' value='\".$val.\"' /></div>\"");

    $this->node->appendChild($pi);
    
  }
  
}


