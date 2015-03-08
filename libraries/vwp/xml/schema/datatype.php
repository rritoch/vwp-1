<?php

/**
 * Virtual Web Platform - Schema Datatype
 *  
 * This file provides XML Schema Datatype support
 *        
 * @package VWP
 * @subpackage Libraries.XML.Schema  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */


/**
 * Virtual Web Platform - Schema Datatype
 *  
 * This class provides XML Schema Datatype support
 *        
 * @package VWP
 * @subpackage Libraries.XML.Schema  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */


class VSchema_DataType extends VObject 
{

	/**
	 * Id
	 * 
	 * @var string $id Id
	 * @access public
	 */
	
    public $id = null;
    
    /**
     * Name
     * 
     * @var string $name Name
     * @access public
     */
    
    public $name = null;
    
    /**
     * Namepsace 
     * 
     * @var string Namespace
     * @access public
     */
    
    public $namespace = null;
    
    /**
     * Schema Node
     * 
     * @var object Schema Node
     * @access public
     */
    
    public $schemaNode = null;
    
    /**
     * Class Constructor
     * 
     * @param object $schemaNode Schema Node
     * @param string $targetNamespace Target Namespace
     * @access public
     */

    function __construct($schemaNode = null,$targetNamespace = null) 
    {
        
        if (!empty($schemaNode)) {
            $this->schemaNode = $schemaNode;        
            $name = $schemaNode->getAttribute('name');            
            $parts = explode(':',$name);        
            $name = array();        
            $this->name = array_pop($parts);
            $prefix = implode(':',$parts);
            $this->namespace = empty($prefix) ? $targetNamespace : $schemaNode->lookupNamespaceURI($prefix);
            $this->id = $this->namespace . '#types.' . $this->name;
        }
    }
    
    // end class VSchema_Datatype
}
