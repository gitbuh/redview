<?php

class RedView_Mod_Markup_Tag_View extends RedView_Mod_Markup_Tag {

  public function markup (RedView_Core_Parser $parser) {

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


