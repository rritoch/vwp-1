<?php

/**
 * Virtual Web Platform - Schema Processing
 *  
 * This file provides Schema Processing
 *        
 * @package VWP
 * @subpackage Libraries.XML  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

/**
 * Require XML Helper
 */

VWP::RequireLibrary('vwp.xml.xmlhelper');

/**
 * Require Schema Data Type
 */

VWP::RequireLibrary('vwp.xml.schema.datatype');

/**
 * Require Schema Sequence
 */

VWP::RequireLibrary('vwp.xml.schema.sequence');

/**
 * Virtual Web Platform - Schema Processing
 *  
 * This class provides Schema Processing
 *        
 * @package VWP
 * @subpackage Libraries.XML  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */



class VSchema extends VObject {
    var $timeout = 86400;

    var $_processed = false;
    
    var $_rootDoc;
    
    var $_source = null;
    var $_cacheFile = null;
    
    var $_imported = array();
                
    static $_vfile;
    static $_vfolder;    
    static $_gc_flag = false;    
    static $_index;
    static $_index_dirty = false;
    static $_index_map;    
    static $_cache = array();
    
    
    function getCacheFile() {    
        return $this->_cacheFile;
    }
    
    public static function getKnownSchemaNamespaces() {
    
       return array(
        'http://www.w3.org/2001/XMLSchema'=>'http://www.w3.org/2001/XMLSchema.xsd'
       );
       
    }
        
    public static function isElementTypeNS($node,$ns,$name) {
                
        if ($node->nodeType != XML_ELEMENT_NODE) {            
            return false;
        }
        
        if (is_string($ns)) {
            $node = array($ns);
        }
        
        if (is_string($name)) {
            $name = array($name);
        }
        
        if (!in_array($node->namespaceURI,$ns)) {            
            return false;
        }
        
        $parts = explode(':',$node->nodeName);
        $nodeName = array_pop($parts);
        
        if (!in_array($nodeName,$name)) {
            return false;
        }
        
        return true;
    }
    

    
    public function getTypes($ns = null, $skip = array()) {
        $dataTypes = array();
        $schemaNS = array_keys(self::getKnownSchemaNamespaces());
        $typeElements = array('simpleType','complexType');
        foreach($schemaNS as $sns) {
            $schemaList = $this->_rootDoc->getElementsByTagNameNS($sns,'schema');            
            
            for($si = 0; $si < $schemaList->length; $si++) {
                $schemaNode = $schemaList->item($si);
                $targetNamespace = $schemaNode->getAttribute('targetNamespace');
                
                for($ci = 0; $ci < $schemaNode->childNodes->length; $ci++) {
                    $childNode = $schemaNode->childNodes->item($ci);
                    if (self::isElementTypeNS($childNode,$schemaNS,$typeElements)) {
                        $name = $childNode->getAttribute('name');
                        if (!empty($name)) {
                            $st = new VSchema_DataType($childNode,$targetNamespace);
                            if (empty($ns) || (VXMLHelper::sameNamespace($st->namespace,$ns))) { 
                                $dataTypes[$st->id] = $st;              
                            }                                          
                        }
                    }
                }
            }            
        }
        
        // Skip avoids Inf Recursion
        
        $newskip = $skip;
        array_push($newskip,$this->_source);
        
        foreach($this->_imported as $uri=>$schema) {
            if (!in_array($uri,$skip)) {
                $dataTypes = array_merge($dataTypes,$schema->getTypes($ns,$newskip));
            }        
        }
        
        return $dataTypes;        
    }
    
    function getSource() {
        return $this->_source;
    }
    
    public function isRemoteSource() {
        return (substr($this->_source,0,5) == 'http:') || 
               (substr($this->_source,0,6) == 'https:');
    
    }
    
    public function isAbsoluteURL($url) {
        if (substr($url,1,1) == ':') {
            return true; // Windows Absolute Path
        }
        
        if (substr($url,0,5) == 'http:') {
            return true; // Absolute URL
        }

        if (substr($url,0,6) == 'https:') {
            return true; // Absolute URL
        }
        
        if (
            (!self::isRemoteSource()) &&
            (substr($url,0,1) == DS)
           ) {
            return true;   
        }
                
        return false; 
    }
        
