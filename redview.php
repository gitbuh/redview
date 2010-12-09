<?php

set_include_path(get_include_path().PATH_SEPARATOR .dirname(__FILE__)); 
spl_autoload_register(function($class){return spl_autoload(str_replace('_', '/', $class));});

/**
    RedView facade class. Provides single static entrypoint to the RedView framework.
*/
class RedView {

  public static $toolbox;
  public static $ready=false;

  public static function init () {
    session_start();
    self::$ready=true;
    self::$toolbox || self::$toolbox = new RedView_Toolbox();
  }

  public static function setup() {
    if (!self::$ready) self::init(); 
    self::$toolbox->router->handleAction();
    self::$toolbox->router->loadPage();
  }
  
  public static function set ($k, $v) {
    RedView_Tag_Slot::$slots[$k] = $v;
    $_SESSION['_rv']['slots'][$k] = $v;
  }
  
  public static function parse ($file) {
    if (!self::$ready) self::setup(); 
    if (is_array(@$_SESSION['_rv']['slots'])) {
      foreach ($_SESSION['_rv']['slots'] as $k=>$v) self::set($k,$v);
    }
    self::$toolbox->parser->parse($file);
    unset($_SESSION['_rv']['slots']);
  }
  
  public static function redirect ($url) {
    if (!self::$ready) self::setup(); 
    return self::$toolbox->router->redirect($url);
  }
  
  public static function end ($k, $v) {
    self::$toolbox->router->endAction($k, $v);
  }
  
  public static function toXml ($string) { 
    return RedView_Xml::toXml($string);
  }
  
  public static function fromXml ($xml) {
    return RedView_Xml::fromXml($xml);
  }
  
  public static function encrypt ($text) {
    return self::$toolbox->crypto->encrypt($text);
  }
  
  public static function decrypt ($text) {
    return self::$toolbox->crypto->decrypt($text);
  }
  
  
  
}


