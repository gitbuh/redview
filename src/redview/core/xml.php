<?php

/**
 * XML helper class.
 */
class RedView_Core_Xml extends RedView_Core {

  /**
   * Convert text to a SimpleXML node collection.
   * 
   * @param string $string
   * 		String containing XML markup.
   * 
   * @return SimpleXMLElement
   * 		SimpleXML node collection.
   */
  public function toXml ($string) { 
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
  public function fromXml ($xml) {
    /*
    $content='';
    foreach ($xml as $i=>$e) {
      $content.=$e->asXML();
    }
    return $content;
    */
    
    $out = '';
    
    foreach ($xml as $e) {
      
      $element = dom_import_simplexml($e);
      
      $dom = new RedView_DOMDocument();
      $element = $dom->importNode($element, true);
      $element = $dom->appendChild($element);
      
      $out .= $dom->saveXHTML();
      
    }
    
    return $out;
    
  }
  
  
}


