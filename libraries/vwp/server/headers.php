<?php

/**
 * Virtual Web Platform - HTTP Request Headers
 *  
 * This file provides the default API for
 * Accessing HTTP request headers.   
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
 * Virtual Web Platform - HTTP Request Headers
 *  
 * This file provides the default API for
 * Accessing HTTP request headers.   
 * 
 * @package VWP
 * @subpackage Libraries.Server  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

class VRequestHeaders extends VHTTPRequest 
{

    /**
     * @var object $_static_ob Static Instance
     */
     
    static $_static_ob;
       
    /**
     * @var array $_headers Request headers indexed by header name
     * @access private  
     */
        
    static $_headers = null;
   
    /**
     * Get Instance of VRequestHeaders
     * 
     * @return object Instance    
     */   
    
    public static function &getInstance() 
    {  
        if (!isset(self::$_static_ob)) {
            self::$_static_ob = new VRequestHeaders;
        }
        return self::$_static_ob;
    }
   
    /**
     * Get Instance of VRequestHeaders
     * 
     * @return object Instance    
     */
   
    public static function &o() 
    {  
        if (!isset(self::$_static_ob)) {
            self::$_static_ob = new VRequestHeaders;
        }
        return self::$_static_ob;
    }
    
    /**
     * Get header
     * 
     * @param string $header Header name
     * @param string $default Default value
     * @return string Header value  
     * @access public
     */
           
    function &get($header,$default = null) 
    {
        $header = strtoupper($header);
        $header = str_replace('-','_',$header);
     
        if (isset(self::$_headers[$header])) {
            return self::$_headers[$header];
        }
        return $default;
    }
   
    /**
     * Check if header was received
     * 
     * @param string $header Header name
     * @return boolean True if header was received.  
     * @access public
     */
    
    function exists($header) 
    {
        return isset(self::$_headers[strtoupper($header)]);
    }
    
    /**
     * Initialize Headers
     * 
     * @access private
     */
             
    public static function _init() 
    {
        if (empty(self::$_headers)) {
            self::$_headers = array();
            foreach($_SERVER as $key=>$val) {
                $key = strtoupper($key);
                if (substr($key,0,5) == "HTTP_") {
                    $key = substr($key,5);
                    self::$_headers[$key] = $val;
                }
            }
        }
    }
    // end class VRequestHeaders   
} 
  
// Initialize request header data
VRequestHeaders::_init();