<?php

class RedView_Router {

  public $viewDir='view';
  public $pageDir='page';
  public $defaultPage='home';
  
  /** 
   *base URL of current site
   */
  public function loadPage()
  { 
    $defaultPage=$this->defaultPage.'/';
    $pageDir=$this->pageDir;
    $viewDir=$this->viewDir;

    if (trim($_REQUEST['_rv:page'],'/').'/' == $defaultPage) RedView::redirect('', true);

    if (!$_REQUEST['_rv:page']) $_REQUEST['_rv:page']=$defaultPage;
        
    if ($_REQUEST['_rv:page'] && $_REQUEST['_rv:page']{strlen($_REQUEST['_rv:page'])-1} != '/') {
      RedView::redirect($_REQUEST['_rv:page'].'/', true);
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
      if (strpos($page,'..')===false && file_exists("$viewDir/$pageDir/$page.html")) {
        $path =  "$pageDir/$page.html";
        break;
      }
      $a = explode('/', $page);
      array_splice($a, -1);
      $page = implode('/', $a);
    }

    // parse the file with RedView
    // requested a non-existing top level path; redirect to front page
    if (!$path || !file_exists("$viewDir/$path")) $this->redirect('', true);

    // normal page
    echo RedView::parse($path);
  }
  
  public function handleAction () {
    $action = @$_REQUEST['_rv:action']; 
    $class  = @$_REQUEST['_rv:class']; 
    $path   = @$_REQUEST['_rv:path'];
  
    if (!($action && $class && $path)) return;
    
    try {
      $c = new ReflectionClass($class); 
      $m = new ReflectionMethod($class, $action); 
    }
    catch (ReflectionException $e) { 
      RedView::redirect($path); 
    }
    
    if (!($m->isPublic() && $m->isStatic() && $c->implementsInterface('RedView_IRemote'))) return;
    
    @$_SESSION['_rv']['fields']         || $_SESSION['_rv']['fields'] = array();
    @$_SESSION['_rv']['fields'][$class] || $_SESSION['_rv']['fields'][$class] = array();
    $_SESSION['_rv']['fields'][$class] = array_merge($_SESSION['_rv']['fields'][$class], $_REQUEST);
    
    $result = call_user_func(array($class, $action));
    
    RedView::redirect($path);
    
  }
  
  
  /** 
   *  redirect browser to another URL
   */
  public function endAction ($slot=null, $value='') {
    $path = @$_REQUEST['_rv:path'];
    $path || $path = '/';
    if ($slot) RedView::set($slot, $value);
    RedView::redirect($path);
  }

  
  /** 
   *  redirect browser to another URL
   */
  public function redirect ($url, $permanent=false) {
    
    if (!strpos($url, '://')) {
      $url = trim($url,'/');
      $url = $this->getUrlBase().'/'.$url.($url?'/':'');
    }
    if ($permanent) header("HTTP/1.1 301 Moved Permanently");
    header("Location: $url");
    exit();
  }


  /** 
   *base URL of current site
   */
  public function getUrlBase()
  { 
    return 'http'
    .(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 's' : '')
    .'://'.$_SERVER['SERVER_NAME']
    .($_SERVER['SERVER_PORT'] != 80 ? ':'.$_SERVER['SERVER_PORT'] : '');
  }
  /** 
   *path of current url (as shown in browser)
   */
  public function getUrlPath()
  { 
    return $this->getUrlBase().$this->getPathFromString($_SERVER['REQUEST_URI']);
  }
  
  /** 
   *path of current script (before any url rewriting)
   */
  public function getScriptPath()
  { 
    return $this->getUrlBase().$this->getPathFromString($_SERVER['SCRIPT_NAME']);
  }
  
  /** 
   *  extract path from string
   */
  public function getPathFromString($file)
  { 
    preg_match_all('/.*\//', $file, $match, PREG_PATTERN_ORDER);
    return $match[0][0];
  }
  
}