    public function getAbsoluteURL($path) {
        if (self::isAbsoluteURL($path)) {
            return $path;
        }        


        $source = empty($this->_source) ? '' : $this->_source;
        
        if (self::isRemoteSource()) {            
            $ds = '/';
        } else {                                            
            $ds = DS;             
        }
        
        $parts = explode($ds,$source);        
        $lastpart = count($parts) - 1;
        
        if (!empty($parts[$lastpart])) {
         array_pop($parts);
         $source = implode($ds,$parts);
        }
        
        $realPath = $source . $ds . $path;
                
        return $realPath;
    }
    
    public function &document() {
        return $this->_rootDoc;
    }
    
    public function getSchemaElement() 
    {
    	 $nsList = self::getKnownSchemaNamespaces();
       
         foreach($nsList as $ns=>$ssloc) {    	
             $s = $this->_rootDoc->getElementsByTagNameNS($ns,'schema');
             if ($s->length > 0) {
                 return $s->item(0);         	
             }
         }
         return VWP::raiseWarning('Schema Element Not Found',__CLASS__,null,false);	
    }
    
    public function getGlobalElementDeclByName($name) 
    {
    	 $parts = explode(':',$name);
    	 $localName = array_pop($parts);
         $prefix = implode(':',$parts);
    	
      	 $nsList = self::getKnownSchemaNamespaces();
       
         foreach($nsList as $ns=>$ssloc) {    	
             $s = $this->_rootDoc->getElementsByTagNameNS($ns,'schema');
             $len = $s->length;
             for($idx=0;$idx<$len;$idx++) {
             	$schemaElement = $s->item($idx);

             	$validNS = true;
             	
                if (!empty($prefix)) {
    		        $validNS = $schemaElement->lookupNamespaceURI($prefix) == $schemaElement->lookupNamespaceURI(null) ? true : false;  
    	        }
             	
    	        if ($validNS) {
             	    $clen = $schemaElement->childNodes->length;
             	    for($ptr=0;$ptr < $clen; $ptr++) {
             		    $node = $schemaElement->childNodes->item($ptr);
             		    if (
             		        $node->nodeType == XML_ELEMENT_NODE &&
             		        $node->localName == 'element' &&
             		        $node->namespaceURI == $ns &&
             		        $node->getAttribute('name') == $localName
             		       ) {
             		   	    return $node;
             	        }
             	    }
    	        }
             }
         }
         return VWP::raiseWarning("Element declaration '$name' not found!",__CLASS__,null,false);    	
    }
    
    /**
     * Get Elements Declared Attributes
     * 
     * @param DOMElement $node Element declaration node
     * @return array|object Attributes
     */
    
    public function getElementDeclAttributes($node) 
    {
    	// Get Declaration Node
    	
    	$ref = $node->hasAttribute('ref') ? $node->getAttribute('ref') : null;
    	if ($ref === null) {
    		$declNode = $node;
    	} else {    		
    		$declNode = $this->getGlobalElementDeclByName($ref);
    	}
    	
    	if (VWP::isWarning($declNode)) {
    		return $declNode;
    	}
    	    	
    	// Dig to complex type    	
    	
    	$ns_list = array_keys(self::getKnownSchemaNamespaces());
    	    	
    	$complexTypeNode = null;
    	
    	$l = $declNode->childNodes->length;
    	for($idx=0;$idx < $l;$idx++) {
    		$c = $declNode->childNodes->item($idx);
   			if (
  			     $c->nodeType == XML_ELEMENT_NODE &&
   			     $c->localName == 'complexType' &&
   			     in_array($c->namespaceURI,$ns_list)
   			    ) {
   			    $complexTypeNode = $c;
   			    $idx = $l;   	
   			}    		
    	}
    	
    	if ($complexTypeNode === null) {
    		return VWP::raiseWarning('Only complex types have attributes!',__CLASS__,null,false);
    	}
    	
    	// Build attribute list
    	
    	$attrs = array();
    	
        $l = $complexTypeNode->childNodes->length;
    	for($idx=0;$idx < $l;$idx++) {
    		$c = $complexTypeNode->childNodes->item($idx);
   			if (
  			     $c->nodeType == XML_ELEMENT_NODE &&
   			     $c->localName == 'attribute' &&
   			     in_array($c->namespaceURI,$ns_list)
   			    ) {
   			    $attrs[] = $c;
   		        	       	
   			}    		
    	}    	
    	
    	return $attrs;
    }
    
