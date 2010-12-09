<?php
/**
    RedView Action. Provides end-to-end form submission handling.
*/
class RedView_Action {

  public $ttl=300;  // five minutes

  public static function encodeRequest($method, $className) {
    $c = get_called_class();
    $o = new $c();
    $sid = session_id();
    return RedView::encrypt(serialize(
      array($method, $className, $_SERVER['REQUEST_URI'], $sid, time() + $o->ttl)
    ), substr($sid, 8));
  } 
  
  /** 
      handle an action
   */
  public function handle () {
    if (!($e = @$_REQUEST['_rv:data'])) return;
    $sid = session_id();
    $a = unserialize(RedView::decrypt($e, substr($sid, 8)))
      or die ('Your session has expired!');
    
    list ($method, $class, $path, $session, $expire) = $a;
    
    $_REQUEST['_rv:path'] = $path;
    
    if (($session != $sid) || ($expire < time())) {
      RedView::redirect($path); 
    }
    try {
      $c = new ReflectionClass($class); 
      $m = new ReflectionMethod($class, $method); 
    }
    catch (ReflectionException $e) { 
      RedView::redirect($path); 
    }
      
    if (!($m->isPublic() && $m->isStatic() && $c->implementsInterface('RedView_IRemote'))) {
      RedView::redirect($path);
    }
    
    @$_SESSION['_rv']['fields']         || $_SESSION['_rv']['fields'] = array();
    @$_SESSION['_rv']['fields'][$class] || $_SESSION['_rv']['fields'][$class] = array();
    $_SESSION['_rv']['fields'][$class] = array_merge($_SESSION['_rv']['fields'][$class], $_REQUEST);
    
    $result = call_user_func(array($class, $method));
    
    RedView::redirect($path);
  }
  
  
  /** 
      redirect and optionally set a slot
   */
  public function end ($slot=null, $value='') {
    $path = @$_REQUEST['_rv:path'];
    $path || $path = '/';
    if ($slot) RedView::set($slot, $value);
    RedView::redirect($path);
  }
  
}

