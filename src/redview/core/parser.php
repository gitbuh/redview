<?php
/**
 *
 * Responsible for "parsing" a view markup file into a cached php file.
 *
 */
class RedView_Core_Parser extends RedView_Core {

  /**
   * Path to look for views, relative to the application root.
   * 
   * @var string
   */
  public $viewPath = 'view';
  
  /**
   * Node currently being parsed
   * 
   * @var DOMNode
   */
  public $currentNode=null;
  
  /**
   * Document currently being parsed
   * 
   * @var RedView_DOMDocument
   */
  public $currentDocument=null;

  /**
   * Apply options.
   * 
   * @param RedView_Options $options
   * 		Options to apply.
   */
  public function applyOptions (RedView_Options $options=null) {
    if (!$options) return;
    
    if (isset($options->view_path)) {
      $this->viewPath = $options->view_path;
    }
    
  }

  /**
   * Parse a file
   * 
   * @param string $file
   * 		path to file
   */
  public function parseFile ($file) {
    
    $text = file_get_contents($file);
    return $this->parseText($text);
    
  }

  /**
   * Parse an XML string
   * 
   * @param string $xml
   * 		valid xml fragment
   */
  public function parseText ($text) {
      
    $doc = new RedView_DOMDocument();
    $doc->preserveWhiteSpace = false;
    $doc->loadXML("$text");
    
    // remove doctype if present
    if ($doc->doctype) $doc->removeChild($doc->doctype);
    
    $xpath = new DOMXpath($doc);
    
    // remove comments
    
    $commentNodes = $xpath->evaluate("//comment()");
  
    foreach ($commentNodes as $node) {
      $node->parentNode->removeChild($node);
    }
    
    // trim whitespace on text nodes
    
    $textNodes = $xpath->evaluate("//text()");
    
    foreach ($textNodes as $node) {
      $node->data = preg_replace("/\s+$/", " ", $node->data);
      $node->data = preg_replace("/^\s+/", " ", $node->data);
    }
    
    // fire parseNode event on all nodes
     
    $nodes = $xpath->evaluate("//*");
    
    foreach ($nodes as $node) {
      $this->currentDocument = &$doc;
      $this->currentNode = &$node;
      $this->sendEvent('parseNode');
    }
    
    $out = '';
    if ($doc->childNodes) {
      foreach ($doc->childNodes as $child) {
        $out .= $doc->saveXHTML( $child );
      }
    }
     
    $out = preg_replace('/\?>(\s*)<\?php/', "?>\n<?php", $out);

    return trim($out);

  }

}

