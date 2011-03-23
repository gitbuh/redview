<?php
/**
 *
 * RedView View.
 *
 * Contollers for individual views should inherit from this class,
 * and be placed alongside the corresponding markup files,
 * having the same name except for the extension (.php instead of .html).
 *
 */
class RedView_View {

  /**
   * Template to render after rendering this view.
   * 
   * @var string
   */
  public $template;

  /**
   * Array of variables to pass to the markup file.
   * These variables become local variables in the markup file.
   * 
   * @var array
   */
  protected $_vars = array();

  public function __construct() {
  }

  public function unserialize () {
  }
  
  public function beforeRender () {
  }

  public function afterRender () {
  }

  /**
   * 
   */
  public function loadTemplate ($template=null) {
    $cache=null;
    if (!$template) $template=$this->template;
    if ($template) $cache = RedView::load($template);
    if ($cache) require $cache;
  }

  public function loadCache ($file) {
    extract($this->_vars);
    if ($params) {
      extract($params);
    }
    include $file;
  }

  public function set ($k, $v) {
    $this->_vars[$k]=$v;
  }
  public function get ($k) {
    return $this->_vars[$k];
  }



  /**
   __sleep
   Magic method, called on serialize. Properties named with a leading underscore will not be serialized.
   @return array of names of properties to serialize.
   */
  public function __sleep () {
    $propNames;
    foreach (array_keys(get_object_vars($this)) as $k) if ($k{0}!='_') $propNames[]=$k;
    return $propNames;
  }

  /**
   __wakeup
   Magic method, called on unserialize.
   */
  public function __wakeup () {
    $this->__construct();
    $this->unserialize();
  }

}

