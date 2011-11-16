<?php
/**
 *
 * Responsible for caching a parsed view markup file to a php file.
 *
 */
class RedView_Core_Cache extends RedView_Core {

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
  public $cachePath = '/tmp/rv-cache';
  /**
   * Default view class (controller) to load when no .php file is present.
   * 
   * @var string
   */
  public $defaultView = 'RedView_View';
  /**
   * Text being written to cache (for onCache event).
   * 
   * @var string
   */
  public $output = '';

  /**
   * Apply options.
   * 
   * @param RedView_Options $options
   * 		Options to apply.
   */
  public function applyOptions (RedView_Options $options=null) {
    
    if (!$options) return;
    
    if (isset($options->cache_enabled)) {
      $this->cacheOn = $options->cache_enabled;
    }
    if (isset($options->cache_path)) {
      $this->cachePath = $options->cache_path;
    }
    if (isset($options->view_class_default)) {
      $this->defaultView = $options->view_class_default;
    }
    
  }
  
  /**
   * Load a cached view/controller.
   *
   * @param string $view
   * 		path to the view to load.
   */
  public function load ($view) {
    $file = "$view.html";
    if (isset($_SESSION['_rv']) && isset($_SESSION['_rv']['slots']) && is_array($_SESSION['_rv']['slots'])) {
      foreach ($_SESSION['_rv']['slots'] as $k=>$v) RedView::$slots[$k] = $v;
    }
    
  	$loader = $this->findLoader($file);
  	
    require $loader;
    
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
    $realFile = "{$this->tools->parser->viewPath}/$file";
    $sha1     = sha1($realFile);
    $cachedir = $this->cachePath;
    $cache    = $cachedir . '/' . basename($realFile) . ".$sha1";
    $loader   = "$cache.loader.php";
    
    if (!$this->cacheOn || !file_exists($loader) || filemtime($loader)<filemtime($realFile)) {
    	
  	  RedView::dbg_timer_start("rewriting php cache $file");
      if (!file_exists($cachedir)) mkdir($cachedir);
      $this->output = $this->tools->parser->parseFile($realFile);
      $this->sendEvent('onCache');
      file_put_contents("$cache.php", $this->output);
      $this->writeLoader($cache, $realFile);
  	  RedView::dbg_timer_end();
  	  
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
    file_put_contents("$cache.loader.php", 
    	"<?php /* $classFile */ ".
        (file_exists($classFile) ? "require_once '$classFile';" : "")." 
        \$view = new $class(); 
      	if (!isset(\$params)) \$params = array();
        foreach (\$params as \$k=>\$v) \$view->set(\$k, \$v); 
        unset (\$params);
        \$view->beforeRender(); 
        \$view->includeFile('$cache.php');
        \$view->afterRender(); 
    ");
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

