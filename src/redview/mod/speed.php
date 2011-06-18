<?php
/**
 *
 * Speed optimizations plugin.
 *
 * - Serves static content from multiple cookieless subdomains.
 * - Specifies image width and height for faster page rendering.
 *
 */
class RedView_Mod_Speed extends RedView_Mod {

  /**
   * Static subdomain index
   * 
   * @var unknown_type
   */
  public static $subdomainIndex = 0;
  
  /**
   * Maximum static subdomains
   * 
   * @var int
   */
  public static $subdomainMax = 9;
  
  /**
   * Relinked links
   * 
   * @var array
   */
  public static $links = array();
  
  /**
   * Subdomain prefix
   * 
   * @var string
   */
  public $subdomainPrefix = 's';
  
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
   *    Event object
   */
  public function parseNode(RedView_Event $event) {

    $parser   = $event->sender;
    $node     = $parser->currentNode;

    $this->setImageDimensions($node);
    $this->useStaticUrl($node);

  }

  /**
   * setImageDimensions
   * 
   * Set image width/height attributes from actual image dimensions.
   *
   * @param DOMNode $node
   */
  protected function setImageDimensions(DOMNode $node) {

    if (!strtolower($node->nodeName) == 'img') return;
    
    $src = $node->getAttribute('src');

    // only works on local images not containing query strings
    if (!$src || $src{0} != '/' || strpos($src, '?')) return;

    list ($width, $height, $type, $attr) = getimagesize($_SERVER['DOCUMENT_ROOT'].$src);

    if (!($width && $height)) return;

    // use attributes instead of styles, so styles can override them if needed.
    if (!$node->getAttribute('width')) $node->setAttribute('width', $width);
    if (!$node->getAttribute('height')) $node->setAttribute('height', $height);

  }
  
  /**
   * useStaticUrl
   * 
   * Rewrite url references to use a static content domain.
   * 
   * @param DOMNode $node
   */
  protected function useStaticUrl(DOMNode $node) {
    $attribs = array(
      'img' => 'src',
      'link' => 'href',
      'script' => 'src'
    );
    
    if (!isset($attribs[$node->nodeName])) return;
    
    $attrib = $attribs[$node->nodeName];
    
    $url = $node->getAttribute($attrib);
    
    // only works on local urls
    if (!$url || $url{0} != '/') return;
    
    if (isset(self::$links[$url])) {
      $node->setAttribute($attrib, self::$links[$url]);
      return;
    }
    
    if (++self::$subdomainIndex > self::$subdomainMax) {
      self::$subdomainIndex = 1;
    }
    
    $newUrl = 'http://' . $this->subdomainPrefix . self::$subdomainIndex . '.' . $_SERVER['SERVER_NAME'] . $url;
    
    self::$links[$url] = $newUrl;
    
    $node->setAttribute($attrib, $newUrl);
    
  }

}

