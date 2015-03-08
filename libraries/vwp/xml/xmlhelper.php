<?php

/**
 * Virtual Web Platform - XML Helper
 *  
 * This file provides XML Helper functions
 *        
 * @package VWP
 * @subpackage Libraries.XML  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch 2011 - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

/**
 * Require Network Support
 */

VWP::RequireLibrary('vwp.net');

/**
 * Require HTTP Client
 */

VNet::RequireClient('http');


/**
 * Virtual Web Platform - XML Helper
 *  
 * This class provides XML Helper functions
 *        
 * @package VWP
 * @subpackage Libraries.XML  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch 2011 - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

class VXMLHelper extends VObject 
{

	/**
	 * Same Namespace
	 * 
	 * @param string $ns1 Namespace
	 * @param string_type $ns2 Namepsace 
	 * @return boolean True if namespaces are the same
	 * @access public
	 */
	
    public static function sameNamespace($ns1,$ns2) 
    {    
        return rtrim($ns1,'/') == rtrim($ns2,'/');
    }

    /**
     * Get Prefix List
     * 
     * @param object $node Node
     * @access public
     */
    
    public static function getPrefixList($node) 
    {
        $prefixList = array();
        
        $tagName = $node->nodeName;
        $parts = explode(':',$tagName);
        if (count($parts) > 1) {
         array_pop($parts);
         $prefix = implode(':',$parts);
         $prefixList[$prefix] = true;
        }
        
        for($i=0; $i < $node->attributes->length; $i++) {
            $attrib = $node->attributes->item($i);
            $tagName = $node->nodeName;
                        
            $parts = explode(':',$tagName);
            if (count($parts) > 1) {
             array_pop($parts);
             $prefix = implode(':',$parts);
             $prefixList[$prefix] = true;
            }
        }
        
        for($i=0; $i < $node->childNodes->length; $i++) {
            $childNode = $node->childNodes->item($i);
            if ($childNode->nodeType == XML_ELEMENT_NODE) {
                $childPrefixes = self::getPrefixList($childNode);
                foreach($childPrefixes as $prefix) {
                    $prefixList[$prefix] = true;
                }            
            }
        }
                                        
        return array_keys($prefixList);
    }
    
    /**
     * Encode string to XML data
     * 
     * @param string $txt Source text
     * @return string Encoded text
     * @access public
     */
              
    public static function xmlentities($txt) 
    {
       $str = $txt;
       $str = str_replace("&","&amp;",$str);
       $str = str_replace("<","&lt;",$str);
       $str = str_replace(">","&gt;",$str);
       $str = str_replace("\"","&quot;",$str);
       return $str;  
    }
    
    /**
     * Fetch a document
     * 
     * @param string $url URL
     * @return string|object Document on success, error or warning otherwise
     * @access public
     */
    
    public static function fetch($url) 
    {
    
        if (v()->filesystem()->path()->isAbsolute($url)) {
            $src = v()->filesystem()->file()->read($url);   
        } else {
            $httpClient =& VHTTPClient::getInstance();
            $src = $httpClient->wget($url);
        }

        if (VWP::isWarning($src)) {
            return $src;
        }
                            
        $doc = new DomDocument;
        VWP::noWarn(true);
        $r = $doc->loadXML($src);
        VWP::noWarn(false);
        if (!$r) {
            return VWP::raiseWarning("Invalid document received from $url.",'VXMLHelper::fetch',null,true);
        }
        return $doc;    
    }  
  
    // end class VXMLHelper
}
