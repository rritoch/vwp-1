<?php

/**
 * Virtual Web Platform - WSDL Message
 *  
 * This file provides WSDL Message support
 *        
 * @package VWP
 * @subpackage Libraries.XML.WSDL  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch 2011 - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

/**
 * Virtual Web Platform - WSDL Message
 *  
 * This class provides WSDL Message support
 *        
 * @package VWP
 * @subpackage Libraries.XML.WSDL  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch 2011 - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */


class VWSDL_Message extends VObject 
{
	/**
	 * Message Node
	 * 
	 * @var object Message Node
	 * @access public
	 */
	
	protected $node;
	
	/**
	 * WSDL Document
	 * 
	 * @var VWSDL $wsdl WSDL Document
	 * @access public
	 */
	
	protected $wsdl;
	
	/**
	 * Message Name
	 * 
	 * @var string Message Name
	 * @access public
	 */
	
    protected $name;
    
    /**
     * Message parts
     * 
     * @var array Message parts
     * @access public
     */

    protected $parts = array();
    
    /**
     * Get Part
     * 
     * @param string $name Part Name
     * @return VServiceDataObject Data
     * @access public
     */
    
    function &getPart($name) 
    {
    	if (isset($this->parts[$name])) {
    		return $this->parts[$name];
    	}
    	
        $nodes = $this->node->childNodes;
        $len = $nodes->length;
                
        for($idx=0;$idx<$len;$idx++) {
        	$child = $nodes->item($idx);
        	if ($child->nodeType == XML_ELEMENT_NODE &&
        	    'part' == (string)$child->localName &&
                 $this->wsdl->wsdlNS == (string)$child->namespaceURI          	    
        	    ) {
        	    $curName = (string)$child->getAttribute('name');
        	    if ($curName == $name) {
        	    	$type = $child->getAttribute('type');
        	    	
        	    	$parts = explode(':',$type);
        	    	
        	    	if (count($parts) == 2) {
        	    		
        	    		$prefix = $parts[0];
        	    		$ns = $child->lookupNamespaceURI($prefix);
        	    		if ($ns !== null) {
        	    			$ns = (string)$ns;
        	    		}
        	    		
        	    		$type = $parts[1];
        	    	} else {
        	    		$ns = null;
        	    	}
        	    	
        	        $this->parts[$name] = $this->wsdl->createDataObject($ns,$type);
        	        return $this->parts[$name];   	
        	    }        	        	
        	}
        }

        $ret = null;
        return $ret;        
    }
    
    /**
     * Class Constructor
     * 
     * @param VWSDL $wsdl WSDL
     * @param object $node message node
     * @access public
     */
    
    function __construct($wsdl,$node) 
    {
        $this->wsdl = $wsdl;
        $this->node = $node;        	
    }
    
    // end class VWSDL_Message    
}
