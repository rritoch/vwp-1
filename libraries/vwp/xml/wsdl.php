<?php

/**
 * Virtual Web Platform - WSDL Processing
 *  
 * This file provides WSDL Processing support
 *        
 * @package VWP
 * @subpackage Libraries.XML  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch 2011 - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

/**
 * Require Schema Support
 */

VWP::RequireLibrary('vwp.xml.schema');

/**
 * Require WSDL Message Support
 */

VWP::RequireLibrary('vwp.xml.wsdl.message');

/**
 * Require Service Data Object Support
 */

VWP::RequireLibrary('vwp.service.dataobject');

/**
 * Require Service Resource Support
 */

VWP::RequireLibrary('vwp.service.resource');

/**
 * Require Service Reply Support
 */

VWP::RequireLibrary('vwp.service.reply');

/**
 * Virtual Web Platform - WSDL Processing
 *  
 * This class provides WSDL Processing support
 *        
 * @package VWP
 * @subpackage Libraries.XML  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch 2011 - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

class VWSDL extends VSchema 
{

	/**
     * WSDL Namespace
     * 
     * @var string $wsdlNS WSDL Namespace
     * @access public
	 */
	
	protected $wsdlNS;
	
	/**
	 * Port Handlers
	 * 
	 * @var array $portHandlers Port Handlers
	 * @access public
	 */
	
	protected $portHandlers = array();
	
	/**
	 * Runtime Variables
	 * 
	 * @var array Runtime Environment Variables
	 * @access public
	 */
	
	protected $runtimeVars = array();

	/**
	 * Insert Port Handler
	 * 
	 * @todo Handle $before Argument in VWSD:insertPortHandler
	 *
	 * @param object $ob Port Handler
	 * @param boolean $before Before Index
	 * @access public
	 */
	
	function insertPortHandler($ob,$before = null) 
	{
		$this->portHandlers[] = $ob;
	}

	/**
	 * Parse WSDL Reference
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
	 * @param string $ref Reference Name
	 * @return object Reference Info
	 * @access public
	 */
	
	function parseWSDLReference($node,$ref) 
	{
		return $this->parseQualifiedName($node,$ref);	    
	}
	
	/**
	 * Get WSDL Element Node By Name
	 * 
	 * @param string $wsdlItem Item Element Name
	 * @param string $namespaceURI WSDL Item Namespace
	 * @param string $localName WSDL Item Name
	 * @return object WSDL Element Node
	 * @access public
	 */
	
	function getWSDLElementNodeByNameNS($wsdlItem,$namespaceURI,$localName)
	{
		$ret = null;
		$doc =& $this->document();
		$nodeList = $doc->getElementsByTagNameNS($this->wsdlNS,'definitions');
		$len = $nodeList->length;
		
		if ($len < 1) {
			return $ret;
		}
		
		$defNode = $nodeList->item(0);
		
		$tns = $defNode->getAttribute('targetNamespace');
		
		if ((string)$tns != (string)$namespaceURI) {
			return $ret;
		}
		
		$itemNodes = $doc->getElementsByTagNameNS($this->wsdlNS,$wsdlItem);
		$len = $itemNodes->length;

		for($idx=0;$idx<$len;$idx++) {
			$child = $itemNodes->item($idx);
			$name = (string)$child->getAttribute('name');
			if ($name == (string)$localName) {
				$ret = $child;
				$idx = $len;
			}
		}
		
		return $ret;
	}

	/**
	 * Get Port Type Operations
	 * 
	 * Reserved for future use
	 * 
	 * @todo Implement VWSDL:getPortTypeOperations
	 * @param string $service Service
	 * @param string $port Port
	 * @return array Port Type Operations
	 */
	
	function getPortTypeOperations($service,$port) 
	{
		
	}

	/**
	 * Get Binding Operations
	 * 
	 * Reserved for future use
	 * 
	 * @todo Implement VWSDL:getBindingOperations
	 * @param string $service Service
	 * @param string $port Port
	 * @return array Binding Operations
	 */
		
	function getBindingOperations($service,$port) 
	{
		
	}	

	/**
	 * Create Data Object
	 * 
	 * @param string $namespaceURI Namespace
	 * @param string $type Type
	 * @return VServiceDataObject Data Object
	 * @access public
	 */
	
	function createDataObject($namespaceURI,$type) 
	{
	    $ob = new VServiceDataObject($this,$namespaceURI,$type);
	    return $ob;	
	}
	
	/**
	 * Create a WSDL Message
	 * 
	 * @param string $namespaceURI Namespace URI
	 * @param string $name Message Name
	 * @return VWSDL_Message WSDL Message
	 * @access public
	 */
	
	function createMessage($namespaceURI,$name)
	{
	    $ret = null;
		$doc =& $this->document();
		$nodeList = $doc->getElementsByTagNameNS($this->wsdlNS,'definitions');
		$len = $nodeList->length;
		
		if ($len < 1) {			
			return $ret;
		}		

		$defNode = $nodeList->item(0);
		
		$tns = $defNode->getAttribute('targetNamespace');
		
		if ((string)$tns != (string)$namespaceURI) {			
			return $ret;
		}		
		
		$messages = $doc->getElementsByTagNameNS($this->wsdlNS,'message');
		
		$len = $messages->length;
		
		for($idx=0;$idx<$len;$idx++) {
		    $child = $messages->item($idx);
		    $curName = (string)$child->getAttribute('name');
		    if ($curName == (string)$name) {
		    	$idx = $len;
		    	$ret = new VWSDL_Message($this,$child);
		    }	
		}
		
		return $ret;
	}
	
	/**
	 * Create A Reply Object
	 * 
	 * @param VServiceResource $resource Resource
	 * @param object $replyHandler Reply Handler
	 * @access public
	 */
	
	function createReply($resource,$replyHandler) 
	{
		$reply = new VServiceReply($resource,$replyHandler);
		return $reply;
	}
	
	/**
	 * Get Service Element By Name
	 * 
	 * @param string $serviceName Service Name
	 * @return object Service Element Node
	 * @access public
	 */
	
	function getServiceElementByName($serviceName) 
	{				
		$doc =& $this->document();
		$services = $doc->getElementsByTagNameNS($this->wsdlNS,'service');
		$len = $services->length;
						
		$ret = null;
		for($idx=0;$idx<$len;$idx++) {
		    $child = $services->item($idx);
		    $curName = (string)$child->getAttribute('name');
		    if ($curName == $serviceName) {
		    	$idx = $len;
		    	$ret = $child;
		    }		    			
		}
		return $ret;		
	}
	
	/**
	 * Get Port Element By Name
	 * 
	 * @param object $srvcNode Service Node
	 * @param string $portName Port Name
	 * @return object Port Element Node
	 * @access public
	 */
	
	function getPortElementByName($srvcNode,$portName) 
	{
		$ret = null;
		
		if ($srvcNode === null) {
			return $ret;
		}
		
		$ports = $srvcNode->getElementsByTagNameNS($this->wsdlNS,'port');
		$len = $ports->length;
						
		for($idx=0;$idx<$len;$idx++) {
		    $child = $ports->item($idx);
		    $curName = (string)$child->getAttribute('name');
		    if ($curName == $portName) {
		    	$idx = $len;
		    	$ret = $child;
		    }		    			
		}
		
		return $ret;		
	}
	
	/**
	 * Get Resource
	 * 
	 * @param string $serviceName Service Name
	 * @param string $portName Port Name
	 * @return VServiceResource Resource 
	 */
	
	function &getResource($serviceName,$portName) 
	{
				
		$srvcNode = $this->getServiceElementByName($serviceName);
        $portNode = $this->getPortElementByName($srvcNode,$portName);
                        
        $drivers = $this->portHandlers;
        $len = count($drivers);
        $serviceHandler = null;
        $idx = 0;
        while($serviceHandler === null && $idx < $len) 
        {        	
        	$serviceHandler = $drivers[$idx]->getHandler($portNode); 
        	$idx++;
        }

        if ($serviceHandler === null) {        	        	
        	$err = VWP::raiseWarning('No handler!',__CLASS__,null,false);
        	return $err;
        }
        
        $resource = new VServiceResource;
        $resource->service = $this;
        $resource->handler = $serviceHandler;
        $resource->portName = $portName;
        $resource->serviceName = $serviceName;
        return $resource;         
	}
	
	/**
	 * Get Service List
	 * 
	 * Reserved for future use
	 * 
	 * @todo Implement VWSDL:getServiceList
	 * @return array Service List
	 * @access public
	 */
	
	function getServiceList() 
	{
		
	}
	
	/**
	 * Get Port List
	 * 
	 * Reserved for future use
	 * 
	 * @todo Implement VWSDL:getPortList
	 * @param string $serviceName Service Name
	 * @return array Service List
	 * @access public
	 */	
	
	function getPortList($serviceName) 
	{
		
	}			
	
	/**
	 * Get Binding Style
	 * 
	 * @param string $service
	 * @param string $port
	 * @return string|object Binding style on success, error or warning otherwise
	 * @access public
	 */
	
    function getBindingStyle($service,$port) 
    {
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
     *
     * @param string $url Source URL
     * @param boolean $refresh Refresh Cache Flag
     * @return VWSDL WSDL Document
     * @access public
     */

    public static function &getWSDL($url, $refresh = false) {
                        
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
     * Set Runtime Environment Variable
     * 
     * @param string $vname Variable Name
     * @param mixed $value Value
     * @param string $class Class
     * @access public
     */
    
    function setVar($vname,$value,$class = '_') 
    {
        if (!isset($this->runtimeVars[$class])) {
        	$this->runtimeVars[$class] = array();
        }

        $this->runtimeVars[$class][$vname] = $value;
        return $value;
    }
    
    /**
     * Get Runtime Environment Variable
     * 
     * @param string $vname Variable Name
     * @param mixed $default Default Value
     * @param string $class Class
     * @access public
     */    
    
    function getVar($vname,$default = null,$class = '_') 
    {
        if (!isset($this->runtimeVars[$class])) {
        	return $default;
        }

        if (!isset($this->runtimeVars[$class][$vname])) {
        	return $default;
        }
        return $this->runtimeVars[$class][$vname];
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
    
    function __construct($schemaDoc = null,$source = null, $cacheFile = null,$refresh = false) 
    {
        parent::__construct($schemaDoc,$source,$cacheFile,$refresh);
        
        $defs = $schemaDoc->getElementsByTagNameNS('http://schemas.xmlsoap.org/wsdl/','definitions');
        if ($defs->length > 0) {
        	$this->wsdlNS = 'http://schemas.xmlsoap.org/wsdl/';
        }        
    }
    
    // end class VWSDL
}
