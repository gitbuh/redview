<?php
/**
 *
 * Built-in markup plugin.
 *
 */
class RedView_Mod_Markup extends RedView_Mod {

  /**
   * Initialize the plugin.
   *
   * @param array $options
   *   		optional options
   *
   * @param RedView_Toolbox $tools
   * 		optional custom tools
   */
  public function setup ($options=array(), RedView_Toolbox $tools=null) {

    parent::setup($options, $tools);

    $this->listen('parseNode');

  }
  
  /**
   * parseNode
   *
   * Event handler for parseNode events.
   *
   * @param RedView_Event $event
   * 		Event object
   */
  public function parseNode(RedView_Event $event) {

    /**
     * @var RedView_Parser
     */
    $parser   = $event->sender;
    $node     = &$parser->currentNode;
    $class    = null;
    $tag      = null;
    
    if (strpos($node->nodeName,'r:')!==0) return;
    
    $class = 'RedView_Mod_Markup_Tag_' . substr($node->nodeName, 2);
    
    if (!class_exists($class)) return;

    $tag = new $class();
    
    $attributes = array();
    if ($node->hasAttributes()) {
      foreach ($node->attributes as $attribute) {
        $attributes[$attribute->name] = $attribute->value;
      }
    }

    $tag->name     = $node->nodeName;
    $tag->parser   = $parser;
    $tag->attribs  = $attributes;

    $tag->markup();

  }

}

