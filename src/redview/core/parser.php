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
   * Whether to pretty-print the XML
   * 
   * @var bool
   */
  public $formatOutput = false;
  
  /**
   * Current node index (unused)
   * 
   * @var int
   */
  public $currentIndex=0;
  
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
   * Name of file currently being parsed
   * 
   * @var string
   */
  public $currentFile=null;
  

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
    $this->currentFile = $file;
    // TODO: unused event
    $event = $this->sendEvent('parseFile');
    if ($event->isCanceled) return;
    
    // if (!$file_exists) debug_print_backtrace();
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
    
    $doc->formatOutput = $this->formatOutput;
    
    $doc->loadXML("$text");
    
    // remove doctype if present
    if ($doc->doctype) $doc->removeChild($doc->doctype);
    
    $xpath = new DOMXpath($doc);

    $list = $xpath->evaluate("//*");
    $this->currentIndex=0;
    
    foreach ($list as $node) {
      $this->currentDocument = &$doc;
      $this->currentNode = &$node;
      $this->sendEvent('parseNode');
      ++$this->currentIndex;
    }

    $out = '';
    if ($doc->childNodes) {
      foreach ($doc->childNodes as $child) {
        $out .= $doc->saveXHTML( $child );
      }
    }
    
    $out = preg_replace('/\?>(\s*)<\?php/', " ?>\n<?php ", $out);

    return trim($out);

  }
  
  

}

