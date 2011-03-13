<?php
/**
 *
 * Responsible for "parsing" a view markup file into a cached php file.
 *
 */
class RedView_Parser extends RedView_ABase {

  /**
   * When turned on, cached files won't be overwritten.
   * 
   * Set to true for production, false for development.
   * 
   * @var bool
   */
  public $cacheOn  = false;
  /**
   * Default location for cached files.
   * 
   * @var string
   */
  public $cacheDir = '/tmp/rv-cache';
  /**
   * Path to look for views, relative to the application root.
   * 
   * @var string
   */
  public $viewDir = 'view';
  /**
   * Default view class (controller) to load when no .php file is present.
   * 
   * @var string
   */
  public $defaultView = 'RedView_View';

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
   * @return DOMNode
   */
  function getCurrentNode () {
    
    return $this->currentNode();
  }

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
   * Parse a file containing HTML and custom tags.
   *
   * @param string $file 
   * 		path to the  to parse.
   */
  public function parse ($file) {
    if (isset($_SESSION['_rv']) && isset($_SESSION['_rv']['slots']) && is_array($_SESSION['_rv']['slots'])) {
      foreach ($_SESSION['_rv']['slots'] as $k=>$v) RedView_Tag_Slot::$slots[$k] = $v;
    }
    require $this->findLoader($file);
    if (isset($_SESSION['_rv']) && isset($_SESSION['_rv']['slots'])) {
      unset($_SESSION['_rv']['slots']);
    }
  }

  /**
   * Find or write a loader for a cached PHP file.
   * 
   * @param string $file
   * @return string path to loader file
   */
  public function findLoader ($file) {
    $this->checkInit();
    $realFile = "{$this->viewDir}/$file";
    $sha1     = sha1($realFile);
    $cachedir = $this->cacheDir;
    $cache    = $cachedir . '/' . basename($realFile) . ".$sha1";
    $loader   = "$cache.loader.php";
    if (!$this->cacheOn || !file_exists($loader) || filemtime($loader)<filemtime($file)) {
      if (!file_exists($cachedir)) mkdir($cachedir);
      file_put_contents("$cache.php", $this->parseFile($realFile));
      $this->writeLoader($cache, $realFile);
    }
    return $loader;
  }

  /**
   * Write a loader for a cached PHP file.
   * 
   * @param string $cache
   * @param string $file
   */
  protected function writeLoader ($cache, $file) {
    $classFile = dirname($file) . '/' . implode('', explode('_', basename($file, '.html') . '.php'));
    $class = $this->getClassFromFile($classFile);
    if (!$class) $class = $this->defaultView;
    file_put_contents("$cache.loader.php", "<?php /* $classFile */ ".
        (file_exists($classFile) ? "
        require_once '$classFile';" : "")." 
      	if (!isset(\$_params)) \$_params = null;
        \$view = new $class(); 
        \$view->beforeRender(); 
        \$view->loadCache('$cache.php', \$_params);");
  }

  /**
   * Get the name of the first class defined in a php file.
   * 
   * @param string $file
   */
  public function getClassFromFile ($file) {
    if (!file_exists($file)) return '';
    $php = file_get_contents ($file);
    $tokens = token_get_all($php);
    $class_token = false;
    foreach ($tokens as $token) if (is_array($token)) {
      if ($token[0] == T_CLASS) {
        $class_token = true;
      } else if ($class_token && $token[0] == T_STRING) {
        return $token[1];
      }
    }
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
    $this->currentIndex=$i=0;
    foreach ($list as $node) {

      $this->currentNode = &$node;
      $this->currentDocument = &$doc;

      $this->sendEvent('parseNode');

      $class='';
      if (isset($this->registry[$this->currentNode->nodeName]))
      $class = @$this->registry[$this->currentNode->nodeName];
      else
      continue;

      $this->currentIndex=++$i;

      $params=array();
      if ($node->hasAttributes()) foreach ($node->attributes as $attrib) {
        $params[$attrib->name]=$attrib->value;
      }
      $vars = get_defined_vars();

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

