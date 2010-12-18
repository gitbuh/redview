<?php
/**
    RedView Action. Provides end-to-end form submission handling.
*/
class RedView_Action {

  public $ttl=300;  // five minutes

  public static function encodeRequest($obj, $method) {
    $c = get_called_class();
    $o = new $c();
    $code=$o->createChars();
    return $code . RedView::encrypt(serialize(
      array($obj, $method, session_id(), time() + $o->ttl)
    ), $o->createInitVector($code));
  } 
  
  /** 
      handle an action
   */
  public function handle () {
    if (!($data = @$_REQUEST['_rv:data'])) return;
    $sid = session_id();
    $code = substr($data, 0, 2);
    $data = substr($data, 2, strlen($data)-2);
    
    $decrypted=RedView::decrypt($data, $this->createInitVector($code));
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
      redirect and optionally set a slot
   */
  public function end ($slot=null, $value='') {
    if ($slot) RedView::set($slot, $value);
    $args = RedView::args();
    $path = trim(implode('/', $args), '/');
    RedView::redirect("/$path/");
  }
  
  
  /** 
   */
  public function createInitVector ($code) {;
    return $code . substr(session_id(), 0, 6);
  }
  /** 
   */
  public function createChars () {
    return substr(base_convert(mt_rand(33, 1024), 10, 32), 0, 2);
  }
  
  
}

