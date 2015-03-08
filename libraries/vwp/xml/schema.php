<?php

/**
 * Virtual Web Platform - XML Schema Processing
 *  
 * This file provides XML Schema Processing support
 *        
 * @package VWP
 * @subpackage Libraries.XML  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch 2011 - All Rights Reserved
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
 * This class provides XML Schema Processing
 *        
 * @package VWP
 * @subpackage Libraries.XML  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch 2011 - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

class VSchema extends VObject 
{
	/**
	 * Timeout in seconds
	 * 
	 * @var integer $timeout Timeout
	 * @access public
	 */
	
    public $timeout = 86400;

    /**
     * Schema Processed Flag
     * 
     * @var boolean $_processed Processed
     * @access public
     */
    
    public $_processed = false;
    
    /**
     * Root Schema Document
     * 
     * @var object $_rootDoc Root DOM Document
     * @access public
     */
    
    public $_rootDoc;
    
    /**
     * Schema Source
     * 
     * @var string $_source Source
     * @access public
     */
    
    public $_source = null;
    
    /**
     * Cache File
     * 
     * @var string $_cacheFile Cache file
     * @access public
     */
    
    public $_cacheFile = null;
    
    /**
     * Imported Schema's
     * 
     * @var array $_imported Imported Schema's
     * @access public
     */
    
    public $_imported = array();
    
    /**
     * File Driver
     * 
     * @var VFile $_vfile File driver
     * @access public
     */
                
    static $_vfile;
    
    /**
     * Folder Driver
     * 
     * @var VFile $_vfolder Folder driver
     * @access public
     */    
    
    static $_vfolder; 

    /**
     * Garbage Collection Flag
     * 
     * True if garbage collection has been executed, or false otherwise
     * 
     * @var boolean $_gc_flag Garbage Collection Flag
     * @access public 
     */
    
    static $_gc_flag = false;

    /**
     * Schema Cache Index
     * 
     * @var object Cache Index DOM Document
     * @access public
     */
    
    static $_index;
    
    /**
     * Index Dirty Flag
     * 
     * True if index has been modified or false otherwise
     * 
     * @var boolean $_index_dirty Index Dirty Flag
     * @access public
     */
    
    static $_index_dirty = false;
    
    /**
     * Index Map
     * 
     * The index map is an associative array of cache files indexed by URL
     * 
     * @var array $_index_map Index Map
     * @access public
     */    
    
    static $_index_map;

    /**
     * Cache
     *
     * @var array $_cache Cache
     * @access public
     */
    
    static $_cache = array();
    
    /**
     * Get Cache File
     * 
     * @return string Cache File
     * @access public
     */
    
    function getCacheFile() 
    {    
        return $this->_cacheFile;
    }
    
    /**
     * Get Known Schema Namespaces
     * 
     * Returns an associative array of Schema namespace definitions indexed by namespace for supported versions of XML Schema.
     * This method is provided for future support of multiple versions of the
     * XML Schema specification.
     * 
     * @return array Known Schema Namespaces
     * @access public
     */
    
    public static function getKnownSchemaNamespaces() 
    {    
       return array(
        'http://www.w3.org/2001/XMLSchema'=>'http://www.w3.org/2001/XMLSchema.xsd'
       );
       
    }

    /**
     * Check if node is an element node of the selected type 
     * 
     * @param object $node Node
     * @param string|array $ns Namespace(s)
     * @param string|array $name Element Name(s)
     * @return boolean True if node is an element of the selected type
     * @access public
     */
    
    public static function isElementTypeNS($node,$ns,$name) 
    {
                
        if ($node->nodeType != XML_ELEMENT_NODE) {            
            return false;
        }
        
        // Clean Namespaces
        
        if (!is_array($ns)) {
            $ns = array($ns);
        }

        foreach($ns as $k=>$v) {
        	$ns[$k] = (string)$v;
        }        
        
        // Clean Names
        
        if (!is_array($name)) {
            $name = array($name);
        }
        
        foreach($name as $k=>$v) {
        	$name[$k] = (string)$v;
        }
        
        if (!in_array((string)$node->namespaceURI,$ns)) {            
            return false;
        }
        
        $parts = explode(':',(string)$node->nodeName);
        $nodeName = array_pop($parts);
        
        if (!in_array($nodeName,$name)) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Get Defined Types
     * 
     * @param string $ns Namespace
     * @param array $skip List of sources to skip
     * @return array Datatypes Indexed By Id
     * @access public 
     */
    
    public function getTypes($ns = null, $skip = array()) 
    {
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
    
    /**
     * Get Schema Source URI
     * 
     * @return string Source URI
     * @access public
     */
    
    function getSource() 
    {
        return $this->_source;
    }
    
    /**
     * Is Remote Source
     * 
     * @return boolean True if schema defined at a remote location
     * @access public
     */
    
    public function isRemoteSource() 
    {
        return (substr($this->_source,0,5) == 'http:') || 
               (substr($this->_source,0,6) == 'https:');
    
    }
    
    /**
     * Is an Absolute URL
     * 
     * @param string $url URL
     * @return boolean True if provided URL is an absolute URL
     * @access public
     */
    
    public function isAbsoluteURL($url) 
    {
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

    /**
     * Get an absolute URL of the provided path
     *      
     * @param string $path Path
     * @return string Absolute URL
     * @access public
     */
    
    public function getAbsoluteURL($path) 
    {
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
    
    /**
     * Schema Document Object
     * 
     * @return object Schema DOM Document
     * @access public
     */
    
    public function &document() 
    {
        return $this->_rootDoc;
    }
    
    /**
     * Get Schema Element Node
     * 
     * @return object Node on success, error or warning otherwise
     * @access public
     */
    
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
    
    /**
     * Get Target Namespace
     * 
     * @return string|object Target namespace on success, error or warning otherwise
     * @access public
     */
    
    public function getTargetNamespace() 
    {
    	$schemaElement = $this->getSchemaElement();
    	if (VWP::isWarning($schemaElement)) {
    		return $schemaElement;
    	}
    	

    	if ($schemaElement->hasAttribute('targetNamespace')) {
    	    return (string)$schemaElement->getAttribute('targetNamespace');
    	}
    	
        return VWP::raiseWarning('Target Namespace not defined',__CLASS__,null,false);    	
    }
    
    /**     
     * Get Global Type Declaration
     * 
     * @todo Process Imports and XSI:schemaLocation in VSchema::getGlobalTypeDecl
     * @param string $namespaceURI Namespace
     * @param string $type Type
     * @return object Type Declaration Node on success, error or warning otherwise
     * @access public
     */
    
    public function getGlobalTypeDecl($namespaceURI,$type) 
    {
        $schemaNode = $this->getSchemaElement();
        $targetNamespace = $this->getTargetNamespace();
        
        if (!(VWP::isWarning($schemaNode) || VWP::isWarning($targetNamespace))) {
                        
            // Process Local
            
            if ($targetNamespace == (string)$namespaceURI) {
            	$simpleTypes = $schemaNode->getElementsByTagNameNS('http://www.w3.org/2001/XMLSchema','simpleType');
            	$len = $simpleTypes->length;
            	for($idx=0;$idx < $len;$idx++) {
            		$item = $simpleTypes->item($idx);
            		if ((string)$type == (string)$item->getAttribute('name')) {
            			return $item;
            		}
            	}
            	$complexTypes = $schemaNode->getElementsByTagNameNS('http://www.w3.org/2001/XMLSchema','complexType');
            	$len = $complexTypes->length;
            	for($idx=0;$idx < $len;$idx++) {
            		$item = $complexTypes->item($idx);
            		if ((string)$type == (string)$item->getAttribute('name')) {
            			return $item;
            		}
            	}

            	// Process imports here!

            	// Not Found!
            	return VWP::raiseWarning('Schema type "'.(string)$namespaceURI.':'.(string)$type.'" not found!',__CLASS__,null,false);
            }

            // Process SchemaType
            
            $schemaNSList = self::getKnownSchemaNamespaces();
             
            if (in_array((string)$namespaceURI,array_keys($schemaNSList))) {
                $schema = self::getSchema($schemaNSList[(string)$namespaceURI]);
                $typeDecl = $schema->getGlobalTypeDecl($namespaceURI,$type);                
                return $typeDecl;	
            }
            
        }
        
        return VWP::raiseWarning('Schema type "'.(string)$namespaceURI.':'.(string)$type.'" not found!',__CLASS__,null,false);        
    }
    
    /**
     * Get Global Element Declaration By Name
     * 
     * @param string $name Qualified Name
     * @access public
     */
    
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
    		        //$validNS = $schemaElement->lookupNamespaceURI($prefix) == $schemaElement->lookupNamespaceURI(null) ? true : false;
    		        
    		        $validNS = $schemaElement->lookupNamespaceURI($prefix) == $this->getTargetNamespace() ? true : false;
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
     * Get Element Declaration Node
     * 
     * This method will follow any ref attributes to find the actual declaration of the element
     * 
     * @param object $schemaElementRefNode Element Node
     * @return object Declaration node on success, error or warning otherwise 
     * @access public
     */
    
    function getElementDecl($schemaElementRefNode)
    {
    	$schemaNSList = self::getKnownSchemaNamespaces();
    	
        if ( (!is_object($schemaElementRefNode)) 
             || $schemaElementRefNode->nodeType != XML_ELEMENT_NODE
             || (!in_array((string)$schemaElementRefNode->namespaceURI,array_keys($schemaNSList)))
             || ('element' != (string)$schemaElementRefNode->localName)             
            ) {
        	return VWP::raiseWarning('Invalid element Node!',__CLASS__,null,false);
        }

        $ref = $schemaElementRefNode->hasAttribute('ref') ? $schemaElementRefNode->getAttribute('ref') : null;
    	if ($ref === null) {
    		$declNode = $schemaElementRefNode;
    	} else {    		
    		$declNode = $this->getGlobalElementDeclByName($ref);
    	}
    	return $declNode;        
    }
    
    /**
     * Get localName of provided Element Declaration
     *
     * @param DOMElement $node Element declaration node
     * @return string|object Local Name on success, error or warning otherwise
     * @access public
     */
    
    function getElementDeclLocalName($node) 
    {
    	$declNode = $this->getElementDecl($node);
    	if (VWP::isWarning($declNode)) {
    		return $declNode;
    	}
    	return (string)$declNode->getAttribute('name');    	
    }
    
    /**
     * Get Elements Declared Attributes
     * 
     * @param DOMElement $node Element declaration node
     * @return array|object Attribute declarations on success, error or warning otherwise
     * @access public
     */
    
    public function getElementDeclAttributes($node) 
    {

    	$declNode = $this->getElementDecl($node);
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
     * This method returns null if the maximum length is not defined
     * or if the maximum length is not a numeric value.
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
     * Get base type of an attribute declaration 
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
     * Get Sequence of a Complex Type Declaration
     * 
     * @param object $complexTypeNode Complex Type Declaration Node
     * @return VSchema_Sequence|object Sequence on success, error or warning otherwise
     * @access public
     */
    
    public function &getSequenceDecl($complexTypeNode) 
    {
    	$ns_list = array_keys(self::getKnownSchemaNamespaces());
    	
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
    
    /**
     * Get Sequence of a Element Declaration
     * 
     * @param object $node Element declaration node
     * @return VSchema_Sequence|object Schema Sequence on success, error or warning otherwise
     * @access public
     */    
    
    public function &getElementDeclSequence($node) 
    {
    	
        // Get Declaration Node
    	
    	$ref = $node->hasAttribute('ref') ? $node->getAttribute('ref') : null;
    	if ($ref === null) {
    		$declNode = $node;
    	} else {    		
    		$declNode = $this->getGlobalElementDeclByName($ref);
    	}    	
    	
    	 // check children for complex type
        $complexTypeNode = null;
        
        if ($declNode->hasAttribute('type')) {
        	$nInfo = $this->parseQualifiedName($declNode,$declNode->getAttribute('type'));        	
        	$complexTypeNode = $this->getGlobalTypeDecl($nInfo->namespaceURI,$nInfo->localName);
        	if (VWP::isWarning($complexTypeNode)) {
        		return $complexTypeNode;
        	}
        	if ('complexType' !== (string)$complexTypeNode->localName) {
                $complexTypeNode = null;        		
        	}
        } else {
    	    $ns_list = array_keys(self::getKnownSchemaNamespaces());
    	 
    	    $l = $declNode->childNodes->length;
    	    for($idx=0;$idx < $l; $idx++) {
    	        $c = $declNode->childNodes->item($idx);
    	        if ( $c->nodeType == XML_ELEMENT_NODE &&
    	             $c->localName == 'complexType' &&
    	             in_array($c->namespaceURI,$ns_list)
    	           ) {
    	            $complexTypeNode = $c;
    	            $idx = $l;   	
    	        }
    	    }
        }
    	 if ($complexTypeNode === null) {
    	     $e = VWP::raiseWarning('Sequence not found! Declaration does not allow child nodes!',__CLASS__,null,false);
    	     return $e;
    	 }
    	     	 
        $sequenceNode = $this->getSequenceDecl($complexTypeNode);
        return $sequenceNode;    	    	 
    }

    /**
     * Process the Schema
     * 
     * @param boolean $refresh Refresh any items in the Cache
     * @access public
     */
    
    public function processSchema($refresh = false) 
    {
              
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

    /**
     * Get a Schema
     * 
     * @param string $url Schema URL
     * @param boolean $refresh Refresh Cache
     * @access public
     */
    
    public static function &getSchema($url,$refresh = false) 
    {
                        
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

    /**
     * Get Cache Path
     * 
     * @return string Cache path
     * @access public
     */
    
    public static function getCachePath() 
    {                        
        return VWP::getVarPath('vwp').DS.'xml'.DS.'cache'.DS.'schema';        
    }

    /**
     * Remove file from cache
     * 
     * @param string $name Cache Filename
     * @access public
     */
    
    function expireByFilename($name) 
    {
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

    /**
     * Return Known Schema Namespaces
     *
     * @return array Known schema namespaces
     * @access public
     */
    
    public static function knownSchemaNamespaces() 
    {
    	$known = self::getKnownSchemaNamespaces();
    	return array_keys($known);           
    }

    /**
     * Execute Garbage Collection
     * 
     * @return boolean|object True on success, error or warning otherwise
     * @access public
     */
    
    public function gc() 
    {
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

        return true;
    }     

    /**
     * Reload Cache Index
     * 
     * @access public
     */
    
    public static function reloadIndex() 
    {
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

    /**
     * Save Cache Index
     * 
     * @return boolean|object True on success, error or warning otherwise
     * @access public
     */
    
    public static function saveIndex() 
    {
        $cachePath = self::getCachePath();
        v()->filesystem()->folder()->create($cachePath);
        
        $indexFile = $cachePath.DS.'cache_index.xml';
        
        $result = self::$_vfile->write($indexFile,self::$_index->saveXML());
        if (!VWP::isWarning($result)) {
            self::$_index_dirty = false;        
        }
        return $result;
    }

    /**
     * Parse Qualified Name
     * 
     * Returned object has 3 properties:
     * 
     * <ul>
     *  <li>prefix 
     *  <li>namespaceURI
     *  <li>localName
     * </ul>
     * 
     * @param object $node Reference Node
     * @param string $qualifiedName Qualfied Name
     * @return object Qualified Name Info
     */
    
	function parseQualifiedName($node,$qualifiedName) 
	{
		$ret = new stdClass;
		
		$ret->prefix = null;
		$ret->namespaceURI = null;
		$ret->localName = null;
				
		$parts = explode(':',(string)$qualifiedName);
						
	    if (count($parts) == 2) {
	    	$ret->prefix = (string)$parts[0];
	    	$ret->localName = (string)$parts[1];
	    	$ret->namespaceURI = (string)$node->lookupNamespaceURI($ret->prefix);	    	  
	    } else {
	    	$ret->localName = (string)$qualifiedName;	    	
	    	$ret->namespaceURI = (string)$node->namespaceURI;	    	
	    }

	    return $ret;
	}
    
	/**
	 * Get the Type Declaration Node of an element declaration
	 * 	 	 
	 * @param object $schemaElementRef Element declaration node
	 * @return object Type Declaration Node on success, error or warning otherwise
	 * @access public
	 */
	
    function getElementTypeDecl($schemaElementRef) 
    {
    	
        $elementDecl = $this->getElementDecl($schemaElementRef);

        if (VWP::isWarning($elementDecl)) {
        	return $elementDecl;
        }        
        
        if ($elementDecl->hasAttribute('type')) {
            $nameInfo = $this->parseQualifiedName($elementDecl, $elementDecl->getAttribute('type'));
            $typeDecl = $this->getGlobalTypeDecl($nameInfo->namespaceURI,$nameInfo->localName);                            
            return $typeDecl;
        } else {
        	$len = $elementDecl->childNodes->length;
        	for($idx=0;$idx<$len;$idx++) {
        		$item = $elementDecl->childNodes->item($idx);
        		if ($item->nodeType == XML_ELEMENT_NODE 
        		    && in_array((string)$item->namespaceURI,array_keys(self::getKnownNamespaces()))
        		   ) {
        		     if ('simpleType' == (string)$item->localName) {
        		     	return $item;
        		     }
        		     if ('complexType' == (string)$item->localName) {
                         return $item;
        		     }        		       	
        		}        		    
        	}        	
        }
                     
        // Undefined Type        
        return VWP::raiseWarning('Element type declaration not found!',__CLASS__,null,false);         
    }	
	
    /**
     * Test if provided element declaration is a simpleType 
     * 
     * @param object $schemaElementRef Element Declaration Node
     * @return boolean True if element declaration type is a simpleType
     * @access public
     */
    
    function isSimpleType($schemaElementRef) 
    {
    	
        $elementDecl = $this->getElementDecl($schemaElementRef);

        if (VWP::isWarning($elementDecl)) {        	        
        	return false;
        }        
        
        if ($elementDecl->hasAttribute('type')) {
            $nameInfo = $this->parseQualifiedName($elementDecl, $elementDecl->getAttribute('type'));
            $typeDecl = $this->getGlobalTypeDecl($nameInfo->namespaceURI,$nameInfo->localName);
            if (VWP::isWarning($typeDecl)) {                
                return false;	
            }
            
            return 'simpleType' == (string)$typeDecl->localName;
             	
        } else {
        	$len = $elementDecl->childNodes->length;
        	for($idx=0;$idx<$len;$idx++) {
        		$item = $elementDecl->childNodes->item($idx);
        		if ($item->nodeType == XML_ELEMENT_NODE 
        		    && in_array((string)$item->namespaceURI,array_keys(self::getKnownNamespaces()))
        		   ) {
        		     if ('simpleType' == (string)$item->localName) {
        		     	return true;
        		     }
        		     if ('complexType' == (string)$item->localName) {
        		     	return false;
        		     }        		       	
        		}        		    
        	}        	
        }
        
        // Undefined Type        
        return false;         
    }

    /**
     * Test if provided element declaration is a complexType 
     * 
     * @param object $schemaElementRef Element Declaration Node
     * @return boolean True if element declaration type is a complexType
     * @access public
     */
        
    function isComplexType($schemaElementRef) 
    {
    	
        $elementDecl = $this->getElementDecl($schemaElementRef);

        if (VWP::isWarning($elementDecl)) {
        	return false;
        }        
        
        if ($elementDecl->hasAttribute('type')) {
            $nameInfo = $this->parseQualifiedName($elementDecl, $elementDecl->getAttribute('type'));
            $typeDecl = $this->getGlobalTypeDecl($nameInfo->namespaceURI,$nameInfo->localName);
            if (VWP::isWarning($typeDecl)) {                
                return false;	
            }
            
            return 'complexType' == (string)$typeDecl->localName;
             	
        } else {
        	$len = $elementDecl->childNodes->length;
        	for($idx=0;$idx<$len;$idx++) {
        		$item = $elementDecl->childNodes->item($idx);
        		if ($item->nodeType == XML_ELEMENT_NODE 
        		    && in_array((string)$item->namespaceURI,array_keys(self::getKnownNamespaces()))
        		   ) {
        		     if ('simpleType' == (string)$item->localName) {
        		     	return false;
        		     }
        		     if ('complexType' == (string)$item->localName) {
        		     	return true;
        		     }        		       	
        		}        		    
        	}        	
        }
        
        // Undefined Type        
        return false;         
    }
    
    /**
     * Test if provided element declaration is a complexType with simple content 
     * 
     * @param object $schemaElementRef Element Declaration Node
     * @return boolean True if element declaration type is a complexType with simple content
     * @access public
     */    
    
    function hasSimpleContent($schemaElementRef) 
    {
    	
        $typeDecl = $this->getElementTypeDecl($schemaElementRef);
        if (VWP::isWarning($typeDecl)) {
        	return false;
        }
        
        $schemaNSList = $this->getKnownSchemaNamespaces();
        
        if ('complexType' == (string) $typeDecl->localName) {
        	$len = $typeDecl->childNodes->length;
        	for($idx=0;$idx<$len;$idx++) {
        		$item = $typeDecl->childNodes->item($idx);
        		if ($item->nodeType == XML_ELEMENT_NODE 
        		    && in_array((string)$item->namespaceURI,array_keys($schemaNSList))
        		    && 'simpleContent' == (string)$item->localName) {
        		    	return true;
        		}        		
        	}
        }

        // Undefined Type        
        return false;         
    }
        
    /**
     * Class Destructor
     * 
     * @access public
     */
    
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

    /**
     * Class Constructor
     * 
     * @param object $schemaDoc Schema Document
     * @param string $source Source
     * @param string $cacheFile Cache File
     * @param boolean $refresh Refresh cache
     * @access public
     */
    
    function __construct($schemaDoc = null,$source = null, $cacheFile = null,$refresh = false) 
    {
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
    
    // end class VSchema
}
