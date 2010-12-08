<?php

class RedView_Xml {

  /** 
      convert to SimpleXML node collection 
  */
  public static function toXml ($string) { 
    $doc = new DOMDocument();
    $root=$doc->createElement('fakeroot');
    $frag=$doc->createDocumentFragment();
    $frag->appendXML($string);
    $root->appendChild($frag);
    $doc->appendChild($root);
    $xml = simplexml_import_dom($doc);
    return $xml->children();
  }
  
  /** 
      convert from SimpleXML to text 
  */
  public static function fromXml ($xml) {
  
    $content='';
    
    foreach ($xml as $i=>$e) {
      $content.=$e->asXML();
    }
    
    return $content;
    
  }
  
  
}


