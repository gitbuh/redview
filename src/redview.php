<?php

set_include_path(get_include_path().PATH_SEPARATOR .dirname(__FILE__)); 
spl_autoload_register(function($class){return spl_autoload(str_replace('_', '/', $class));});

/**
    RedView facade class. Provides single static entrypoint to the RedView framework.
*/
class RedView extends RedView_View {

 /**
  * @var RedView_Toolbox $tools
  */
  public static $tools=null;

  /**
   * @var array $slots
   */
  public static $slots=array();
  
  /** 
      init
      
      start the RedView page lifecycle.
      
      @param RedView_Toolbox $tools optional custom tools
      @return RedView_Toolbox
  */
  public static function init (RedView_Toolbox $tools=null) {
    session_start();
    self::$tools = $tools ? $tools : new RedView_Toolbox();
    return self::$tools;
  }

  /** 
      setup
      
      start the RedView page lifecycle.
      
      @return mixed
  */
  public static function setup() {
    if (!self::$tools) self::init();
    self::$tools->router->loadPage();
  }
  
  /** 
      args
      
      pages get an argv from the url like this: 
      
         file path is:    (...pagedir)/admin/edit.php
      user browses to:    http: //example.com/admin/edit/ahaha/ohohoh/
       args() returns:    Array ( [0] => admin/edit [1] => ahaha [2] => ohohoh )
      
      @param int $index 
      		optional index of arg to return
      		
      @return mixed 
      		Array of page args, or arg at requested index.
  */
  public static function args ($index=-1) {
    if (!self::$tools) self::init();
    $args = self::$tools->router->args;
    return $index>-1 ? $args[index] : $args;
  }
  
  /** 
      set
      
      set a slot to a value. 
      
      The setting will be retained in the session 
      until the next page lifecycle is complete.
      
      @return mixed
  */
  public static function setSlot ($k, $v) {
    self::$slots[$k] = $v;
    $_SESSION['_rv']['slots'][$k] = $v;
  }
  
  public static function getSlot ($k) {
    if (isset($_SESSION['_rv']['slots'][$k])) 
      return $_SESSION['_rv']['slots'][$k];
    return self::$slots[$k];
  }
  
  
  /** 
      redirect
      
      redirect to another page.
      
      @param string $url relative (to pagedir) path of url.
      @return mixed
  */
  public static function redirect ($url) {
    if (!self::$tools) self::setup(); 
    return self::$tools->router->redirect($url);
  }
  
  /** 
      end
      
      end a view action, optionally setting a slot to a value. 
      
      @param string $slot optional slot to set
      @param string $value optional value to put in slot
      @return mixed
  */
  public static function end ($k, $v) {
    if (!self::$tools) self::setup(); 
    return self::$tools->router->end($k, $v);
  }
  
  public static function load ($file) {
    if (!self::$tools) self::setup(); 
    self::$tools->cache->load($file);
  }
  
  public static function toXml ($string) { 
    return RedView_Xml::toXml($string);
  }
  
  public static function fromXml ($xml) {
    return RedView_Xml::fromXml($xml);
  }
  
  
  
}


