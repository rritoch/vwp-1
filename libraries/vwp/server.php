<?php

/**
 * Virtual Web Platform - Server
 *  
 * This file provides access to server data        
 * 
 * @package VWP
 * @subpackage Libraries  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */


// restricted access

class_exists("VWP") || die();

/**
 * Require HTTP Request Type
 */ 

VWP::RequireLibrary('vwp.httprequest');
 
/**
 * Virtual Web Platform - Server
 *  
 * This class provides access to server data        
 * 
 * @package VWP
 * @subpackage Libraries  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License
 */
 
class VServer extends VHTTPRequest {
   
    /**
     * Server Name
     * 
     * @var string $name Server name
     * @access public      
     */
             
    static $name = null;  
   
    /**
     * Server Address
     * 
     * @var string $addr Server name
     * @access public      
     */
   
    static $addr = null;
   
    /**
     * Server Port
     * 
     * @var string $port Server Port
     * @access public      
     */
   
    static $port = null;
   
    /**
     * Server Administrator
     * 
     * @var string $admin Server administrator
     * @access public      
     */
   
    static $admin = null;
   
    /**
     * Server Access Protocol
     * 
     * @var string $protocol Server Access Protocol
     * @access public      
     */
   
    static $protocol = null;
   
    /**
     * Server Signature
     * 
     * @var string $signature Server Signature
     * @access public     
     */
     
    static $signature = null;
   
    /**
     * Server Software
     * 
     * @var string $software Server Software
     * @access public      
     */
   
    static $software = null;
    
    
    /**
     * Initialize server data
     * 
     * @access private
     */       
    
    public static function _init() 
    {
        
        if (!isset(self::$name)) {
            self::$name = $_SERVER["SERVER_NAME"];
        }
        
        if (!isset(self::$addr)) {
            self::$addr = $_SERVER["SERVER_ADDR"];
        }
        
        if (!isset(self::$port)) {
            self::$port = $_SERVER["SERVER_PORT"];
        }
        
        if (!isset(self::$admin)) {
            self::$admin = $_SERVER["SERVER_ADMIN"];
        }
        
        if (!isset(self::$protocol)) {
            self::$protocol = $_SERVER["SERVER_PROTOCOL"];
        }
        
        if (!isset(self::$software)) {
            self::$port = $_SERVER["SERVER_SIGNATURE"];
        }
        
        if (!isset(self::$software)) {
            self::$software = $_SERVER["SERVER_SOFTWARE"];
        }
              
    }
     
     // end VServer class      
} 

// Initialize server data
VServer::_init();
