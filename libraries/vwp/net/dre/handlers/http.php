<?php

/**
 * Virtual Web Platform - HTTP Service Handler
 *   
 * @package VWP
 * @subpackage Libraries.Networking.DRE.Handlers  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License 
 */

/**
 * Require Handler Support
 */

VWP::RequireLibrary('vwp.service.handler');

/**
 * Virtual Web Platform - HTTP Service Handler
 *   
 * @package VWP
 * @subpackage Libraries.Networking.DRE.Handlers  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License 
 */


class VHTTP_Service_Handler extends VServiceHandler 
{

	/**
	 * Callback
	 * 
	 * @var mixed $cb Callback
	 * @access public
	 */
	
	protected $cb;
	
	/**	 
	 * HTTP Namespace
	 */
	
	const HTTPNS = 'http://schemas.xmlsoap.org/wsdl/http/';
	
	/**
	 * HTTP Extension Namespace
	 */
	
	const HTTPEXTNS = 'urn:vwp:xml:wsdl:httpext:1.0.2';
	
	/**
	 * Mime Namespace
	 */	
	
	const MIMENS = 'http://schemas.xmlsoap.org/wsdl/mime/';
	
	/**
	 * Mime Extension Namespace
	 */	
	
	const MIMEEXTNS = 'urn:vwp:xml:wsdl:mimeext:1.0.2';

	/**
	 * Translate Sequence to Post Vars
	 * 
	 * @param object $schema Schema
	 * @param object $sequence Sequence Node
	 * @param VServiceDataObject $partData
	 * @param string $keyField Key field
	 * @return array Post Vars
	 * @access public	 
	 */
	
