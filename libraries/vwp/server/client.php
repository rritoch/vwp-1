<?php

/**
 * Virtual Web Platform - Client Info
 *  
 * This file provides the default API for
 * Accessing HTTP client information.   
 * 
 * @package VWP
 * @subpackage Libraries.Server  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

// restricted access

class_exists("VWP") || die();

/**
 * Require HTTP Request Support
 */

VWP::RequireLibrary('vwp.httprequest');

/**
 * Virtual Web Platform - Client Info
 *  
 * This class provides the default API for
 * Accessing HTTP client information.   
 * 
 * @package VWP
 * @subpackage Libraries.Server  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

class VClientInfo extends VHTTPRequest 
{

    /**
     * @var integer $_init Initialized
     * @access private  
     */
     
    static $_init = null;
 
    /**
     * @var string $addr Client Address
     * @access private  
     */    
  
    static $addr = null;

    /**
     * Get Client IP Address
     *   
     * @return string Client IP Address
     * @access public  
     */
          
    public static function getAddr() 
    {
        return self::$addr;
    }

    /**
     * Get Client IP Address
     *   
     * @return string Client IP Address
     * @access public  
     */
  
    public static function _init() 
    {
        if (empty(self::$_init)) {
            self::$_init = 1;
            self::$addr = $_SERVER['REMOTE_ADDR'];
        }   
    }
    
    // end class VClientInfo
} 


// Initialize Client Info
VClientInfo::_init();