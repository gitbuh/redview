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


}
