<?php
/**
 *
 * Responsible for caching a parsed view markup file to a php file.
 *
 */
class RedView_Cache extends RedView_ABase {

  /**
   * When turned on, cached files won't be overwritten.
   * 
   * Set to true for production, false for development.
   * 
   * @var bool
   */
  public $cacheOn  = false;
  /**
   * Default location for cached files.
   * 
   * @var string
   */
  public $cacheDir = '/tmp/rv-cache';
  /**
   * Default view class (controller) to load when no .php file is present.
   * 
   * @var string
   */
  public $defaultView = 'RedView_View';

  /**
   * Load a cached view/controller.
   *
   * @param string $file 
   * 		path to the file to load.
   */
  public function load ($file) {
    if (isset($_SESSION['_rv']) && isset($_SESSION['_rv']['slots']) && is_array($_SESSION['_rv']['slots'])) {
      foreach ($_SESSION['_rv']['slots'] as $k=>$v) RedView_Tag_Slot::$slots[$k] = $v;
    }
    require $this->findLoader($file);
    if (isset($_SESSION['_rv']) && isset($_SESSION['_rv']['slots'])) {
      unset($_SESSION['_rv']['slots']);
    }
  }

  /**
   * Find or write a loader for a cached PHP file.
   * 
   * @param string $file
   * @return string path to loader file
   */
  public function findLoader ($file) {
    $realFile = "{$this->tools->parser->viewDir}/$file";
    $sha1     = sha1($realFile);
    $cachedir = $this->cacheDir;
    $cache    = $cachedir . '/' . basename($realFile) . ".$sha1";
    $loader   = "$cache.loader.php";
    if (!$this->cacheOn || !file_exists($loader) || filemtime($loader)<filemtime($file)) {
      if (!file_exists($cachedir)) mkdir($cachedir);
      file_put_contents("$cache.php", $this->tools->parser->parseFile($realFile));
      $this->writeLoader($cache, $realFile);
    }
    return $loader;
  }

  /**
   * Write a loader for a cached PHP file.
   * 
   * @param string $cache
   * @param string $file
   */
  protected function writeLoader ($cache, $file) {
    $classFile = dirname($file) . '/' . implode('', explode('_', basename($file, '.html') . '.php'));
    $class = $this->getClassFromFile($classFile);
    if (!$class) $class = $this->defaultView;
    file_put_contents("$cache.loader.php", "<?php /* $classFile */ ".
        (file_exists($classFile) ? "
        require_once '$classFile';" : "")." 
        \$view = new $class(); 
      	if (!isset(\$_params)) \$_params = array();
        foreach (\$_params as \$_k=>\$_v) \$view->set(\$_k, \$_v); 
        \$view->beforeRender(); 
        \$view->loadCache('$cache.php');");
  }

  /**
   * Get the name of the first class defined in a php file.
   * 
   * @param string $file
   */
  public function getClassFromFile ($file) {
    if (!file_exists($file)) return '';
    $php = file_get_contents ($file);
    $tokens = token_get_all($php);
    $class_token = false;
    foreach ($tokens as $token) if (is_array($token)) {
      if ($token[0] == T_CLASS) {
        $class_token = true;
      } else if ($class_token && $token[0] == T_STRING) {
        return $token[1];
      }
    }
  }


}

