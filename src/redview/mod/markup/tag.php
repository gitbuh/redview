<?php

/**
 * 
 * 
 * @author owner
 *
 */
abstract class RedView_Mod_Markup_Tag extends RedView_Base {

  /**
   Tag (node) name
   @var string
   */
  public $name;
  /**
   Tag attributes
   @var array<string=>string>
   */
  public $attribs=array();

  /**
   * From node
   * 
   * Set name and attribs from DOM node
   * 
   * @param DOMNode $node
   */
   public function fromNode(DOMNode $node) {
    
    $this->name     = $node->nodeName;
    
    $this->attribs = array();
    if ($node->hasAttributes()) {
      foreach ($node->attributes as $attribute) {
        $this->attribs[$attribute->name] = $attribute->value;
      }
    }
    
  }
  
  /**
   * Manipulate XML when writing to the cache.
   *
   * @param RedView_Core_Parser $parser
   */
  abstract public function markup (RedView_Core_Parser $parser);


  /**
   * toPhp
   * 
   * Replace opening and closing tags with PHP processing instructions.
   * 
   * @param DOMNode $node
   *        Node to replace.
   * 
   * @param string $openPi
   *        Optional opening PHP code.
   *        
   * @param string $closePi
   *        Optional closing PHP code.
   */
  public function toPhp (DOMNode $node, $openPi=null, $closePi=null) {
    
    $doc = $node->ownerDocument;
    if ($openPi) {
      $pi = $doc->createProcessingInstruction('php', " $openPi ");
      $node->parentNode->insertBefore($pi, $node);
    }
    if ($closePi) {
      $pi2 = $doc->createProcessingInstruction('php', " $closePi ");
    }

    while ($node->childNodes->length) {
      $node->parentNode->insertBefore($node->childNodes->item(0), $node);
    }
    
    if ($closePi) {
      $node->parentNode->replaceChild($pi2, $node);
    } else {
      $node->parentNode->removeChild($node);
    }
    
  }
  
}
