<?php

/**
 * Virtual Web Platform - SOAP Client VWP mapitemtype
 *  
 * This file provides a mapitemtype object for
 * use in mappings   
 * 
 * @package VWP
 * @subpackage Libraries.Networking.Clients  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */


/**
 * Virtual Web Platform - SOAP Client VWP mapitemtype
 *  
 * This class provides a mapitemtype object for
 * use in mappings   
 * 
 * @package VWP
 * @subpackage Libraries.Networking.Clients  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License    
 */
 
class VSOAPVWPType_mapitemtype extends VType 
{
	/**
	 * Key
	 * 	 
	 * @var string Key
	 * @access public
	 */
	
    public $key;
    
    /**
     * Value
     *      
     * @var string Value
     * @access public
     */
    
    public $value;
 
    /**
     * Class Constructor
     * 
     * @param string $key Key
     * @param string $value Value
     * @access public
     */
    
    function __construct($key = null,$value = null) 
    {
        $this->key = $key;
        $this->value = $value;  
    }
    
    // End class VSOAPVWPType_mapitemtype
    
}