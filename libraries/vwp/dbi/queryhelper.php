<?php

/**
 * VWP - DBI Query
 *  
 * This file provides query helper functions        
 * 
 * @package VWP
 * @subpackage Libraries.DBI  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */


/**
 * VWP - DBI Query
 *  
 * This class provides query helper functions        
 * 
 * @package VWP
 * @subpackage Libraries.DBI  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

class VDBI_QueryHelper extends VObject
{

	/**
	 * Query
	 * 
	 * @var VDBI_Query
	 */
	
	protected $query;
	
	/**
	 * Query Configuration
	 * 
	 * @var object $_doc DOM Document
	 * @access private
	 */
	
	protected $_doc;
	
	/**
	 * Env Data
	 * 
	 * @access private
	 */
	
	protected $_data = array();
	
	
	/**
	 * Required Nodes
	 * 
	 * @var array $required_core_nodes Required Core Nodes
	 * @access private
	 */
	
	static $required_core_nodes = array(
	    'title',
	    'datasources',
	    'fields');

	/**
	 * Required Nodes
	 * 
	 * @var array $all_core_nodes All Core Nodes
	 * @access private
	 */	
	
	static $all_core_nodes = array(
	    'title',
	    'datasources',
	    'tables',
        'table_relationships',
        'input_filters',      
        'summary_options',
        'groupings',
        'output_filters',
        'labels',
	    'values');	
	
	/**
	 * Get DOM Configuration Node
	 * 
	 * @return object Configuration Node on success, error or warning otherwise
	 * @access private
	 */
	
	public function _getCfgNode() 
	{
        // Get configuration node
	    
	    $nodeList = $this->_doc->getElementsByTagNameNS(VDBI_Query::NS_QUERY_1_0,'query');
	    	    
	    if ($nodeList->length != 1) {
	    	return VWP::raiseWarning('Report configuration not found!'.' ('.$nodeList->length.')',get_class($this),null,false);
	    }
	    return $nodeList->item(0);		
	}

	/**
	 * Get DOM Core Node
	 * 
	 * @param string $nodeName Node Name
	 * @return object Core Node on success, error or warning otherwise
	 * @access private
	 */	
	
	function _getCoreNode($nodeName) 
	{
		
		if (!in_array($nodeName,self::$all_core_nodes)) {
		    return VWP::raiseWarning('Unknown core node!',__CLASS__,null,false);	
		}
		
	    $cfgNode = $this->_getCfgNode();
	    if (VWP::isWarning($cfgNode)) {
	    	return $cfgNode;
	    }
	    
	    $nodeList = $cfgNode->getElementsByTagNameNS(VDBI_Query::NS_QUERY_1_0,$nodeName);
        	    	    
	    if ($nodeList->length > 1) {
            return VWP::raiseWarning("Invalid Configuration: Ambiguous core field $nodeName!",get_class($this),null,false);
        }
	    
        if ($nodeList->length > 0) {
	    	return $nodeList->item(0);
        }
	    	    	    
	    if (in_array($nodeName,self::$required_core_nodes)) {
            return VWP::raiseWarning('Invalid Configuration: Missing required core node!',__CLASS__,null,false);   	    	
	    }
	    
	    return null;	    	    
	}
	
	/**
	 * Make Core Node
	 * 
	 * @param $nodeName
	 * @return object Core Node on success, error or warning otherwise
	 * @access private
	 */		
	
	public function _makeCoreNode($nodeName) 
	{
		
		$node = $this->_getCoreNode($nodeName);
		if ($node !== null) {
			return $node;
		}
		
		$cfgNode = $this->_getCfgNode();
	    if (VWP::isWarning($cfgNode)) {
            return $cfgNode;
        }		
		
        $previousSibling = null;        
        $tmp = self::all_core_nodes;
        $started = false;
        
        while($previousSibling === null && count($tmp)) {
        	$check = array_pop($tmp);
        	if ($started) {
        	    $previousSibling = $this->_getCoreNode($check);	
        	} else {
        		if ($check == $nodeName) {
        			$started = true;
        		}
        	}        	        	
        }
        
        if (VWP::isWarning($previousSibling)) {
        	return $previousSibling;
        }
  				
		$nextSibling = VDomHelper::getInstance()->getNextElementSibling($prevousSibling);
		
		$newNode = $this->_doc->createElementNS(VDBI_Query::NS_QUERY_1_0,$nodeName);
		
		if ($nextSibling === null) {
			$cfgNode->appendChild($newNode);
		} else {
			$cfgNode->insertBefore($newNode,$nextSibling);
		}
		
		$this->$nodeName = array();
		return $newNode;
	}

	/**
	 * Resolve Value of Node
	 * 
	 * @param DOMNode $node
	 * @param boolean $followRef True to follow ref attributes
	 * @access public
	 */
	
	function _resolveValue($node,$followRef = true) 
	{
	    if ($followRef && $node->hasAttribute('ref')) {
	    	$ref = $node->getAttribute('ref');
	    	if (substr($ref,0,1) != '#') {	    		
	    		return null;
	    	}
	    	$id = substr($ref,1);
	    	$newNode = $node->ownerDocument->getElementById($id);

	    	if ($newNode === null) {	    		
	    		return null;
	    	}
	    	return $this->_resolveValue($newNode,true);
	    }
	    	    
	    if ('var' == (string)$node->localName) {
	    	
	    	if ('env' == (string)$node->parentNode->localName) {
	    		$class = $node->getAttribute('class');
	    		if ($class === null) {
	    			$ret = $this->getVar((string)$node->getAttribute('name'));
	    		} else {
	    		    $ret = $this->getVar((string)$node->getAttribute('name'),null,(string)$class);
	    		}
	    		return $ret;	    			    		
	    	}
	    }

        $value = '';
        $cnodes = $node->childNodes;
        $len = $cnodes->length;
        for($i=0; $i<$len; $i++) {
          	$item = $cnodes->item($i);
           	if ($item->nodeType == XML_TEXT_NODE) {
           		$value .= (string)$item->data;
           	}
        }
        
	    return $value;
	}
	
	/**
	 * Set Resolve Value of Node
	 * 
	 * @param DOMNode $node
	 * @param boolean $followRef True to follow ref attributes
	 * @access public
	 */
	
	function _setResolveValue($node,$value,$followRef = true) 
	{		
	    if ($followRef && $node->getAttribute('ref') !== null) {
	    	$ref = $node->getAttribute('ref');
	    	if (substr($ref,0,1) != '#') {
	    		return null;
	    	}
	    	$id = substr($ref,1);
	    	$newNode = $node->ownerDocument->getElementById($id);
	    	if ($newNode === null) {
	    		return null;
	    	}
	    	return $this->_setResolveValue($newNode,$value,true);
	    }
	    
		if ('var' == (string)$node->localName) {

	    	if ('env' == (string)$node->parentNode->localName) {
	    		$class = $node->getAttribute('class');
	    		if ($class === null) {
	    			return $this->setVar((string)$node->getAttribute('name'),$value);
	    		} else {
	    		    return $this->setVar((string)$node->getAttribute('name'),$value,(string)$class);
	    		}
	    	}
	    }	    
	    
	    $newNode = $node->cloneNode(false);
	    $text = $this->_doc->createTextNode($value);
	    $newNode->appendChild($text);
	    $node->parentNode->replaceChild($newNode,$node);
	    
	    return $value;
	}	
	
	/**
	 * Activate ID Attributes
	 * 
	 * @param object $node DOM Node
	 * @access public
	 */
	
	function _activateIds($node) 
	{
		if ($node->nodeType == XML_ELEMENT_NODE) {
			if ($node->hasAttribute('id')) {
				$result = $node->setIdAttribute('id',true);								
			}
		}
		$len = $node->childNodes->length;
		for($idx=0;$idx < $len; $idx++) {
			$child = $node->childNodes->item($idx);
			if ($child->nodeType == XML_ELEMENT_NODE) {
				$this->_activateIds($child);
			}
		} 
	}	
	
	/**
	 * Set Var
	 * 
	 * @param string $name Variable Name
	 * @param mixed $value Value
	 * @param string $class Namespace
	 * @access public
	 */	
	
	function setVar($name,$value,$class = null) 
	{
		if (is_null($class)) {
			$class = 'default';
		} else {
			$class = (string)$class;
		}

		$data = $this->_data;
		if (!isset($data[$class])) {
			$data[$class] = array();
		}
		
		$data[$class][(string)$name] = $value;
		$this->_data = $data;
		return $value;
	}
	
	/**
	 * Get Var
	 * 
	 * @param string $name Variable Name
	 * @param mixed $default Default Value
	 * @param string $class Namespace
	 * @access public
	 */		
	
	function getVar($name,$default,$class = null) 
	{
		if (is_null($class)) {
			$class = 'default';
		} else {
			$class = (string)$class;
		}
		$data = $this->_data;
		if (isset($data[$class][$name])) {
			return $data[$class][$name];
		} 
		return $default;
	}	
	
	/**
	 * Set DOM Document
	 * 
	 * @param object $doc
	 * @access public
	 */
	
	function setDOMDocument(&$doc) 
	{	
		$this->_activateIds($doc);					
		$this->_doc =& $doc;
					
	}
	
	/**
	 * Get DOM Document
	 * 
	 * @return object DOM Document
	 * @access public
	 */
	
	function &getDOMDocument() 
	{
		return $this->_doc;			
	}
	
	/**
	 * Class Constructor
	 * 
	 * @param unknown_type $query
	 * @access public
	 */
	
	function __construct($query) 
	{
		$this->query =& $query;
	}
	
	// end class VDBI_QueryHelper
}
