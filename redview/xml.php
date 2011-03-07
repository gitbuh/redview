<?php

/**
 * XML helper class.
 */
class RedView_Xml {

  /**
   * Convert text to a SimpleXML node collection.
   * 
   * @param string $string
   * 		String containing XML markup.
   * 
   * @return SimpleXMLElement
   * 		SimpleXML node collection.
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
   * Convert from SimpleXML to text.
   * 
   * @param SimpleXMLElement $xml
   * 		SimpleXML node collection
   * 
   * @return string
   * 		String containing XML markup.
   */
  public static function fromXml ($xml) {
    $content='';
    foreach ($xml as $i=>$e) {
      $content.=$e->asXML();
    }
    return $content;
  }
  
  
}


