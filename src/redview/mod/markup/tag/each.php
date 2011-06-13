<?php

class RedView_Mod_Markup_Tag_Each extends RedView_Mod_Markup_Tag {

  public function markup (RedView_Core_Parser $parser) {
    
    $dom = $parser->currentDocument;
    $node = $parser->currentNode;

    $in = $this->attribs['in'];
    $k  = $this->attribs['key'];
    $v  = $this->attribs['value'];
  
    $pi = $k ? "foreach ($in as $k=>$v) {" : "foreach ($in as $v) {";
    
    $this->toPhp($parser->currentNode, $pi, '}');
    
  }

}


