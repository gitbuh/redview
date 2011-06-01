<?php

class RedView_Core_Router extends RedView_Core {

  public $pagePath='page';
  
  public $viewPath='view';
  
  public $defaultPage='home';

  public $args;

  /**
   * Apply options.
   * 
   * @param RedView_Options $options
   * 		Options to apply.
   */
  public function applyOptions (RedView_Options $options=null) {
    
    if (!$options) return;
  
    if (isset($options->view_path)) {
      $this->viewPath = $options->view_path;
    }
    if (isset($options->view_page_path)) {
      $this->pagePath = $options->view_page_path;
    }
    if (isset($options->view_page_default)) {
      $this->defaultPage = $options->view_page_default;
    }
    
  }
  
  /**
   base URL of current site
   */
  public function loadPage() {

    $this->sendEvent('beforePageLoad');

    $defaultPage=$this->defaultPage.'/';
    $pagePath=$this->pagePath;
    $viewPath=$this->viewPath;

    if (!isset($_REQUEST['_rv:page'])) $_REQUEST['_rv:page']="";

    if (trim($_REQUEST['_rv:page'],'/').'/' == $defaultPage) $this->redirect('', true);

    if (!$_REQUEST['_rv:page']) $_REQUEST['_rv:page']=$defaultPage;

    // make sure the URL ends in a slash, otherwise form posts won't work right
    if ($_REQUEST['_rv:page']{strlen($_REQUEST['_rv:page'])-1}!='/') {
      $this->redirect($_REQUEST['_rv:page'].'/', true);
    }

    // the entire URL after site root (where index.php lives) is in $_REQUEST['page']
    $page = trim($_REQUEST['_rv:page'],'/');

    $path;

    // look for a file "./pages/$page.html", or use "./pages/home.html"
    $lastPage = '';
    $path = '';
    $page .= '/';
    while ($page && $lastPage != $page) {
      $lastPage = $page;
      if (strpos($page,'..')===false && file_exists("$viewPath/$pagePath/$page.html")) {
        $path =  "$pagePath/$page.html";
        break;
      }
      $a = explode('/', $page);
      array_splice($a, -1);
      $page = implode('/', $a);
    }

    // requested a non-existing top level path; redirect to front page
    if (!$path || !file_exists("$viewPath/$path")) $this->redirect($defaultPage, true);

    // make argv
    $args=explode('/', trim(substr($_REQUEST['_rv:page'], strlen($page)), '/'));
    $args[0] ? array_unshift($args, $page) : $args[0]=$page;
    $_REQUEST['_rv:argv'] = $args;

    $this->args = $args;

    $this->sendEvent('onPageLoad');

    // Load the cached view/controller
    ob_start();
    echo $this->tools->cache->load($path);
    $out=ob_get_clean();

    echo $out;
  }


  /**
   * Redirect
   *
   * Redirect browser to another URL. Immediately
   * terminates the current page lifecycle.
   *
   * @param string $url
   *		Absolute or relative URL to redirect to.
   *
   * @param bool $permanent
   * 		Serve '301 Moved Permanently' if true
   */
  public function redirect ($url, $permanent=false) {

    $event = $this->sendEvent('onRedirect');

    if ($event && $event->isCanceled) return;

    if (!strpos($url, '://')) {
      $url = trim($url,'/');
      $url = $this->getUrlBase().'/'.$url.($url?'/':'');
    }
    if ($permanent) header("HTTP/1.1 301 Moved Permanently");
    header("Location: $url");
    exit();
  }


  /**
   * End
   *
   * Redirect to current page and optionally
   * set a slot to a value.
   *
   * @param string $slot
   * 		Slot key to set
   *
   * @param mixed $value
   * 		Value to set
   */
  public function end ($slot=null, $value='') {
    if ($slot) RedView::setSlot($slot, $value);
    $path = trim(implode('/', $this->args), '/');
    $this->redirect("/$path/");
  }

  /**
   * Get URL base
   *
   * get base URL of current site
   *
   * @return string
   */
  public function getUrlBase() {
    return 'http'
    .  (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 's' : '')
    .  '://'.$_SERVER['SERVER_NAME']
    .  ($_SERVER['SERVER_PORT'] != 80 ? ':'.$_SERVER['SERVER_PORT'] : '');
  }

  /**
   * Get URL path
   *
   * @return string
   * 		path of current url (as shown in browser)
   */
  public function getUrlPath() {
    return $this->getUrlBase().$this->getPathFromString($_SERVER['REQUEST_URI']);
  }

  /**
   * Get script path
   *
   * @return string
   * 		path of current script (before any url rewriting)
   */
  public function getScriptPath() {
    return $this->getUrlBase().$this->getPathFromString($_SERVER['SCRIPT_NAME']);
  }


  protected function getPathFromString($file) {
    preg_match_all('/.*\//', $file, $match, PREG_PATTERN_ORDER);
    return $match[0][0];
  }

}
