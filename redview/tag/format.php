<?php

class RedView_Tag_Format extends RedView_ATag {

  public static function register ($parser) {
    $parser->register('r:format', __CLASS__);
  }

  public function markup (RedView_Parser $parser) {

    $dom = $parser->currentDocument;
    $node = $parser->currentNode;

    $xml=$this->innerXml($parser);

    $pi = $dom->createProcessingInstruction('php', "echo <<<RV_HEREDOC\n$xml\nRV_HEREDOC;\n");

    $node->parentNode->replaceChild($pi, $node);
  }

}


