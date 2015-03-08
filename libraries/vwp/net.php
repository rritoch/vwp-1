<?php

/**
 * Virtual Web Platform - Network Support
 *  
 * This file provides access to networking
 * features.
 *   
 * @package VWP
 * @subpackage Libraries  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License    
 */


// Restrict access
class_exists('VWP') || die(); // restrict access

/**
 *  Require System Registry
 */
  
VWP::RequireLibrary('vwp.sys.registry');


/**
 *  Require Filesystem Path Support
 */
  
class_exists('VPath') || VWP::RequireLibrary('vwp.filesystem.path');
 
/**
 * Virtual Web Platform - Network Support
 *  
 * This class provides access to networking
 * features.
 *   
 * <pre>
 *  
 *  Registry keys can be used to add support
 *  for new clients and protocols.
 *  
 *  Client Key: NETWORK\Clients\[client_id]
 *  Protocol Key: NETWORK\Protocols\[protocol_id]
 *  
 *  The registry key should have a value named 'location'
 *   which is the file which will be loaded
 *   when the client or protocol is requested.
 *  
 *  Required Headers:
 *   
 *    VWP::RequireLibrary('vwp.net');
 *       
 *  Usage: 
 *        
 *    VNet::RequireClient(string $client_id);
 *    VNet::RequireProtool(string $client_id);
 *                    
 * </pre>
 *   
 * @package VWP
 * @subpackage Libraries  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License    
 */
 
class VNet extends VObject 
{
    
    /**
     * @var array $_client_cache Client Cache
     * @access private
     */
             
    static $_client_cache = array();
   
    /**
     * @var array $_protocol_cache Protocol Cache
     * @access private
     */
     
    static $_protocol_cache = array();
     
    /**
     * Load a networking client interface
     * 
     * @param string $client_id Client ID
     * @return boolean|object True on success, error or warning otherwise
     * @access public              
     */
         
    public static function RequireClient($client_id) 
    {
        
        if (empty($client_id)) {
            return VWP::raiseError("Invalid client requested!","NET",null,false);
        }
        
        
        if (!isset(self::$_client_cache[$client_id])) {
        
            // Search Registry
         
            $localMachine = & Registry::LocalMachine();
            $key = "NETWORK\\Clients\\" . $client_id;
         
            $result = Registry::RegOpenKeyEx($localMachine,
                              $key,
                              0,
                              0, //samDesired
                              $registryKey);
                               
            if (!VWP::isWarning($result)) {
             
                $data = array();
                $idx = 0;
                $keylen = 255;
                $vallen = 255;
                $lptype = REG_SZ; 
                while (!VWP::isError($result = Registry::RegEnumValue(
                                          $registryKey,
                                          $idx++,
                                          $key,
                                          $keylen,
                                          0, // reserved
                                          $lpType,
                                          $val,
                                          $vallen)))  {
                    if (!VWP::isWarning($result) || $result->errno = ERROR_MORE_DATA) {                                       
                        $data[$key] = $val;
                    }
                    $keylen = 255;
                    $vallen = 255;  
                }
          
                Registry::RegCloseKey($registryKey);
          
                if (isset($data["location"])) {
                    $location = v()->filesystem()->path()->clean($data["location"]);
                    if (file_exists($location)) {
                        require_once($location);
                        self::$_client_cache[$client_id] = true;
                    }
                }  
            }  
        
            Registry::RegCloseKey($localMachine);
       
         
            // Search Core
         
            $filename = strtolower($client_id) . '.php';   
            $paths = VWP::getPaths('library');
         
            if (defined('VPATH_LIB')) {
                array_push($paths,VPATH_LIB);
            }
         
            foreach($paths as $libpath) {
                if (!isset(self::$_client_cache[$client_id])) {   
                    if (file_exists($libpath.DS.'vwp'.DS.'net'.DS.'clients'.DS.$filename)) {     
                        require_once($libpath.DS.'vwp'.DS.'net'.DS.'clients'.DS.$filename);
                        self::$_client_cache[$client_id] = true;           
                    }
                }
            }
         
            if (!isset(self::$_client_cache[$client_id])) {             
                self::$_client_cache[$client_id] = VWP::raiseError("Unable to locate $client_id client!","NET",null,false);
            }
        }
        return self::$_client_cache[$client_id];
    }
   
    /**
     * Load a networking protocol interface
     * 
     * @param string $protocol_id Protocol ID
     * @return boolean|object True on success, error or warning otherwise     
     * @access public         
     */
     
    public static function RequireProtocol($protocol_id) 
    {
        
        if (empty($protocol_id)) {
            return VWP::raiseError("Invalid protocol requested!","NET",null,false);
        }
        
        if (!isset(self::$_protocol_cache[$protocol_id])) {
        
            // Search Registry
         
            $localMachine = & Registry::LocalMachine();
            $key = "NETWORK\\Protocols\\" . $protocol_id;
         
            $result = Registry::RegOpenKeyEx($localMachine,
                              $key,
                              0,
                              0, //samDesired
                              $registryKey);
                               
            if (!VWP::isWarning($result)) {
             
                $data = array();
                $idx = 0;
                $keylen = 255;
                $vallen = 255;
                $lptype = REG_SZ; 
                while (!VWP::isError($result = Registry::RegEnumValue(
                                          $registryKey,
                                          $idx++,
                                          $key,
                                          $keylen,
                                          0, // reserved
                                          $lpType,
                                          $val,
                                          $vallen)))  {
                    if (!VWP::isWarning($result) || $result->errno = ERROR_MORE_DATA) {                                       
                        $data[$key] = $val;
                    }
                    $keylen = 255;
                    $vallen = 255;  
                }
          
                Registry::RegCloseKey($registryKey);
          
                if (isset($data["location"])) {
                    $location = v()->filesystem()->path()->clean($data["location"]);
                    if (file_exists($location)) {
                        require_once($location);
                        self::$_protocol_cache[$protocol_id] = true;
                    }
                }  
            }  
         
            // Search Core
         
            $filename = strtolower($protocol_id) . '.php';   
            $paths = VWP::getPaths('library');
         
            if (defined('VPATH_LIB')) {
                array_push($paths,VPATH_LIB);
            }
         
            foreach($paths as $libpath) {
                if (!isset(self::$_protocol_cache[$protocol_id])) {   
                    if (file_exists($libpath.DS.'vwp'.DS.'net'.DS.'protocols'.DS.$filename)) {     
                        require_once($libpath.DS.'vwp'.DS.'net'.DS.'protocols'.DS.$filename);
                        self::$_protocol_cache[$protocol_id] = true;           
                    }
                }
            }
         
            // Give up!
            if (!isset(self::$_protocol_cache[$protocol_id])) {             
                self::$_protocol_cache[$protocol_id] = VWP::raiseError("Unable to locate $protocol_id protocol!","NET");
            }
        }
        return self::$_protocol_cache[$protocol_id];
    }
    
    // end VNet class  
} 