<?php

abstract class RedView_Mod_Markup_ClassTag extends RedView_Mod_Markup_Tag {


  
  /**
      Keep outer HTML
  */
  public $keepOuter=false;
  
  public static $tags;

  
  /**
      What to do when tag is pushed onto stack
      for example start output buffering
  */
  public function open () { }
  
  /**
      What to do when tag is popped off the stack
      for example get output buffer, manipulute, and output
  */
  public function close () { }
  
  public function markup ($parser) {
    
    if ($this->isInert($parser)) return;
    
    $doc    = $parser->currentDocument;
    $node   = $parser->currentNode;
    $open   = $this->prefixNode($parser);
    $close  = $this->suffixNode($parser);
    
    if ($open) $node->parentNode->insertBefore($open, $node);
    if ($this->keepOuter) {
      if ($close) {
        $node->parentNode->insertBefore($close, $node);
        $node->parentNode->insertBefore($node, $close);
      }
    }
    else {
      while ($node->childNodes->length) {
        $node->parentNode->insertBefore($node->childNodes->item(0), $node);
      }
      if ($close) {
        $node->parentNode->replaceChild($close, $node);
      }
      else {
        $node->parentNode->removeChild($node);
      }
    }
  }
  
  public function isInert ($parser) {
    return false;
  } 
  
  /**
      Open processing instruction
  */
  public function prefixNode ($parser) { 
    $doc    = $parser->currentDocument;
    $class  = get_class($this);
    $atts   = var_export($this->attribs, true);
    $tag = get_class($this);
    return $doc->createProcessingInstruction("php", "\${-1}=new $class('{$parser->currentNode->nodeName}', $atts, \$this); $class::\$tags[]=\${-1}; \${-1}->open()");
  }
  
  /**
      Closing processing instruction
  */
  public function suffixNode ($parser) { 
    $doc = $parser->currentDocument;
    $class  = get_class($this);
    return $doc->createProcessingInstruction("php", "\${-1}=array_pop($class::\$tags); \${-1}->close()");
  }
  
}