    /**
     * Get attribute declaration maximum length
     * 
     * @param DOMElement $node Attribute node
     * @return integer Length
     * @access public
     */
    
    public function getAttributeDeclMaxLength($node)
    {
    	$typeName = $node->hasAttribute('type') ? $node->getAttribute('type') : null;
    	
    	if ($typeName === null) {    	
    		$ns_list = array_keys(self::getKnownSchemaNamespaces());
    		
    		$simpleTypeNode = null;
    		$l = $node->childNodes->length;
    		for($idx=0;$idx<$l;$idx++) {
    			$c = $node->childNodes->item($idx);
    			if (
    			     $c->nodeType == XML_ELEMENT_NODE &&
    			     $c->localName == 'simpleType' &&
    			     in_array($c->namespaceURI,$ns_list)
    			    ) {
    			    $simpleTypeNode = $c;
    			    $idx = $l;    			
    			}
    		}

    		if ($simpleTypeNode === null) {
    			return null;
    		}
    		
    	    $restrictionNode = null;
    		    		
    		$l = $simpleTypeNode->childNodes->length;
    		for($idx=0;$idx<$l;$idx++) {
    			$c = $simpleTypeNode->childNodes->item($idx);
    			if (
    			     $c->nodeType == XML_ELEMENT_NODE &&
    			     $c->localName == 'restriction' &&
    			     in_array($c->namespaceURI,$ns_list)
    			    ) {
    			    $restrictionNode = $c;
    			    $idx = $l;    			
    			}
    		}

    		if ($restrictionNode === null) {
    			return null;
    		}    		
    		    		
    		
    	    $l = $restrictionNode->childNodes->length;
    		for($idx=0;$idx<$l;$idx++) {
    			$c = $restrictionNode->childNodes->item($idx);
    			if (
    			     $c->nodeType == XML_ELEMENT_NODE &&
    			     $c->localName == 'maxLength' &&
    			     in_array($c->namespaceURI,$ns_list)
    			    ) {
    			    $maxLength = $c->getAttribute('value');
    			    if (is_numeric($maxLength)) {
    			    	return $maxLength;
    			    }    			
    			}
    		}
    	}     		    	
        return null;	
    }
    
    /**
     * Get Attribute's base type 
     * 
     * Note: The base type is returned as an object with a namespace property and a type property
     *  
     * @param DOMElement $node Attribute element node
     * @return VSchema_DataType|object Base type on success, error or warning otherwise
     * @access public
     */
    
    public function getAttributeDeclBaseType($node)
    {
    	$typeName = $node->hasAttribute('type') ? $node->getAttribute('type') : null;
    	
    	if ($typeName === null) {
    		$ns_list = array_keys(self::getKnownSchemaNamespaces());
    		
    		$simpleTypeNode = null;
    		$l = $node->childNodes->length;
    		for($idx=0;$idx<$l;$idx++) {
    			$c = $node->childNodes->item($idx);
    			if (
    			     $c->nodeType == XML_ELEMENT_NODE &&
    			     $c->localName == 'simpleType' &&
    			     in_array($c->namespaceURI,$ns_list)
    			    ) {
    			    $simpleTypeNode = $c;
    			    $idx = $l;    			
    			}
    		}

    		if ($simpleTypeNode === null) {
    			return VWP::raiseWarning("No type defined for provided node!",__CLASS__,null,false);
    		}
    		
    	    $restrictionNode = null;
    		    		
    		$l = $simpleTypeNode->childNodes->length;
    		for($idx=0;$idx<$l;$idx++) {
    			$c = $simpleTypeNode->childNodes->item($idx);
    			if (
    			     $c->nodeType == XML_ELEMENT_NODE &&
    			     $c->localName == 'restriction' &&
    			     in_array($c->namespaceURI,$ns_list)
    			    ) {
    			    $restrictionNode = $c;
    			    $idx = $l;    			
    			}
    		}

    		if ($restrictionNode === null) {
    			return VWP::raiseWarning('No type defined for provided node!',__CLASS__,null,false);
    		}    		
    		
    		$typeName = $restrictionNode->hasAttribute('base') ? $restrictionNode->getAttribute('base') : null;
    		
    		if ($typeName === null) {
    			return VWP::raiseWarning('No type defined for provided node!',__CLASS__,null,false);
    		}     		    	
    	}
    	
    	$parts = explode(':',$typeName);
    	$localName = array_pop($parts);
    	$prefix = implode(':',$parts);
    	 
    	$dataType = new VSchema_DataType;
    	$dataType->name = $localName;
    	$dataType->namespace = $node->lookupNamespaceURI(empty($prefix) ? null : $prefix);
    	$dataType->id = $dataType->namespace. '#types.' . $dataType->name;
        
    	return $dataType;    	    	
    }
    
