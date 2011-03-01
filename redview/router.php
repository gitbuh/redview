<?php

class RedView_Router extends RedView_ABase {

  public $viewDir='view';
  public $pageDir='page';
  public $defaultPage='home';

  public $args;

  /**
   base URL of current site
   */
  public function loadPage() {
    $defaultPage=$this->defaultPage.'/';
    $pageDir=$this->pageDir;
    $viewDir=$this->viewDir;

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
      if (strpos($page,'..')===false && file_exists("$viewDir/$pageDir/$page.html")) {
        $path =  "$pageDir/$page.html";
        break;
      }
      $a = explode('/', $page);
      array_splice($a, -1);
      $page = implode('/', $a);
    }

    // requested a non-existing top level path; redirect to front page
    if (!$path || !file_exists("$viewDir/$path")) $this->redirect($defaultPage, true);

    // make argv
    $args=explode('/', trim(substr($_REQUEST['_rv:page'], strlen($page)), '/'));
    $args[0] ? array_unshift($args, $page) : $args[0]=$page;
    $_REQUEST['_rv:argv'] = $args;

    // handle any post actions if appropriate
    $this->tools->action->handle();

    ob_start();
    // parse the file with RedView
    echo $this->tools->parser->parse($path);
    $out=ob_get_clean();
    $doc = new DOMDocument();

    // if ($out==''){print_r(get_defined_vars());}

    $doc->loadHTML($out);
    echo $doc->saveHTML();
  }


  /**
   redirect browser to another URL
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
   base URL of current site
   */
  public function getUrlBase() {
    return 'http'
    .(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 's' : '')
    .'://'.$_SERVER['SERVER_NAME']
    .($_SERVER['SERVER_PORT'] != 80 ? ':'.$_SERVER['SERVER_PORT'] : '');
  }
  /**
   path of current url (as shown in browser)
   */
  public function getUrlPath() {
    return $this->getUrlBase().$this->getPathFromString($_SERVER['REQUEST_URI']);
  }

  /**
   path of current script (before any url rewriting)
   */
  public function getScriptPath() {
    return $this->getUrlBase().$this->getPathFromString($_SERVER['SCRIPT_NAME']);
  }

  /**
   extract path from string
   */
  public function getPathFromString($file) {
    preg_match_all('/.*\//', $file, $match, PREG_PATTERN_ORDER);
    return $match[0][0];
  }

}
