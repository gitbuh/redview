<?php

abstract class RedView_ATag {

  /**
      Tag (node) name
      @var string 
  */
  public $name;
  /**
      Tag attributes
      @var array<string=>string> 
  */
  public $attribs=array();

  /**
      View object for a class tag.
      Should be null when writing to cache.
      @var RedView_View
  */
  public $view;
  
  public function __construct ($name=null, $attribs=null, $view=null) {
    $this->name=$name;
    $this->attribs=$attribs;
    $this->view=$view;
  }
  
  /**
      Register with the parser
      @return string name of tag to rewrite
  */
  public static function register ($parser) { 
    return '';
  }
  
  /**
      Manipulate XML when writing to cache.
      Called by $this->markup.
  */
  public function markup ($parser) {
  
  }
  
  /**
      Put a node before this node when writing to cache.
      Called by $this->markup.
  */
  public function prefixNode ($parser) {
  }
  
  /**
      Put a node after this node when writing to cache
      Called by $this->markup.
  */
  public function suffixNode ($parser) {
  }
  
  
}

