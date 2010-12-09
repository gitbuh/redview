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
    
    @list ($a1, $a2) = explode('::', $this->attribs['action']);
    $class  = $a2 ? "'$a1'" : 'get_class($this)';
    $method = $a2 ? "'$a2'" : "'$a1'";
    $path = @array_shift(explode('?',$_SERVER['REQUEST_URI']));
    
    
    $pi = $doc->createProcessingInstruction('php', "echo \"<input type='hidden' name='_rv:data' value=\".RedView_Action::encodeRequest($method, $class).\" />\"");

    $node->appendChild($pi);
    
  }
  
}