    /**
     * Get Elements Declared Child Element Sequence
     * 
     * @param DOMElement $node Element declaration node
     * @return VSchema_Sequence|object Schema Sequence on success, error or warning otherwise
     */    
    
    public function &getElementDeclSequence($node) 
    {
    	 // check children for complex type
        $complexTypeNode = null;
    	$ns_list = array_keys(self::getKnownSchemaNamespaces());
    	 
    	$l = $node->childNodes->length;
    	for($idx=0;$idx < $l; $idx++) {
    	    $c = $node->childNodes->item($idx);
    	    if ( $c->nodeType == XML_ELEMENT_NODE &&
    	         $c->localName == 'complexType' &&
    	         in_array($c->namespaceURI,$ns_list)
    	       ) {
    	        $complexTypeNode = $c;
    	        $idx = $l;   	
    	    }
    	}
    	 
    	 if ($complexTypeNode === null) {
    	     $e = VWP::raiseWarning('Sequence not found! Declaration does not allow child nodes!',__CLASS__,null,false);
    	     return $e;
    	 }
    	     	 
        $sequenceNode = null;
    	    	 
    	$l = $complexTypeNode->childNodes->length;
    	for($idx=0;$idx < $l; $idx++) {
    	    $c = $complexTypeNode->childNodes->item($idx);
    	    if ( $c->nodeType == XML_ELEMENT_NODE &&
    	         $c->localName == 'sequence' &&
    	         in_array($c->namespaceURI,$ns_list)
    	       ) {
    	        $sequenceNode = $c;
    	        $idx = $l;   	
    	    }
    	}
    	 
    	 if ($sequenceNode === null) {
    	     $e = VWP::raiseWarning('Sequence not found! Declaration does not allow child nodes!',__CLASS__,null,false);
    	     return $e;
    	 }

    	 $sequence = new VSchema_Sequence($sequenceNode);
    	 return $sequence;
    }
    
    
    
    public function processSchema($refresh = false) {
       
       $this->_imported = array();
       $nsList = self::getKnownSchemaNamespaces();
       
       foreach($nsList as $ns=>$ssloc) {
            
            $schemaList = $this->_rootDoc->getElementsByTagNameNS($ns,'schema');            
            if (($schemaList->length > 0) && ($this->_source != $ssloc)) {
                    $this->_imported[$ssloc] =& self::getSchema($ssloc);
            }
                        
            for($si = 0; $si < $schemaList->length; $si++) {
                $schemaNode = $schemaList->item($si);
    
                $importList = $this->_rootDoc->getElementsByTagNameNS($ns,'import');
               for($i=0;$i < $importList->length; $i++) {
                   $node = $importList->item($i);
               
                   $url1 = $node->getAttribute('schemaLocation');
                   $url2 = $node->getAttribute('namespace');
                   if (!empty($url1)) {
                       $url = $this->getAbsoluteURL($url1);
                       
                       $this->_imported[$url] =& self::getSchema($url,$refresh);
                       if (VWP::isWarning($this->_imported[$url])) {
                           $err = $this->_imported[$url];
                           $this->setError($err);
                           unset($this->_imported[$url]);
                       }
                       
                   } elseif (!empty($url2)) {
                       $url = $this->getAbsoluteURL($url2);
                       $this->_imported[$url] =& self::getSchema($url,$refresh);
                       if (VWP::isWarning($this->_imported[$url])) {
                           $err = $this->_imported[$url];
                           $this->setError($err);
                           unset($this->_imported[$url]);
                       }               
                   }
               }       
           }
        }
       
        $this->_processed = true;
    }
                 
