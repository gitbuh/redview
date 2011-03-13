<?php
/**
 *   RedView Action. Provides form submission handling.
 */
class RedView_Action {
  /**
   * Time to live, in seconds.
   * 
   * After the time expires, the form post won't be accepted.
   * 
   * @var int
   */
  public $ttl=300;

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
    $c = get_called_class();
    $o = new $c();
    $code=substr(base_convert(mt_rand(33, 1024), 10, 32), 0, 2);
    return $code . RedView::encrypt(serialize(
      array($obj, $method, session_id(), time() + $o->ttl)
    ),  $code . substr(session_id(), 0, 6));
  } 
  
  /** 
   * Handle a form post if serialized callback object is present in the post data.
   */
  public function handle () {
    // if no action was present, return.
    if (!isset($_REQUEST['_rv:data'])) return;
    $data = $_REQUEST['_rv:data'];
    $sid = session_id();
    $code = substr($data, 0, 2);
    $data = substr($data, 2, strlen($data)-2);
    
    $decrypted=RedView::decrypt($data,  $code . substr(session_id(), 0, 6));
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
      
    if (!($m->isPublic() && !$m->isStatic() && $c->implementsInterface('RedView_IRemote'))) {
      $this->end();
    }
    
    $class = get_class($view);
    
    @$_SESSION['_rv']['fields']         || $_SESSION['_rv']['fields'] = array();
    @$_SESSION['_rv']['fields'][$class] || $_SESSION['_rv']['fields'][$class] = array();
    $_SESSION['_rv']['fields'][$class] = array_merge($_SESSION['_rv']['fields'][$class], $_REQUEST);
    
    $result = call_user_func(array($view, $method));
    
    $this->end();
  }
  
  /**
   * Redirect to where the form was posted from 
   * and optionally set a slot to a value.
   * 
   * @param string $slot
   * 		Slot key to set
   * 
   * @param mixed $value
   * 		Value to set
   */
  public function end ($slot=null, $value='') {
    if ($slot) RedView::setSlot($slot, $value);
    $args = RedView::args();
    $path = trim(implode('/', $args), '/');
    RedView::redirect("/$path/");
  }
  
}

