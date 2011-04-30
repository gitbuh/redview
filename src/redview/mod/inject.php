<?php
/**
 *
 * Injects php variables into HTML tag attributes.
 * 
 * Give tags an 'inject' attribute to invoke this module's functionality.
 *
 */
class RedView_Mod_Inject extends RedView_Mod {
  
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
  public function parseNode (RedView_Event $event) {
    
    $parser = $event->sender;
    $dom = $parser->currentDocument;
    $node = $parser->currentNode;
    
    if (!$node->hasAttributes()) return;
      
    $inject = false;
    foreach ($node->attributes as $attrib) {
      if ($attrib->name == 'inject') { 
        $node->removeAttributeNode($attrib);
        $inject = true; 
      }
    }
    
    if (!$inject) return;
    
    $event->cancel();
    
    $d = new DOMDocument();
    $b = $d->importNode($node->cloneNode(false),true);
    $d->appendChild($b); $h = $d->saveHTML();
    
    foreach ($node->attributes as $attribute) {
      $this->attribs[$attribute->name] = $attribute->value;
    }
    
    $pi = $dom->createProcessingInstruction('php', 
			"echo '$h'");
    
    $pi2 = $dom->createProcessingInstruction('php', " /* */ ");
  
    $node->parentNode->insertBefore($pi, $node);

    while ($node->childNodes->length) {
      $node->parentNode->insertBefore($node->childNodes->item(0), $node);
    }
    $node->parentNode->replaceChild($pi2, $node);

  }
  
}
