<?php

set_include_path(get_include_path().PATH_SEPARATOR .dirname(__FILE__)); 
spl_autoload_register(function($class){return spl_autoload(str_replace('_', '/', $class));});

/**
    RedView facade class. Provides single static entrypoint to the RedView framework.
*/
class RedView {

  public static $toolbox=null;

  /** 
      init
      
      start the RedView page lifecycle.
      
      @param RedView_Toolbox $toolbox optional custom toolbox
      @return RedView_Toolbox
  */
  public static function init (RedView_Toolbox $toolbox=null) {
    session_start();
    self::$toolbox = $toolbox ? $toolbox : new RedView_Toolbox();
    return self::$toolbox;
  }

  /** 
      setup
      
      start the RedView page lifecycle.
      
      @return mixed
  */
  public static function setup() {
    if (!self::$toolbox) self::init();
    self::$toolbox->router->loadPage();
  }
  
  /** 
      args
      
      pages get an argv from the url like this: 
      
         file path is:    (...pagedir)/admin/edit.php
      user browses to:    http: //example.com/admin/edit/ahaha/ohohoh/
       args() returns:    Array ( [0] => admin/edit [1] => ahaha [2] => ohohoh )
      
      @param int $index optional index of arg to return
      @return array of page args, or arg at requested index.
  */
  public static function args ($index=-1) {
    return $index>-1 ? @$_REQUEST['_rv:argv'][$index] : $_REQUEST['_rv:argv'];
  }
  
  /** 
      set
      
      set a slot to a value. 
      
      The setting will be retained in the session 
      until the next page lifecycle is complete.
      
      @return mixed
  */
  public static function set ($k, $v) {
    RedView_Tag_Slot::$slots[$k] = $v;
    return $_SESSION['_rv']['slots'][$k] = $v;
  }
  
  public static function get ($k) {
    return RedView_Tag_Slot::$slots[$k];
  }
  
  
  /** 
      redirect
      
      redirect to another page.
      
      @param string $url relative (to pagedir) path of url.
      @return mixed
  */
  public static function redirect ($url) {
    if (!self::$toolbox) self::setup(); 
    return self::$toolbox->router->redirect($url);
  }
  
  /** 
      end
      
      end a view action, optionally setting a slot to a value. 
      
      @param string $slot optional slot to set
      @param string $value optional value to put in slot
      @return mixed
  */
  public static function end ($k, $v) {
    return self::$toolbox->action->end($k, $v);
  }
  
  public static function parse ($file) {
    if (!self::$toolbox) self::setup(); 
    if (is_array(@$_SESSION['_rv']['slots'])) {
      foreach ($_SESSION['_rv']['slots'] as $k=>$v) self::set($k,$v);
    }
    self::$toolbox->parser->parse($file);
    unset($_SESSION['_rv']['slots']);
  }
  
  public static function toXml ($string) { 
    return RedView_Xml::toXml($string);
  }
  
  public static function fromXml ($xml) {
    return RedView_Xml::fromXml($xml);
  }
  
  public static function encrypt ($text, $iv=null) {
    return self::$toolbox->crypto->encrypt($text, $iv);
  }
  
  public static function decrypt ($text, $iv=null) {
    return self::$toolbox->crypto->decrypt($text, $iv);
  }
  
  
  
}


