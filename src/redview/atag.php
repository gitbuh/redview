<?php

abstract class RedView_ATag extends RedView_ABase {

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
      View object for a class tag.
      Should be null when writing to cache.
      @var RedView_View
  */
  public $view;
  
  /**
   * Base tag constructor.
   * 
   * @param string $name
   * 		The tag's XML name.
   * @param Array $attribs
   * 		The tag's XML attributes, as an array of key=>value string pairs.
   * @param RedView_View $view
   * 		The view object that this tag is associated with, if any (runtime only).
   */
  public function __construct ($name=null, $attribs=null, RedView_View $view=null) {
    $this->name=$name;
    $this->attribs=$attribs;
    $this->view=$view;
  }
  
  /**
   * Register a tag with the parser.
   * 
   * @param RedView_Parser $parser
   * @return string name of tag to rewrite.
   */
  public static function register ($parser) { 
    return '';
  }
  
  
  /**
   * Manipulate XML when writing to the cache.
   * 
   * @param RedView_Parser $parser
   */
  public function markup ($parser) {
  
  }
  
  
  /**
   * Get the inner XML content of the current node.
   * 
   * @param RedView_Parser $parser
   * @return string
   * 		Text content of inner XML of this node, 
   * 		with all entities converted to plain text.
   */
  public function innerXml ($parser) {
    $dom = $parser->currentDocument;
    $node = $parser->currentNode;
    $xpath = new DOMXpath($dom);
    $list = $xpath->evaluate("node()", $node);
    $xml='';
    foreach ($list as $child) {
      // TODO: Get rid of html_entity_decode and use original content
      //  i.e. get XMLDocument not to encode entities in the first place somehow.
      $xml.=html_entity_decode($dom->saveXML($child));
    }
    return  $xml;
  }
  
}

