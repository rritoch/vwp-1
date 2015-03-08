<?php

/**
 * DOM 3 DOMImplementationList
 * 
 * @package VWP
 * @subpackage Libraries.DOM 
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License 
 */

/**
 * DOM 3 DOMImplementation
 * 
 * @package VWP
 * @subpackage Libraries.DOM
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License 
 */

class VDOMImplementationList extends VObject
 {
	/**
	 * Implementations
	 * 
	 * @var array Implementations
	 * @access private
	 */
	
    private $_implist = array();
    
    /**
     * Length
     * 
     * The number of DOMImplementations in the list
     * 
     * @var integer $length
     * @access public
     */
    
    protected $length;
    
    /**     
     * Get Attribute
     * 
     * Note: This method is not defined by the DOM Specification. 
     *       DOM Conformant applications should not access this method directly.
     * 
     * @param string $property Property
     * @return mixed Property value
     */  
    
    function &get($property) {
        switch($property) {
        
            case "length":
                $ret = count($this->_implist);
                break;
            default:
                $ret = null;
                break;
        }
        return $ret;
    }

    /**     
     * Set Property
     * 
     * Note: This method is not defined by the DOM Specification. 
     *       DOM Conformant applications should not access this method directly.
     * 
     * @param string $property Property
     * @param mixed $value Value     
     */   
        
    function set($property,$value) 
    {    
        return null;
    }
    
    /**
     * Get Implementation
     * 
     * Returns the indexth item in the collection. If index is 
     * greater than or equal to the number of DOMImplementations 
     * in the list, this returns null.
     *  
     * @param integer $index Index
     * @return object DOM Implementation
     */
    
    function &item($index) 
    {
        $index = (int)$index;
        $ret = null;
        if ($index < 0) {
            return $ret;
        }
        if (($index + 1) > count($this->_implist)) {
            return $ret;
        }
        $ret = $this->_implist[$index]; 
        return $ret;
    }

    // end class VDomDOMImplementationList
}

