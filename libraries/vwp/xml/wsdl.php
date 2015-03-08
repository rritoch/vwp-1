<?php

/**
 * Virtual Web Platform - WSDL Processing
 *  
 * This file provides WSDL Processing
 *        
 * @package VWP
 * @subpackage Libraries.XML  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

/**
 * Require Schema Support
 */

VWP::RequireLibrary('vwp.xml.schema');

/**
 * Virtual Web Platform - WSDL Processing
 *  
 * This class provides WSDL Processing
 *        
 * @package VWP
 * @subpackage Libraries.XML  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

class VWSDL extends VSchema 
{

	/**
	 * Get Binding Style
	 * 
	 * @param string $service
	 * @param string $port
	 * @return string|object Binding style on success, error or warning otherwise
	 * @access public
	 */
	
    function getBindingStyle($service,$port) {
        $bindNode = $this->getBindingTypeNode($service,$port);                
        $bindingStyle = strtolower($bindNode->getAttribute('style'));
                
        switch($bindingStyle) {
          case "document":
           // Hmm Need to find operation based on the message
          case "rpc":
           // Ok Should have operation!
           break;
          default:
          return VWP::raiseWarning('Unsupported binding style!',null,null,false);        
        }
        return $bindingStyle;    
    }

    /**
     * Get WSDL Document
     * Enter description here ...
     * @param unknown_type $url
     * @param unknown_type $refresh
     */

    function &getWSDL($url, $refresh = false) {
                        
        if (!isset(self::$_vfile)) {
            $tmp = new VSchema;
        }
        
        if (!isset(self::$_index)) {        
            self::reloadIndex();
        }
        
        if (!$refresh && isset(self::$_cache[$url])) {
            return self::$_cache[$url];
        }
                
        if (v()->filesystem()->path()->isAbsolute($url)) {
            
            $doc = VXMLHelper::fetch($url);            
            if (VWP::isWarning($doc)) {
                return $doc;
            }
            $filename = $url;
            
            
        } else {
            $cachePath = self::getCachePath();
            
            $cacheUrl = rtrim($url,'/');
            if ((!$refresh) && isset(self::$_index_map[$cacheUrl])) {
                $filename = $cachePath.DS.self::$_index_map[$cacheUrl];
                
                $doc = VXMLHelper::fetch($filename);
                if (VWP::isWarning($doc)) {
                    unset(self::$_index_map[$cacheUrl]);
                    self::saveIndex();
                }
            } 
            
            if ($refresh || (!isset(self::$_index_map[$cacheUrl]))) {
                $doc = VXMLHelper::fetch($url);
                
                if (VWP::isWarning($doc)) {
                    return $doc;
                }
                                
                $filename = self::$_vfile->mktemp('schema',$cachePath);
                
                if (VWP::isWarning($filename)) {
                    $filename->ethrow();
                } else {                
                    $result = self::$_vfile->write($filename,$doc->saveXML());
                    if (VWP::isWarning($result)) {
                        $result->ethrow();
                    } else {
                        $id = self::$_vfile->getName($filename);
                        self::$_index_map[$cacheUrl] = $id;
                        $node = self::$_index->createElement('cache');
                        $node->setAttribute('filename',$id);
                        $node->setAttribute('src',$cacheUrl);
                        self::$_index->documentElement->appendChild($node);
                        self::$_index_dirty = true;
                    }
                }
            }        
        }
        
        self::$_cache[$url] = new VWSDL($doc,$url,$filename,$refresh);
        return self::$_cache[$url];      
           
    }

    /**
     * Class Constructor
     * 
     * @param object $schemaDoc Schema Document
     * @param string $source Schema Source
     * @param string $cacheFile Cache Filename
     * @param boolean $refresh Refresh File
     * @access public
     */
    
    function __construct($schemaDoc = null,$source = null, $cacheFile = null,$refresh = false) {
        parent::__construct($schemaDoc,$source,$cacheFile,$refresh);
    }
    
    // end class VWSDL
}
