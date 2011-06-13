<?php

/**
 * 
 * 
 * @author owner
 *
 */
abstract class RedView_Mod_Markup_Tag extends RedView_Base {
  
  /**
   * @var DOMNode
   */
  public $node;
  
  /**
   * Tag (node) name
   * 
   * @var string
   */
  public $name;
  
  /**
   * Tag attributes
   * 
   * @var array<string=>string>
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
    
    $this->node = $node;
    
    $this->name = $node->nodeName;
    
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
  abstract public function markup();


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
  public function toPhp ($openPi=null, $closePi=null) {
    
    $pi='';
    $pi2='';
    
    if ($openPi) {
      $pi = $this->node->ownerDocument->createProcessingInstruction('php', " $openPi ");
      $this->node->parentNode->insertBefore($pi, $this->node);
    }
    
    if ($closePi) {
      $pi2 = $this->node->ownerDocument->createProcessingInstruction('php', " $closePi ");
    }

    while ($this->node->childNodes->length) {
      $this->node->parentNode->insertBefore($this->node->childNodes->item(0), $this->node);
    }
    
    if ($closePi) {
      $this->node->parentNode->replaceChild($pi2, $this->node);
    
    } else {

      $this->node->parentNode->removeChild($this->node);
    }
    
  }
  
}
