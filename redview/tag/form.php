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
    $action = $a2 ? $a2 : $a1;
    $path = @array_shift(explode('?',$_SERVER['REQUEST_URI']));
    
    
    if (RedView::$toolbox->crypto->enabled) {
      $pi = $doc->createProcessingInstruction('php', '@$viewClass||$viewClass='.$class."; 
\$v=RedView::encrypt(json_encode(array('$action', \"\$viewClass\", \"{\$_SERVER['REQUEST_URI']}\")));
echo \"<input type='hidden' name='_rv:data' value=\".\$v.\" />\";
");
    }
    else {
      $node->setAttribute('method', 'POST');
      $hidden = $doc->createElement('input');
      $hidden->setAttribute('type', 'hidden');
      $hidden->setAttribute('name', '_rv:action');
      $hidden->setAttribute('value', $action);
      $node->appendChild($hidden);
      
      $pi = $doc->createProcessingInstruction('php', '@$viewClass||$viewClass='.$class.'; echo <<<EOT
<input type="hidden" name="_rv:class" value="{$viewClass}">
<input type="hidden" name="_rv:path" value="{$_SERVER[\'REQUEST_URI\']}">
EOT
');
    }

    $node->appendChild($pi);
    
  }
  
}


