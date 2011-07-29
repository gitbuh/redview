<?php

class RedView_Core_Router extends RedView_Core {

  public $pagePath='page';
  
  public $viewPath='view';
  
  public $defaultPage='home';

  public $args;
  
  public $output='';
  
  public $requestUrl='';
  
  public $state;

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
  
    $this->state = RedView::STATE_PRELOAD;

    if (!isset($_REQUEST['_rv:page'])) $_REQUEST['_rv:page']="";
    
    $this->requestUrl = $_REQUEST['_rv:page'];
    
    $this->sendEvent('onRequest');

    $defaultPage=$this->defaultPage.'/';
    $pagePath=$this->pagePath;
    $viewPath=$this->viewPath;

    if (trim($this->requestUrl,'/').'/' == $defaultPage) $this->redirect('', true);

    if (!$this->requestUrl) $this->requestUrl=$defaultPage;

    // make sure the URL ends in a slash, otherwise form posts won't work right
    if ($this->requestUrl{strlen($this->requestUrl)-1}!='/') {
      $this->redirect($this->requestUrl.'/', true);
    }

    // the entire URL after site root (where index.php lives) is in $_REQUEST['page']
    $page = trim($this->requestUrl,'/');

    $path;

    // look for a file "./pages/$page.html", or use "./pages/home.html"
    $lastPage = '';
    $path = '';
    $page .= '/';
    while ($page && $lastPage != $page) {
      $lastPage = $page;
      if (strpos($page,'..')===false && file_exists("$viewPath/$pagePath/$page.html")) {
        $path =  "$pagePath/$page.html";
        $view = "$pagePath/$page";
        break;
      }
      $a = explode('/', $page);
      array_splice($a, -1);
      $page = implode('/', $a);
    }

    // requested a non-existing top level path; redirect to front page
    if (!$path || !file_exists("$viewPath/$path")) $this->redirect($defaultPage, true);

    // make argv
    $args=explode('/', trim(substr($this->requestUrl, strlen($page)), '/'));
    $args[0] ? array_unshift($args, $page) : $args[0]=$page;

    $this->args = $args;

    $this->sendEvent('onPageLoad');
    
    $this->state = RedView::STATE_LOAD;

    // Load the cached view/controller
    ob_start();
    echo $this->tools->cache->load($view);
    $out=ob_get_clean();

    $this->output = &$out;
    
    $this->sendEvent('onFilter');
    
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
    if ($this->state == RedView::STATE_PRELOAD) {
      $path = trim(implode('/', $this->args), '/');
      $this->redirect("/$path/");
    } else {
      $this->redirect("/");
    }
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
    // .  $this->getUrlSub();
  }

  public function getUrlSub() {
    if (!isset($_REQUEST['_rv:page']) || !$_REQUEST['_rv:page']) return $_SERVER['REQUEST_URI'];
    return substr($_SERVER['REQUEST_URI'], 0 , -strlen($_REQUEST['_rv:page']));
  }

}
