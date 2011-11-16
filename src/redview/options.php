<?php

/**
 * Default Options
 */
class RedView_Options {
  
// RedView looks for this file and applies settings from it.

//    [cache]

// Turn this on during production.

  public $cache_enabled     = true;

// RedView needs to store cache files somewhere.
// Somewhere in /tmp/ makes sense during development.

  public $cache_path    = '/tmp/rv-cache';

//    [parser]

// needs to be the same as the router's viewPath.

  public $view_path     = 'view';

//    [router]

// needs to be directly inside the viewPath.

  public $view_page_path     = 'page';

// a page named (defaultPage)'.html' needs to be 
// directly inside the pagePath.

  public $view_page_default = 'home';
  
  
  public $view_class_default = 'RedView_View';
  
//    [action]

// Time to live. Trying to post a form after this many seconds 
// will result in an error. Not the same as session timeout.

  public $mod_form_ttl         = 24000;     // 4 hours

//    [crypto]

// Set `enabled` to `true` to encrypt form callback data 
// and possibly other things in the future.
// (turn it off for debugging only).

  public $mod_form_crypto_enabled = true;

// Your password should be made unique and kept secrect.

  public $mod_form_crypto_password='change_me';
}