    public static function &getSchema($url,$refresh = false) {
                        
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
        
        self::$_cache[$url] = new VSchema($doc,$url,$filename,$refresh);
        return self::$_cache[$url];      
    }
     
    public static function getCachePath() {                        
        return VWP::getVarPath('vwp').DS.'xml'.DS.'cache'.DS.'schema';        
    }

    function expireByFilename($name) {
        $cachePath = self::getCachePath();
        self::$_vfile->delete($cachePath.DS.$name);
        $entries = self::$_index->getElementsByTagName('cache');
        $d = array();
        
        for($i=0;$i < $entries->length; $i++) {
            $node = $entries->item($i);
            if ($node->getAttribute('filename') == $name) {
                $d[] = $node;    
            } 
        }
                
        foreach($d as $node) {
            $src = $node->getAttribute('src');
            unset(self::$_index_map[$src]);
            $node->parentNode->removeChild($node);
            self::$_index_dirty = true;
        }        
    }

    public static function knownSchemaNamespaces() {
        return array('http://www.w3.org/2001/XMLSchema');    
    }

    public function gc() {
        if (!isset(self::$_vfile)) {
            self::$_vfile =& v()->filesystem()->file();
        }
        if (!isset(self::$_vfolder)) {
            self::$_vfolder =& v()->filesystem()->folder();
        }            
          $t = time();
          $cachePath = self::getCachePath();
          $files = self::$_vfolder->folders($cachePath);
          if (VWP::isWarning($files)) {
              return $files;
          }   
          
          foreach($files as $name) {
              if ($name != 'cache_index.xml') {
                $mt = self::$_vfile->getMTime($cachePath.DS.$name);
                if (!VWP::isWarning($mt)) {
                      if (($mt + $this->timeout) < $t) {
                            $this->expireByFilename($name);
                       }                  
                }                      
            }          
        }
        self::$_gc_flag = true;                  
    }     

    public static function reloadIndex() {
        $cachePath = self::getCachePath();
        $indexFile = $cachePath.DS.'cache_index.xml';
        if (!self::$_vfile->exists($indexFile)) {
            self::$_index = new DomDocument;
            self::$_index->loadXML('<' . '?xml version="1.0" ?' . '>' . "\n" . '<schema_cache></schema_cache>');
            self::$_index_dirty = true;
        } else {
            $src = self::$_vfile->read($indexFile);
            if (VWP::isWarning($src)) {
                $src->ethrow();
            }
            self::$_index = new DomDocument;
            self::$_index->loadXML($src);
            self::$_index_dirty = false;
            
            $items = self::$_index->getElementsByTagName('cache');
            self::$_index_map = array();
            for($i=0;$i<$items->length;$i++) {
                $item = $items->item($i);
                self::$_index_map[$item->getAttribute('src')] = $item->getAttribute('filename');
            }
        
        }        
    }

    public static function saveIndex() {
        $cachePath = self::getCachePath();
        v()->filesystem()->folder()->create($cachePath);
        
        $indexFile = $cachePath.DS.'cache_index.xml';
        
        $result = self::$_vfile->write($indexFile,self::$_index->saveXML());
        if (!VWP::isWarning($result)) {
            self::$_index_dirty = false;        
        }
        return $result;
    }

    function __destruct() {
        
        if (!self::$_gc_flag) {
            self::$_gc_flag = true;
            self::gc();
        }
            
        if (self::$_index_dirty) {
            self::saveIndex();
        }
            
        parent::__destruct();
    }
        
    function __construct($schemaDoc = null,$source = null, $cacheFile = null,$refresh = false) {
        parent::__construct();
        
        $doProcess = false;
        if (!empty($schemaDoc)) {
            $this->_rootDoc = $schemaDoc;
            $this->_source = $source;
            $this->_cacheFile = $cacheFile;
            $doProcess = true;
        }
        
        if (!empty($source)) {        
            self::$_cache[$source] = $this; // Recursion fix!
        }
        
        
        if (!isset(self::$_vfile)) {
            self::$_vfile =& v()->filesystem()->file();
        }
        if (!isset(self::$_vfolder)) {
            self::$_vfolder =& v()->filesystem()->folder();
        } 
        
        if (!isset(self::$_index)) {
            self::reloadIndex();
        }
        
        if ($doProcess) {
            $this->processSchema($refresh);
        }       
        
    }
}



