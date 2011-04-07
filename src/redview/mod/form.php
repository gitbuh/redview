<?php
/**
 *
 * Form handling plugin.
 *
 */
class RedView_Mod_Form extends RedView_Mod {

  /**
   * Time to live, in seconds.
   * 
   * After the time expires, the form post won't be accepted.
   * 
   * @var int
   */
  public static $ttl=300;
  
  /**
   * Whether encryption is enabled.
   * 
   * FIXME: Things will currently break if encryption is not enabled.
   * 
   * @var bool
   */
  public $useEncryption = true;
  
  /**
   * Application-specific secret password. Override in app.ini. 
   * 
   * @var string
   */
  public $password='change_me';

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
    $this->listen('onPageLoad');

  }
  
  /**
   * Apply options.
   * 
   * @param array $options
   * 		Options to apply.
   */
  public function applyOptions ($options=array()) {
  
    if (isset($options['mod_form_ttl'])) {
      $this->ttl = $options['mod_form_ttl'];
    }
    if (isset($options['mod_form_crypto_enabled'])) {
      $this->useEncryption = $options['mod_form_crypto_enabled'];
    }
    if (isset($options['mod_form_crypto_password'])) {
      $this->password = $options['mod_form_crypto_password'];
    }
    
  }
  
  /**
   * parseNode
   *
   * Event handler for parseNode events.
   *
   * @param RedView_Event $event
   * 		Event object
   */
  public function parseNode(RedView_Event $event) {

    /**
     * @var RedView_Parser
     */
    $parser   = $event->sender;
    $node     = &$parser->currentNode;
    $tag      = null;
    
    switch ($node->nodeName) {

      case 'form':
        $tag = new RedView_Mod_Form_Tag_Form();
        break;
      case 'field':
        $tag = new RedView_Mod_Form_Tag_Field();
        break;
      default:
        return;
    }
      
    $attributes = array();
    if ($node->hasAttributes()) {
      foreach ($node->attributes as $attribute) {
        $attributes[$attribute->name] = $attribute->value;
      }
    }
    
    $tag->parser   = $parser;
    $tag->attribs  = $attributes;
    
    $tag->markup();

  }
  
  /**
   * parseNode
   *
   * Event handler for onPageLoad events.
   *
   * @param RedView_Event $event
   * 		Event object
   */
  public function onPageLoad (RedView_Event $event) {
    
        // if no action was present, return.
    if (!isset($_REQUEST['_rv:data'])) return;
    
    $data = $_REQUEST['_rv:data'];
    $sid = session_id();
    $code = substr($data, 0, 2);
    $data = substr($data, 2, strlen($data)-2);
    
    $decrypted=$this->decrypt($data,  $code . substr(session_id(), 0, 6));
    $a = unserialize($decrypted) or die ("Your session has expired!");
    
    list ($view, $method, $session, $expire) = $a;
    
    if (($session != $sid) || ($expire < time())) {
      $this->end(); 
    }
    try {
      $c = new ReflectionClass($view); 
      $m = new ReflectionMethod($view, $method); 
    }
    catch (ReflectionException $e) { 
      $this->end(); 
    }
      
    if (!($m->isPublic() && !$m->isStatic() && $c->isSubclassOf('RedView_View'))) {
      $this->end();
    }
    
    $class = get_class($view);
    
    @$_SESSION['_rv']['fields']         || $_SESSION['_rv']['fields'] = array();
    @$_SESSION['_rv']['fields'][$class] || $_SESSION['_rv']['fields'][$class] = array();
    $_SESSION['_rv']['fields'][$class] = array_merge($_SESSION['_rv']['fields'][$class], $_REQUEST);
    
    $result = call_user_func(array($view, $method));
    
    $this->tools->router->end();
    
  }
  


  /**
   * Serialize and encrypt an object, one of its methods, and some additional data.
   * 
   * We resurrect the object and call the method when a form post is
   * made from the corresponding view.
   * 
   * @param Object $obj
   * @param string $method
   * 		name of method
   */
  public static function serializeCallbackObject($obj, $method) {
    $code=substr(base_convert(mt_rand(33, 1024), 10, 32), 0, 2);
    return $code . $this->encrypt(serialize(
      array($obj, $method, session_id(), time() + self::$ttl)
    ),  $code . substr(session_id(), 0, 6));
  } 

  
  /**
   * Encrypt some text
   * 
   * @param string $text
   * 		Text to encrypt
   * 
   * @param string $initVector
   * 		Initialization vector
   * 
   * @return string
   * 		Base-64 encoded encrypted text
   */
  protected function encrypt ($text, $initVector) {
    if (!$this->useEncryption) return $text;
    return openssl_encrypt($text, 'DES3', $this->password, false, $initVector);
  }
  
  /**
   * Decrypt base64 encoded encrypted text
   * 
   * @param string $text
   * 		Base64-encoded string to decrypt
   * 
   * @param string $initVector
   * 		Initialization vector
   * 
   * @return string
   * 		Decrypted text
   */
  protected function decrypt ($text, $initVector) {
    if (!$this->useEncryption) return $text;
    return openssl_decrypt($text, 'DES3', $this->password, false, $initVector);
  }
  
  
}


