<?php 

class RedView_Parser {

  public $cacheOn  = false;  // set to true for production
  public $cacheDir = '/tmp/rv-cache';
  public $viewDir = 'view';
  public $defaultView = 'RedView_View';
  
  public $preserveWhiteSpace = true;
  public $formatOutput = false;

  public $registry;
  public $tags;
  public $initted=false;

  public function register ($tag, $class) {
    $this->registry[$tag]=$class;
  }
  
  protected function checkInit () {
    if ($this->initted) return;
    $this->initted = true;
    foreach (explode(',', $this->tags) as $tag) $tag::register($this);
  }
  
  public function parse ($file) {
    require $this->findLoader($file);
  }
  
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
  
  protected function writeLoader ($cache, $file) {
    $classFile = substr($file, 0, strlen($file)-5).'.php';
    $class = $this->getClassFromFile($classFile);
    if (!$class) $class = $this->defaultView;
    file_put_contents("$cache.loader.php", "<?php ".
        (file_exists($classFile) ? "require_once '$classFile';" : "").
        " \$obj = new $class(); \$obj->loadMarkup('$cache.php');\n");
  }

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
  
  public $currentIndex=0;
  public $currentNode=null;
  public $currentDocument=null;
  
  public function parseFile ($file) {
    $xml = file_get_contents($file);
    return $this->parseXml ($xml);
  }
  
  public function parseXml ($xml) {
    $this->checkInit();
    $doc = new DOMDocument();
    $doc->preserveWhiteSpace = $this->preserveWhiteSpace;
    $doc->formatOutput = $this->formatOutput;
     if (!$xml) debug_print_backtrace();
    $doc->loadXML($xml);
    $xpath = new DOMXpath($doc);
    $list = $xpath->evaluate("//*");
    $this->currentIndex=$i=0;
    foreach ($list as $node) {
    
      $this->currentNode = &$node;
      $this->currentDocument = &$doc;
      $class = @$this->registry[$this->currentNode->nodeName];
      
      if (!$class) continue;
      
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
      
    $out = $doc->saveXML();
    $out = trim(str_replace('<?xml version="1.0"?>', '', $out), "\r\n\t ");
    $out = preg_replace('/\?>(\s*)<\?php/', '; ', $out);
    
    return $out;
    
  }
   
  
} 

