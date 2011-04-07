<?php
/**
 *
 * Responsible for "parsing" a view markup file into a cached php file.
 *
 */
class RedView_Parser extends RedView_Base {

  /**
   * Path to look for views, relative to the application root.
   * 
   * @var string
   */
  public $viewDir = 'view';

  /**
   * Whether to preserve whitespace in the XML
   * 
   * @var bool
   */
  public $preserveWhiteSpace = true;
  /**
   * Whether to pretty-print the XML
   * 
   * @var bool
   */
  public $formatOutput = false;
  /**
   * Array of registered RedView_Tag objects
   * 
   * @var array
   */
  public $registry;
  /**
   * A comma-delimited list of tags to register
   *
   * @var string
   */
  public $tags;
  /**
   * Whether the parser has been initialized
   * 
   * @var bool
   */
  public $initted=false;
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
   * @var DOMDocument
   */
  public $currentDocument=null;

  /**
   * Register a class as a handler for tags.
   * 
   * @param string $tag
   * @param string $class
   */
  public function register ($tag, $class) {
    $this->registry[$tag]=$class;
  }

  /**
   * Do initialization if needed.
   */
  protected function checkInit () {
    if ($this->initted) return;
    $this->initted = true;
    //TODO: there must be a better way to register tags?
    foreach (explode(',', $this->tags) as $tag) $tag::register($this);
  }

  /**
   * Parse a file
   * 
   * @param string $file
   * 		path to file
   */
  public function parseFile ($file) {
    // if (!$file_exists) debug_print_backtrace();
    $xml = file_get_contents($file);
    return $this->parseXml ($xml);
  }

  /**
   * Parse an XML string
   * 
   * @param string $xml
   * 		valid xml fragment
   */
  public function parseXml ($xml) {

    //TODO: fix this primitive doctype removal
    $xml = preg_replace('/<!DOCTYPE[^>]*>/','',$xml);
     
    $this->checkInit();
    $doc = new DOMDocument();
    $doc->preserveWhiteSpace = $this->preserveWhiteSpace;
    $doc->formatOutput = $this->formatOutput;
    $doc->loadXML("<fakeroot>$xml</fakeroot>");
    $xpath = new DOMXpath($doc);

    /*
     $list = $xpath->evaluate("//processing-instruction()");
     foreach ($list as $node) {
     $node->nodeValue .= '?';
     }
     */
    $list = $xpath->evaluate("/fakeroot//*");
    $this->currentIndex=0;
    foreach ($list as $node) {

      $this->currentNode = &$node;
      $this->currentDocument = &$doc;

      $this->sendEvent('parseNode');

      $class='';
      if (isset($this->registry[$this->currentNode->nodeName]))
      $class = @$this->registry[$this->currentNode->nodeName];
      else
      continue;

      ++$this->currentIndex;

      $params=array();
      if ($node->hasAttributes()) foreach ($node->attributes as $attrib) {
        $params[$attrib->name]=$attrib->value;
      }

      $obj=new $class($this->currentNode->nodeName, $params);

      $obj->markup($this);

    }

    if (!$this->preserveWhiteSpace) {
      $list = $xpath->evaluate("//text()");
      foreach ($list as $node) {
        $node->data = preg_replace("/\s+$/", " ", $node->data);
        $node->data = preg_replace("/^\s+/", " ", $node->data);
      }
    }

    $html = '';
    $children = $doc->firstChild->childNodes;
    if ($children) {
      foreach ($children as $child) {
        $html .= $doc->saveXML( $child );
      }
    }
    $out = $html;

    $out = preg_replace('/\?>(\s*)<\?php/', " ?>\n<?php ", $out);

    return $out;

  }


}

