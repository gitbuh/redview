<?php
/**
 *
 * Xhtml plugin.
 *
 */
class RedView_Mod_Xhtml extends RedView_Mod {

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
    $this->listen('onFilter');

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
    return;

  }

  /**
   * onFilter
   *
   * Event handler for onFilter events.
   *
   * @param RedView_Event $event
   * 		Event object
   */
  public function onFilter(RedView_Event $event) {
    $event->sender->output = '<?xml version="1.0"?>'
    ."\n".'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'
    ."\n".trim($event->sender->output);
  }

}

