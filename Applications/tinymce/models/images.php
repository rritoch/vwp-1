<?php

VWP::RequireLibrary('vwp.net');

VNet::RequireClient('http');


class TinyMCE_Model_Images extends VModel {
	
	const IMAGELISTNS = 'http://standards.vnetpublishing.com/schemas/vwp/2011/03/ImageList';
	
	function getList($url) 
	{
          
          $HTTPClient =& VHTTPClient::getInstance();
           
           
          // Fetch web document using http GET method
           
          $data = $HTTPClient->wget($url);

          if (VWP::isWarning($data)) {
              return $data;
          }
          
          $doc = new DOMDocument;
          
          VWP::noWarn(true);
          $r = @ $doc->loadXML($data);
          VWP::noWarn(false);

          if (!$r) {
              return VWP::raiseWarning('Invalid File List',__CLASS__,null,false);
          }
          
          $images = $doc->getElementsByTagNameNS(self::IMAGELISTNS,'image');
          
          $image_list = array();
          $len = $images->length;
          
          for($idx=0;$idx<$len;$idx++) {
              $node = $images->item($idx);
              $value = '';
              $cnodes = $node->childNodes;
              $l2 = $cnodes->length;
              for($i=0; $i<$l2; $i++) {
          	      $item = $cnodes->item($i);
           	      if ($item->nodeType == XML_TEXT_NODE) {
           		      $value .= (string)$item->data;
           	      }
              }              
              $name = (string)$node->getAttribute('name');
              if (!empty($name)) {
                  $image_list[$name] = $value;
              }
          }
          
          // Return result
           
          return $image_list;    
		
	}
}