	function sequence2postVars($schema,$sequence,$partData,$keyField = null) 
	{
		
		$post = array();
	    $data = $partData->getValue();
	    
	    if ($data === null) {
	    	return $post;
	    }
	    
	    $len = $sequence->length;
	    for($idx=0;$idx < $len; $idx++) {
	    	
	    	$item = $sequence->item($idx);
	    	$itemDecl = $schema->getElementDecl($item);
	    		    		    	
	    	$name = (string)$itemDecl->getAttribute('name');
	    	$simpleType = $schema->isSimpleType($item);

	    	if ($simpleType) {
	    		$complexType = false;
	    		$simpleContent = true;
	    	} else {
	    	    $complexType = $schema->isComplexType($item);
	    	    $simpleContent = $schema->hasSimpleContent($item);
	    	}
	    	
	    	
	    	
	    	if (!($simpleType || $complexType)) {	    			    		
	    		return VWP::raiseWarning('Invalid item type! Expected simpleType or complexType.',__CLASS__,null,false);
	    	}
	    	
	    	if ($complexType) {
	    		$itemKey = null;
	    		$ilen = $item->childNodes->length;
	    		for($iidx=0;$iidx < $ilen;$iidx++) {
	    			$ii = $item->childNodes->item($iidx);
	    			if ($ii->nodeType == XML_ELEMENT_NODE
	    			   && 'http://www.w3.org/2001/XMLSchema' == (string)$ii->namespaceURI
	    			   && 'key' == (string)$ii->localName) {
	    			    $fields = $ii->getElementsByTagNameNS('http://www.w3.org/2001/XMLSchema','field');
	    			    $selectors = $ii->getElementsByTagNameNS('http://www.w3.org/2001/XMLSchema','selector');
	    			    
	    			    if (($fields->length > 0) && ($selectors->length > 0)) {
	    			    	$iidx = $ilen;	    			    	
	    			    	$itemKey = new stdClass();
	    			    	$itemKey->field = $fields->item(0)->getAttribute('xpath');
	    			    	$itemKey->selector = $selectors->item(0)->getAttribute('xpath');
	    			    }   	
	    			}
	    		}
	    	}
	    		    		    	
	    	$maxOccurs = $item->getAttribute('maxOccurs');
	    	if ($maxOccurs !== null) {
	    		$maxOccurs = (string)$maxOccurs; 
	    	}
	    		    	
	    	$minOccurs = $item->getAttribute('minOccurs');
	    	if ($minOccurs !== null) {
	    		$minOccurs = (string)$minOccurs; 
	    	}
	    	
	    	
	    	$result = null;
	    	if ($maxOccurs == 'unbounded' || $maxOccurs > 1) {
	    		$mode = 'array';
	    		$ctr = 0;
	    		$result = array();
	    	} else {
	    		if ($maxOccurs == '1') {
	    		    $mode = 'string';	    		    
	    		}
	    	}
	    	
	    	if ($maxOccurs != '0') {
	    			    		
	    		// process values
	    		
	    	    foreach($data as $dataitem) {
	    	        $dataItemValue = $dataitem[0];
	    	        $dataItemName = $dataitem[1];
	    	        if ($keyField !== null || $dataItemName == $name) {
	    	        	if ($keyField === null && $maxOccurs == '1' && $result !== null) {
	    	        		return VWP::raiseWarning('Only one value allowed for "'.(string)$name.'"!',__CLASS__,null,false);
	    	        	}
	    	        	
	    	        	if ($simpleContent) {
	    	        	   if ($keyField === null) {	
	    	                   $result = (string)$dataItemValue->getValue();
	    	        	   } else {	    	        	   	   
	    	        	       if ($result === null) {
	    	        	           $result = array();
	    	        	       }
	    	        	       if (substr($keyField->field,0,1) == '@') {
	    	        	           $attr = substr($keyField->field,1);	    	        	           
	    	        	           $k = (string)$dataItemValue->getAttributeNS($schema->getTargetNamespace(),$attr);
	    	        	           $result[$k] = (string)$dataItemValue->getValue();	    	        	       	
	    	        	       } else {
	    	        	           return VWP::raiseWarning('Element keys not supported!',__CLASS__,null,false);
	    	        	       }
	    	        	   }
	    	        	} else {
	    	        	   if ($itemKey === null) {
	    	        	       return VWP::raiseWarning('Sequential array unsupported!',__CLASS__,null,false);
	    	        	   }
	    	        	   
	    	        	   if (substr($itemKey->field,0,1) == '@') {

	    	        	       $childSeq = $schema->getElementDeclSequence($item);
	    	        	       if (VWP::isWarning($childSeq)) {
	    	        	           return $childSeq;
	    	        	       }	    	        	       
	    	        	       
	    	        	       $d = $this->sequence2postVars($schema,$childSeq,$dataItemValue,$itemKey);
	    	        	       $result = $d[$itemKey->selector];	    	        	       
	    	        	   } else {
	    	        	       return VWP::raiseWarning('Elemental Keys unsupported!',__CLASS__,null,false);	    	        	   	
	    	        	   }	    	        	   	    	        	   	
	    	        	}		    	            	
	    	        }	     	
	            }
	            
	            // process defaults
	            
	            if ($result === null && $maxOccurs == '1' && $minOccurs == '1') {
	            	if ($item->hasAttribute('default')) {
	                    $result = $item->getAttribute('default');
	                    if ($result !== null) {
	                	    $result = (string)$result;
	                    }
	            	}	
	            }	    			            	            
	    	}

	    	if ($result !== null) {
	    		$post[$name] = $result;		    		
	    	}
	    	
	    }
	    	    
	    return $post;
	}
	
	/**
	 * Apply URL Encoded Attributes
	 * 
	 * @param array $queryData Query Data
	 * @param object $complexTypeNode Node
	 * @param VServiceDataObject $partData Data
	 * @return array Query data
	 * @access public
	 */
	
	function applyUrlEncodedAttributes($queryData,$complexTypeNode,$partData) 
	{

	    $len = $complexTypeNode->childNodes->length;

	    for($idx=0;$idx < $len;$idx++) {
	    	
	    	$item = $complexTypeNode->childNodes->item($idx);

	    	if ($item->nodeType == XML_ELEMENT_NODE &&
	    	    'http://www.w3.org/2001/XMLSchema' == (string)$item->namespaceURI &&
	    	    'attribute' == (string)$item->localName
	    	) {
	    		$name = (string)$item->getAttribute('name');
	    		$value = $partData->getAttributeNS($partData->getNamespaceURI(),$name);
	    		
	    		if ($value === null) {
	    			if ($item->hasAttribute('default')) {
	    			    $value = $item->getAttribute('default');
	    			}	    			
	    		}
	    		
	    		if ($value !== null) {	    			
	    			$queryData[$name] = $value;
	    		}
	    	}	    	
	    }
	    return $queryData;	
	}
	
