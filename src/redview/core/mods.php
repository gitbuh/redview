<?php
/**
 *
 * Responsible for managing modules.
 *
 */
class RedView_Core_Mods extends RedView_Core {

  /**
   * Array of modules
   *
   * @var array
   */
  public $mods = array();
  
  /**
   * Store a module object.
   * 
   * @param string $moduleName
   * 
   * @param RedView_Mod $module
   */
  public function set ($moduleName, RedView_Mod $module) {
    $this->mods[$moduleName] = $module;
  }

  /**
   * Retrieve a stored module object.
   * 
   * @param string $moduleName
   * 
   * @return RedView_Mod
   */
  public function get ($moduleName) {
    return $this->mods[$moduleName];
  }
  


}

