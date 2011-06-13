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
   * @param RedView_Options $options
   *      optional options
   *
   * @param RedView_Toolbox $tools
   *    optional custom tools
   */
  public function setup (RedView_Options $options=null, RedView_Toolbox $tools=null) {

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
    $d->appendChild($b); 
    
    // saveHTML annoyingly decides to url encode href attributes.
    // It also escapes things as html entities.
    // TODO: figure out a better workaround for this. 
    $h = html_entity_decode(urldecode($d->saveHTML()));
    $h2 = '';
    
    preg_match('/<\/[^>]+>$/', $h, $m, PREG_OFFSET_CAPTURE);
    
    if ($m && $m[0]) {
      $h = substr($h, 0, $m[0][1]);
      $h2 = $m[0][0];
    }
    
    $pi = $dom->createProcessingInstruction('php', 
			"echo <<<RV_HEREDOC\n$h\nRV_HEREDOC;\n");
    
    $pi2 = $dom->createProcessingInstruction('php', "echo '$h2';");
  
    $node->parentNode->insertBefore($pi, $node);

    while ($node->childNodes->length) {
      $node->parentNode->insertBefore($node->childNodes->item(0), $node);
    }
    $node->parentNode->replaceChild($pi2, $node);

  }
  
}