	/**
	 * Get Handler
	 * 
	 * @param object $portNode Node
	 * @access public
	 */
	
	function &getHandler($portNode) 
	{
		$ret = null;
	    if (is_object($portNode)) {
	       $childNodes = $portNode->childNodes;
	       $len = $childNodes->length;
	       for($idx=0;$idx<$len;$idx++) {
	           $child = $childNodes->item($idx);
	           if (self::HTTPNS == (string)$child->namespaceURI) {
	               if ($child->nodeType == XML_ELEMENT_NODE) {
	                   if ($child->localName == 'address') {
	                       $ret =& $this;
	                       return $ret;
	                   }
	               }
	           }	
	       } 
	    }
	    return $ret;	
	}
	
	/**
	 * Partial Form Data Encode
	 * 
	 * @param array $data Data
	 * @param string $prefix Prefix
	 * @access public
	 */
	
	function pencode($data,$prefix = null) 
	{		
		$newData = array();
	    foreach($data as $key=>$val) {
	    	if (is_array($val)) {
	    		$result = $this->pencode($val,$prefix == null ? $key : $prefix . '[' . $key . ']');
	    		foreach($result as $k=>$v) {
	    			$newData[$k] = $v;
	    		}
	    	} else {
	    		if ($prefix === null) {
	    		    $newData[$key] = $val;
	    		} else {
	    			$newData[$prefix.'['.$key.']'] = $val;
	    		}
	    	}
	    }
	    return $newData;	
	}
	
	/**
	 * Do Method Call
	 *
	 * @param VServiceResource $resource Resource
	 * @param string $method Method
	 * @param array $args Arguments
	 * @return mixed Return value
	 * @access public
	 */
	
