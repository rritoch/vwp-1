<?php

/**
 * Virtual Web Platform - Post Data
 *  
 * This file provides the default API for
 * Accessing HTTP Post Data.   
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
 * Virtual Web Platform - Post Data
 *  
 * This class provides the default API for
 * Accessing HTTP Post Data.   
 * 
 * @package VWP
 * @subpackage Libraries.Server  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

class VRawPost extends VHTTPRequest 
{

    /**
     * @var integer $_init Initialized
     * @access private  
     */
     
    static $_init = null;
 
    /**
     * @var string $data Raw Post Data
     * @access private  
     */    
  
    static $data = null;


    /**
     * Get Client IP Address
     *   
     * @return string Client IP Address
     * @access public  
     */
          
    public static function getData() 
    {
        return self::$data;
    }

    /**
     * Get Client IP Address
     *   
     * @return string Client IP Address
     * @access public  
     */
  
    public static function _init() 
    {
        global $HTTP_RAW_POST_DATA;
        if (empty(self::$_init)) {
            self::$_init = 1;   
            if($HTTP_RAW_POST_DATA) {
                self::$data = $HTTP_RAW_POST_DATA;
            } else {
                self::$data = file_get_contents("php://input");
            }
        }
    }   
 
    // end class VRawPost
} 

// Initialize Post Data
VRawPost::_init();
