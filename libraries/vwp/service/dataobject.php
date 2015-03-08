<?php

/**
 * Virtual Web Platform - Service Data Object
 *  
 * This file provides Data Objects for Services
 *        
 * @package VWP
 * @subpackage Libraries.Service  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch 2011 - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

/**
 * Virtual Web Platform - Service Data Object
 *  
 * This class provides Data Objects for Services
 *        
 * @package VWP
 * @subpackage Libraries.Service  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch 2011 - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

class VServiceDataObject extends VObject 
{

	
	/**
	 * Namespace
	 * 
	 * @var string $namespaceURI Namespace
	 */
	
	protected $namespaceURI;
	
	/**
	 * Schema Type
	 * 
	 * @var string $schemaType Schema Type
	 * @access public
	 */
	
	protected $schemaType;

	/**
	 * Instance Namespace
	 * 
	 * @var string $instanceNamespaceURI Namespace
	 * @access public
	 */
	
	protected $instanceNamespaceURI;

	/**
	 * Instance Namespace
	 * 
	 * @var string $instanceSchemaType Schema Type
	 * @access public
	 */
		
	protected $instanceSchemaType;

	/**
	 * Value
	 * 
	 * @var mixed $value
	 * @access public
	 */
	
	protected $value;

	/**
	 * Attributes
	 * 
	 * @var array $attributes Attributes
	 * @access public
	 */
	
	protected $attributes = array();
	
	
	/**
	 * Schema
	 *
	 * @var VSchema Schema Schema
	 * @access public
	 */
	
	protected $schema;	
	
	/**
	 * Set Value 
	 *	 
	 * @param string $namespaceURI Instance NamespaceURI
	 * @param string $schemaType Instance Type
	 * @param mixed $value Instance Value
	 */
	
	function setValue($namespaceURI,$schemaType,$value) 
	{
		$this->instanceNamespaceURI = $namespaceURI;
		$this->instanceSchemaType = $schemaType;
		$this->value = $value;
	} 

	/**
	 * Set Attribute
	 * 
	 * @param string $namespaceURI Namespace
	 * @param string $name Attribute Name
	 * @param string $value Value
	 * @access public
	 */
		
	function setAttributeNS($namespaceURI,$name,$value) 
	{
		if ($value === null) {
		    $len = count($this->attributes);
		    for($idx=0;$idx<$len;$idx++) {
			    if ($this->attributes[$idx]->namespaceURI == $namespaceURI && $this->attributes[$idx]->name == $name) {
				    $this->attributes[$idx]->value = $value;
			    }
		    }
		    return;				
		}
		$value = (string)$value;
		
		$len = count($this->attributes);
		for($idx=0;$idx<$len;$idx++) {
			if ($this->attributes[$idx]->namespaceURI == $namespaceURI && $this->attributes[$idx]->name == $name) {
				$this->attributes[$idx]->value = $value;
			}
		}		
		$ob = new stdClass;
		$ob->namespaceURI = $namespaceURI;
		$ob->name = $name;
		$ob->value = $value; 
		$this->attributes[$idx] = $ob;
	} 

	/**
	 * Get Attribute
	 * 
	 * @param string $namespaceURI Attribute Namespace
	 * @param string $name Attribute Name
	 * @return string Value
	 * @access public
	 */
	
	function getAttributeNS($namespaceURI,$name)
	{
		$len = count($this->attributes);
		for($idx=0;$idx<$len;$idx++) {
			if ($this->attributes[$idx]->namespaceURI == $namespaceURI && $this->attributes[$idx]->name == $name) {
				return $this->attributes[$idx]->value;
			}
		}
		return null;			
	} 

	/**
	 * Get Namespace
	 * 
	 * @return string Namespace
	 * @access public
	 */
	
	function getNamespaceURI() 
	{
		return $this->namespaceURI;
	}
	
	/**
	 * Get Schema Type
	 * 
	 * @return string Schema Type
	 * @access public
	 */
	
	function getSchemaType() 
	{		
		return $this->schemaType;
	}
	
	/**
	 * Get Instance Namespace
	 * 
	 * @return string Namespace
	 * @access public
	 */	
	
	function getInstanceNamespaceURI() 
	{
		return $this->valueNamespaceURI;
	}
	
	/**
	 * Get Instance Schema Type
	 * 
	 * @return string Schema Type
	 * @access public
	 */	
	
	function getInstanceSchemaType() 
	{
		return $this->instanceSchemaType;
	}
	
	/**
	 * Get Value
	 * 
	 * @return mixed Value
	 * @access public
	 */
	
	function getValue() 
	{
		return $this->value;
	}
	
	/**
	 * Set Property Variable
	 * 
	 * @param string $vname Variable Name
	 * @param mixed $value Value
	 * @access public
	 */
	
	function &set($vname,$value) 
	{		
	    switch($vname) {
	    	case "schema":
	    	case "schemeType":
	    	case "namespaceURI":
	    		$ret =& $value;
	    		break;	
	    	default:	
		        $ret = parent::set($vname,$value);		        
		        break;
	    }
		return $ret;		        
	}
	
	/**
	 * Append Schema Data
	 * 
	 * @param VServiceDataObject $obj Data Object
	 * @param string $name Value Name
	 * @access public
	 */
	
	function schemaTypeAppendChild($obj,$name = null) 
	{
		if (!is_array($this->value)) {
	        $this->value = array();	
		}
		$this->value[] = array($obj,$name);
	}
	
	/**
	 * Class Constructor
	 * 
	 * @param VSchema $schema Schema
	 * @param string $namespaceURI Namespace
	 * @param string $schemaType Schmea Type
	 * @access public
	 */
	
	function __construct($schema,$namespaceURI,$schemaType) 
	{
		$this->schema = $schema;
		$this->namespaceURI = $namespaceURI;
		$this->schemaType = $schemaType;
	}
	
	// end class VServiceDataObject
}
