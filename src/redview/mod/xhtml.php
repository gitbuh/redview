<?php
/**
 *
 * Xhtml plugin. Help make cached files xhtml-firendly.
 *
 */
class RedView_Mod_Xhtml extends RedView_Mod {
  
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
    $this->listen('onCache');

  }

  /**
   * parseNode
   *
   * Event handler for parseNode events.
   *
   * @param RedView_Event $event
   *    Event object
   */
  public function parseNode(RedView_Event $event) {

    /**
     * @var RedView_Core_Parser
     */
    $parser   = $event->sender;
    $node     = $parser->currentNode;
    $class    = null;
    $tag      = null;

    // make tags xhtml-friendly

    if ($node->nodeName == 'html') {
      if (!$node->getAttribute('xmlns')) {
        $node->setAttribute('xmlns', 'http://www.w3.org/1999/xhtml');
      }
    }
    elseif ($node->nodeName == 'img') {
      if (!$node->getAttribute('alt')) {
        $node->setAttribute('alt', '');
      }
    }

  }

  /**
   * onCache
   *
   * Event handler for onCache events.
   *
   * @param RedView_Event $event
   * 		Event object
   */
  public function onCache(RedView_Event $event) {
    // remove xmlns:r
    $event->sender->output = preg_replace('/\s*xmlns:r="[^"]*"/', '', $event->sender->output);
    
    // Add doctype to files beginning with '<html'
    
    // TODO: This wont work if <html is not the first thing in cached document...
    // Any php and most r: tags before html tag will cause this to fail.
    // Nesting html tag inside r:view works as expected, though.
    if (strtolower(substr($event->sender->output, 0, 5)) != '<html') return;
    $event->sender->output = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'
        ."\n".$event->sender->output;
        
  }

}

