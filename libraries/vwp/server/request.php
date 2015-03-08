<?php

/**
 * Virtual Web Platform - HTTP Request Info
 *  
 * This file provides the default API for
 * Accessing HTTP request information.   
 * 
 * @package VWP
 * @subpackage Libraries.Server  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License 
 */

// Restrict access

class_exists("VWP") || die();


VWP::RequireLibrary('vwp.httprequest');

/**
 * Virtual Web Platform - HTTP Request Info
 *  
 * This file provides the default API for
 * Accessing HTTP request information.   
 * 
 * @package VWP
 * @subpackage Libraries.Server  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

class VRequest extends VHTTPRequest 
{
   
    /**
     * @var object $_static_ob Static Instance
     */
              
    static $_static_ob = null;
   
    /**
     * @var string $_request Request data
     * @access private 
     */
    
    static $_request;   

    
    /**
     * Get Instance of VRequest
     * 
     * @return object Instance    
     */   
 
    public static function &getInstance() 
    {  
        if (!isset(self::$_static_ob)) {
            self::$_static_ob = new VRequest;
        }
        return self::$_static_ob;
    }

    /**
     * Get Instance of VRequest
     * 
     * @return object Instance    
     */

    public static function &o() 
    {  
        if (!isset(self::$_static_ob)) {
            self::$_static_ob = new VRequest;
        }
        return self::$_static_ob;
    }    
            
    /**
     * Get all request info
     *   
     * <pre>
     *  Returns an array with the following keys:
     *   "time"   : Request time
     *   "uri"    : Requested URI
     *   "method" : HTTP Request method        
     * </pre>
     *     
     * @return array Request info
     * @access public  
     */
            
    public static function getAll() 
    {
        return self::$_request;
    }
    
   
    /**
     * Get request variable
     * 
     * @param string $header Header name
     * @param string $default Default value
     * @return string Header value  
     * @access public
     */
           
    function &get($var,$default = null) 
    {        
        if (isset(self::$_request[$var])) {
            return self::$_request[$var];
        }
        return $default;
    }
   
    
    /**
     * Get Path Info
     * 
     * @return string Path Info
     * @access public
     */
     
    public static function getPathInfo() 
    {
        return isset(self::$_request['path_info']) ? self::$_request['path_info'] : null;
    }
              
    /**
     * Initalize data
     *   
     * @access private
     */     
   
    public static function _init() 
    {
    
        if (isset(self::$_request)) {
            return true;
        }
        
        self::$_request = array();
        
        if (!isset(self::$_request['method'])) {
            self::$_request['method'] = $_SERVER["REQUEST_METHOD"];
        }
   
        if (!isset(self::$_request['uri'])) {
            self::$_request['uri'] = $_SERVER["REQUEST_URI"];
        }
   
        if (!isset(self::$_request['time'])) {
            self::$_request['time'] = $_SERVER["REQUEST_TIME"];
        }  
   
        if (!isset(self::$_request['path_info'])) {
            if (isset($_SERVER["PATH_INFO"])) {
                self::$_request['path_info'] = $_SERVER["PATH_INFO"];
            }
        }
        
        
        if (!isset(self::$_request['https'])) {
            if (isset($_SERVER["HTTPS"])) {
                self::$_request['https'] = $_SERVER["HTTPS"];
            }
        }             
        
        if (!isset(self::$_request['script_name'])) {
            if (isset($_SERVER["SCRIPT_NAME"])) {
                self::$_request['script_name'] = $_SERVER["SCRIPT_NAME"];
            }
        }   
    
    }
   
} // end class VRequest
 
// Initialize Request Data 
VRequest::_init();
