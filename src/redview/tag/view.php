<?php
/*
 class RedView_Tag_View extends RedView_Tag_AClassTag {

 public $view;

 public static function register ($parser) {
 $parser->register('r:view', __CLASS__);
 }
 public function open () {

 foreach ($this->attribs as $k=>$v) {
 $this->view->$k = $v;
 }

 $this->view->beforeRender();

 }

 public function close () {

 $this->view->render();

 $this->view->afterRender();

 $this->view->loadTemplate();

 }


 }
 */

class RedView_Tag_View extends RedView_ATag {

  public static function register ($parser) {
    $parser->register('r:view', __CLASS__);
  }

  public function markup (RedView_Parser $parser) {

    $doc = $parser->currentDocument;
    $node = $parser->currentNode;

    $pi = null;

    if (isset($this->attribs['template'])) {
      $pi = $doc->createProcessingInstruction('php',
        		"\$this->template=\"{$this->attribs['template']}\"");
    }
    $pi2 = $doc->createProcessingInstruction('php',
        		"\$this->afterRender(); \$this->loadTemplate();");

    if ($pi) $node->parentNode->insertBefore($pi, $node);

    while ($node->childNodes->length) {
      $node->parentNode->insertBefore($node->childNodes->item(0), $node);
    }

    $node->parentNode->replaceChild($pi2, $node);

  }
}