	function callMethod($resource,$method,$args) 
	{
						
	    $service =& $resource->service;
	    $serviceNode = $service->getServiceElementByName($resource->serviceName);
	    $portNode = $service->getPortElementByName($serviceNode,$resource->portName);
	    
	    // Parse Port Node
	    
	    $bindingRef = $service->parseWSDLReference($portNode,(string)$portNode->getAttribute('binding'));
	    $bindingNode = $service->getWSDLElementNodeByNameNS('binding',$bindingRef->namespaceURI,$bindingRef->localName);

		if ($bindingNode === null) 
	    {	    	
	    	$ret = $service->createReply($resource,null);
	    	$ret->setReply(VWP::raiseWarning('Missing binding definition!',__CLASS__,null,false));
	    	return $ret;	    	
	    }	    
	    
	    $portTypeRef = $service->parseWSDLReference($bindingNode,(string)$bindingNode->getAttribute('type'));
	    $portTypeNode = $service->getWSDLElementNodeByNameNS('portType',$portTypeRef->namespaceURI,$portTypeRef->localName);
	    
	    if ($portTypeNode === null) 
	    {	    	
	    	$ret = $service->createReply($resource,null);
	    	$ret->setReply(VWP::raiseWarning('Missing portType definition!',__CLASS__,null,false));
	    	return $ret;	    	
	    }	    
	    	    	    
	    // Parse Binding Node
	    
	    $transportBindingNode = null;
	    $bindingOperationNode = null;
	    $len = $bindingNode->childNodes->length;
	    for($idx=0;$idx < $len; $idx++) {
	    	$child = $bindingNode->childNodes->item($idx);
	        if (self::HTTPNS == (string)$child->namespaceURI) {
	        	if ($child->nodeType == XML_ELEMENT_NODE && 'binding' == (string)$child->localName) {
	        		$transportBindingNode = $child;
	        	}
	        }
	        if ($service->wsdlNS == (string)$child->namespaceURI) {
	        	if ($child->nodeType == XML_ELEMENT_NODE && 
	        	    'operation' == (string)$child->localName &&
	        	    $method == (string)$child->getAttribute('name')) {
	        		$bindingOperationNode = $child;
	        	}	        	
	        }
	    }

	    if ($transportBindingNode === null) {
	    	$ret = $service->createReply($resource,null);
	    	$ret->setReply(VWP::raiseWarning('Missing transport binding!',__CLASS__,null,false));
	    	return $ret;
	    }
	    
		if ($bindingOperationNode === null) {
	    	$ret = $service->createReply($resource,null);
	    	$ret->setReply(VWP::raiseWarning('Missing binding operation!',__CLASS__,null,false));
	    	return $ret;
	    }	    
	    
	    // Parse portType node

	    
		$portTypeOperationNode = null;
	    $len = $portTypeNode->childNodes->length;
	    for($idx=0;$idx < $len; $idx++) {
	    	$child = $portTypeNode->childNodes->item($idx);
	        if ($service->wsdlNS == (string)$child->namespaceURI) {
	        	if ($child->nodeType == XML_ELEMENT_NODE && 
	        	    'operation' == (string)$child->localName &&
	        	    $method == (string)$child->getAttribute('name')) {
	        		$portTypeOperationNode = $child;
	        	}	        	
	        }
	    }	    
	    
		if ($portTypeOperationNode === null) {
	    	$ret = $service->createReply($resource,null);
	    	$ret->setReply(VWP::raiseWarning('Missing portType operation!',__CLASS__,null,false));
	    	return $ret;
	    }	    
	    
	    // Parse Binding Operation Node
	    	    
	    $bindingInputNode = null;
	    $bindingOutputNode = null;
	    $len = $bindingOperationNode->childNodes->length;
		for ($idx=0;$idx < $len; $idx++) {
	    	$child = $bindingOperationNode->childNodes->item($idx);
	        if ($service->wsdlNS == (string)$child->namespaceURI) {
	        	if ($child->nodeType == XML_ELEMENT_NODE && 
	        	    'input' == (string)$child->localName) {
	        		$bindingInputNode = $child;
	        	}
	        	if ($child->nodeType == XML_ELEMENT_NODE && 
	        	    'output' == (string)$child->localName) {
	        		$bindingOutputNode = $child;
	        	}	        	
	        }
	    }	    

		if ($bindingInputNode === null) {
	    	$ret = $service->createReply($resource,null);
	    	$ret->setReply(VWP::raiseWarning('Missing binding operation input specification',__CLASS__,null,false));
	    	return $ret;
	    }
	    
		if ($bindingOutputNode === null) {
	    	$ret = $service->createReply($resource,null);
	    	$ret->setReply(VWP::raiseWarning('Missing binding operation output specification!',__CLASS__,null,false));
	    	return $ret;
	    }	    
	    
	    // parse portType Operation Node
	    
		$portTypeInputNode = null;
	    $portTypeOutputNode = null;
	    $len = $portTypeOperationNode->childNodes->length;
		for ($idx=0;$idx < $len; $idx++) {
	    	$child = $portTypeOperationNode->childNodes->item($idx);
	        if ($service->wsdlNS == (string)$child->namespaceURI) {
	        	if ($child->nodeType == XML_ELEMENT_NODE && 
	        	    'input' == (string)$child->localName) {
	        		$portTypeInputNode = $child;
	        	}
	        	if ($child->nodeType == XML_ELEMENT_NODE && 
	        	    'output' == (string)$child->localName) {
	        		$portTypeOutputNode = $child;
	        	}	        	
	        }
	    }	    

		if ($portTypeInputNode === null) {
	    	$ret = $service->createReply($resource,null);
	    	$ret->setReply(VWP::raiseWarning('Missing portType operation input specification',__CLASS__,null,false));
	    	return $ret;
	    }
	    
		if ($portTypeOutputNode === null) {
	    	$ret = $service->createReply($resource,null);
	    	$ret->setReply(VWP::raiseWarning('Missing portType operation output specification!',__CLASS__,null,false));
	    	return $ret;
	    }	    
	    
	    // Parse Binding Input Node

	    $inputConfig = array();

	    $len = $bindingInputNode->childNodes->length;
		for ($idx=0;$idx < $len; $idx++) {
	    	$child = $bindingInputNode->childNodes->item($idx);
	        if (self::HTTPEXTNS == (string)$child->namespaceURI) {
	        	if ($child->nodeType == XML_ELEMENT_NODE && 
	        	    'urlEncodedAttributes' == (string)$child->localName) {	        		
	        		$inputConfig["urlEncodedAttributes"] = true;
	        	}
	        }
	        if (self::MIMEEXTNS == (string)$child->namespaceURI) {
	        	if ($child->nodeType == XML_ELEMENT_NODE && 
	        	    'multipartFormData' == (string)$child->localName) {	        		
	        		$inputConfig["mode"] = 'mime_ext';
	        		$inputConfig['type'] = 'multipartFormData';
	        		$idx = $len;
	        	}	        	
	        }
	        
			if (self::MIMENS == (string)$child->namespaceURI) {
	        	if ($child->nodeType == XML_ELEMENT_NODE && 
	        	    'content' == (string)$child->localName) {
	        	    $inputConfig["mode"] = "mime";
	        	    $inputConfig["type"] = "content";	        		
	        		$inputConfig["content_type"] = (string)$child->getAttribute('type');
	        		$inputConfig["part"] = (string)$child->getAttribute('part');	        			        		
	        		$idx = $len;
	        	}		        	
	        }	        
	    }	    

	    if (!isset($inputConfig['mode'])) 
	    {
	    	$ret = $service->createReply($resource,null);
	    	$ret->setReply(VWP::raiseWarning('Unrecognized input binding type!',__CLASS__,null,false));
	    	return $ret;	    	    
	    }

	    // Parse portType input node
		    
        $inputMessageRef = $service->parseWSDLReference($portTypeInputNode,(string)$portTypeInputNode->getAttribute('message'));
        $inputMessageNode = $service->getWSDLElementNodeByNameNS('message',$inputMessageRef->namespaceURI,$inputMessageRef->localName);

		if ($inputMessageNode === null) {
	    	$ret = $service->createReply($resource,null);
	    	$ret->setReply(VWP::raiseWarning('Missing portType operation input message specification!',__CLASS__,null,false));
	    	return $ret;
	    }        
        
	    $request_method = (string)$transportBindingNode->getAttribute('verb');
	    
	    
	    // Process Message as args
	    
	    if (isset($inputConfig['part'])) {
	    	
	    	
	    	$messagePartNode = null;
	    	
	        $len = $inputMessageNode->childNodes->length;
		    for ($idx=0;$idx < $len; $idx++) {
	    	    $child = $inputMessageNode->childNodes->item($idx);
	            if ($service->wsdlNS == (string)$child->namespaceURI) {
	        	    if ($child->nodeType == XML_ELEMENT_NODE && 
	        	        'part' == (string)$child->localName &&
	        	        $inputConfig['part'] == (string)$child->getAttribute('name')) {
	        		    $messagePartNode = $child;
	        		    $idx = $len;
	        	    }	        	
	            }
	        }	    	
	    	
	        if ($messagePartNode == null) {
	    	    $ret = $service->createReply($resource,null);
	    	    $ret->setReply(VWP::raiseWarning('Missing input message part "' .$inputConfig['part'].'"!',__CLASS__,null,false));
	    	    return $ret;	        	
	        }
	        
	        $messagePartTypeRef = $service->parseWSDLReference($messagePartNode,(string)$messagePartNode->getAttribute('type'));
	        $inputPartTypeDecl = $service->getGlobalTypeDecl($messagePartTypeRef->namespaceURI,$messagePartTypeRef->localName);
	        
	        if (VWP::isWarning($inputPartTypeDecl)) {
	    	    $ret = $service->createReply($resource,null);
	    	    $ret->setReply($inputPartTypeDecl);
	    	    return $ret;	        	
	        }
	        
	        if ('complexType' !== (string)$inputPartTypeDecl->localName) {
	    	    $ret = $service->createReply($resource,null);
	    	    $ret->setReply(VWP::raiseWarning('Complex type expected for input message part!',__CLASS__,null,false));
	    	    return $ret;	        	
	        }
	        
	        // Parse Location
	        
	        $location = null;
	        
	        $len = $portNode->childNodes->length;
	        for($idx=0;$idx < $len;$idx++) {
	        	$item = $portNode->childNodes->item($idx);
	        	if (self::HTTPNS == (string)$item->namespaceURI) {
	        		if ($item->nodeType == XML_ELEMENT_NODE &&
	        		    'address' == (string)$item->localName) {
	        		        $location = $item->getAttribute('location');
	        		        $idx = $len;
	        		    }
	        	}
	        }
	        
	        if ($location !== null) {
	        	$lparts = VURI::parse($location);
	        		        	
	        	if (isset($lparts['domain']) && $lparts['domain'] == 'env') {
	        		$vname = explode('.',$lparts['extra']);
	        		$class = array_shift($vname);
	        		$vname = implode('.',$vname);
	        		$location = $service->getVar($vname,null,$class);	        		
	        	}
	        }
	        
	        if ($location === null) {
	    	    $ret = $service->createReply($resource,null);
	    	    $ret->setReply(VWP::raiseWarning('Undefined location!',__CLASS__,null,false));
	    	    return $ret;	        	
	        }
	        
	        // Serialize
	        
	        $partData = $args[0]->getPart($inputConfig['part']); 
	        
	        $http_options = array();
	        $post = '';
	        	        	        
	        if (strtolower($request_method) == 'post') {
	        	
	        	$post = array();
	        	
	        	$sequence = $service->getSequenceDecl($inputPartTypeDecl);
	            if (VWP::isWarning($sequence)) {	        	    
	    	        $ret = $service->createReply($resource,null);
	    	        $ret->setReply($sequence);
	    	        return $ret;		        	    
	            }	        	
	        	

	            $post = $this->sequence2postVars($service,$sequence,$partData,null);
	            
	            if (VWP::isWarning($post)) {
	    	        $ret = $service->createReply($resource,null);
	    	        $ret->setReply($post);
	    	        return $ret;	            	
	            }
	            	            
	        }
	        
	        if (isset($inputConfig['urlEncodedAttributes']) && $inputConfig['urlEncodedAttributes']) {

	        	$urlParts = VURI::parse($location);
	        	$base_url = $urlParts['base_url'];
	        	
	        	$query = isset($urlParts['query']) ? $urlParts['query'] : '';
	        	
	        	$queryData = VURI::parseQuery($query);
	        	
	        	$queryData = $this->applyUrlEncodedAttributes($queryData,$inputPartTypeDecl,$partData);
	        		        	
	        	if (count(array_keys($queryData)) > 0) {
	        		$url = $base_url . '?' . VURI::createQuery($queryData); 	        		
	        	} else {
	        		$url = $base_url;
	        	}
	        } else {
	        	// should process other url encodings here
	        	$url = $location;
	        }
	        
	        $httpObj =& VHTTPClient::getInstance();

	        $post = $this->pencode($post);
	        	        	        
	        $result = $httpObj->fetch($url,$request_method, $http_options, $post);
	        $ret = $service->createReply($resource,$this);

	        if (VWP::isWarning($result)) {
	            $ret->setReply($result);
	        } else {	        	
	        	// We should really be checking the output format?	        	
	        	$doc = new DOMDocument;
	        	$doc->loadXML($result);
	        	$ret->setReply($doc);	        	
	        }	        
	        return $ret;
	    }
	    
	    // Parts as args	    	
	    $ret = $service->createReply($resource,null);
	    $ret->setReply(VWP::raiseWarning('Direct serialization of message parts is unsupported!',__CLASS__,null,false));
	    return $ret;	    	    	   
	}
	
	/**
	 * Wait For Response
	 * 
	 * @param integer $timeout Seconds
	 * @access public
	 */
	
	function wait($timeout) 
	{
	    return true; // nowait!	
	}
	
	/**
	 * Set Callback
	 * 
	 * @param mixed $callback Callback
	 * @access public
	 */
	
	function setCallback($callback) 
	{
	     $this->cb = $callback;	
	}
	
	// end class VHTTP_Service_Handler
}
