<?php

/**
 * Virtual Web Platform - HTTP Client
 *  
 * This file provides the default HTTP client.   
 * 
 * @package VWP
 * @subpackage Libraries.Networking.Clients
 * @todo Implement POLLING mode  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License
 */

// Restricted Access
class_exists('VWP') || die();

/**
 * Require PHP CURL extension
 */

VWP::RequireExtension('curl');

/**
 * Virtual Web Platform - HTTP Client
 *  
 * This class provides the default HTTP client.   
 * 
 * @package VWP
 * @subpackage Libraries.Networking.Clients  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License
 */

class VHTTPClient extends VObject 
{

    /**
     * HTTP Link
     *    
     * @var mixed $_link HTTP Link
     * @access private  
     */
     
    protected $_link;
 
    /**
     * Read callback
     * 
     * Reserved for future use
     * 
     * @access private
     * @return integer Number of bytes read  
     */           

    function _cb_read($hand,$str) 
    {
        $args = func_get_args();
        return strlen($str); 
    }    
    
    /**
     * Get a HTTPClient object
     * 
     * @return VHTTPClient HTTP Client
     * @access public    
     */
      
    public static function &getInstance() 
    {
        static $httpclients = array();  
        $httpclients[] = new VHTTPClient;
        $ptr = count($httpclients) - 1;
        return $httpclients[$ptr];
    }
 
    /**
     * Fetch HTTP response
     *
     * @param string $url URL
     * @param string $method Method
     * @param array $options Options
     * @param string|array $data Data
     * @return string|object HTTP Response on success, error or warning otherwise
     * @access public
     */
    
    function fetch($url,$method = 'GET',$options = array(), $data = '') 
    {
        $method_clean = strtolower((string)$method);
        if (empty($method_clean)) {
        	return VWP::raiseWarning('Ambiguous HTTP request method!',get_class($this),null,false);
        }
        switch ($method_clean) {
        	case "get":
        		return $this->wget($url,$options);
        		break;
        	case "post":
        		return $this->wpost($url,$data,$options);
        		break;
        	default:
        		break;
        }
        
        set_time_limit(45);
        
        if (!function_exists('curl_init')) {
            return VWP::raiseError('Missing libcurl',get_class($this) . ":wget",null,false);  
        }
  
        if (!(isset($options["raw_post"]) && $options["raw_post"])) {  
            if (isset($options["files"])) {
                if (is_string($post_data)) {
                    $data = explode("&",$post_data);
                    $post_data = array();
                    foreach($data as $ent) {
                         $parts = explode("=",$ent);
                         $key = urldecode(array_shift($parts));
                         $val = urldecode(implode("=",$parts));
                         $post_data[$key] = $val;
                    }
                }
                
                $data = $post_data;
                $post_data = array();
                foreach($data as $key=>$val) {
                    if (preg_match('/^\\@.*/',$val)) {
                        $post_data[$key] = " " . $val;
                    } else {
                        $post_data[$key] = $val;
                    }
                }
                
                foreach($options["files"] as $name=>$filename) {
                    $post_data[$name] = "@" . $filename;       
                }
            } else {
               if (is_array($post_data)) {
                  $data = array();
                  foreach($post_data as $key=>$val) {
                      array_push($data,urlencode($key) . "=" . urlencode($val));
                  }
                  $post_data = implode("&",$data);
               }       
            }
        }
        
        if (isset($options["http_header"])) {
            curl_setopt($this->_link,CURLOPT_HTTPHEADER,$options["http_header"]);
        }
        
        curl_setopt($this->_link, CURLOPT_CUSTOMREQUEST, $method);        
        curl_setopt($this->_link,CURLOPT_POSTFIELDS,$post_data);
      
        curl_setopt($this->_link,CURLOPT_URL,$url);
        curl_setopt($this->_link,CURLOPT_RETURNTRANSFER,true);
    
        $result = curl_exec($this->_link);
        if ($result === false) {
            return VWP::raiseError(curl_error($this->_link),get_class($this).":wpost",curl_errno($this->_link),false);
        }  
        return $result;
    }
        
    /**
     * Set Agent name
     * 
     * @param string $name Agent name
     * @return true|object Returns true on success, error or warning otherwise
     * @access public        
     */
      
    function setAgent($name) 
    {
        if (!function_exists('curl_init')) {
            return VWP::raiseError('Missing libcurl',get_class($this) . ":wget",null,false);  
        }   
        curl_setopt($this->_link,CURLOPT_USERAGENT,$name);
        return true;  
    }
 
    /**
     * Request web document using HTTP Get Method
     *    
     * @param string $url URL
     * @param array $options
     * @access public
     */
        
    function wget($url, $options = array()) 
    {
        VWP::SetTimeLimit(45);
  
        if (!function_exists('curl_init')) {
            return VWP::raiseError('Missing libcurl',get_class($this) . ":wget",null,false);  
        } 
  
        curl_setopt($this->_link,CURLOPT_POSTFIELDS,null);
        curl_setopt($this->_link,CURLOPT_URL,$url);
        curl_setopt($this->_link,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($this->_link,CURLOPT_HTTPGET,true);

        $follow = isset($options['followlocation']) ? $options['followlocation'] : true;
        curl_setopt($this->_link,CURLOPT_FOLLOWLOCATION,$follow);
  
        $result = curl_exec($this->_link);
        if ($result === false) {
            return VWP::raiseError(curl_error($this->_link),get_class($this).":wpost",curl_errno($this->_link),false);
        }
  
        return $result;  
    }
 
    /**
     * Request web document using HTTP Post Method
     *    
     * @param string $url URL
     * @param array $post_data Post Data
     * @param $options
     * @return string|object HTTP Response Document on success, error or warning otherwise
     * @access public
     */
        
    function wpost($url, $post_data = '', $options = array()) 
    {
        set_time_limit(45);
        if (!function_exists('curl_init')) {
            return VWP::raiseError('Missing libcurl',get_class($this) . ":wget",null,false);  
        }
  
        if (!(isset($options["raw_post"]) && $options["raw_post"])) {  
            if (isset($options["files"])) {
                if (is_string($post_data)) {
                    $data = explode("&",$post_data);
                    $post_data = array();
                    foreach($data as $ent) {
                         $parts = explode("=",$ent);
                         $key = urldecode(array_shift($parts));
                         $val = urldecode(implode("=",$parts));
                         $post_data[$key] = $val;
                    }
                }
                
                $data = $post_data;
                $post_data = array();
                foreach($data as $key=>$val) {
                    if (preg_match('/^\\@.*/',$val)) {
                        $post_data[$key] = " " . $val;
                    } else {
                        $post_data[$key] = $val;
                    }
                }
                
                foreach($options["files"] as $name=>$filename) {
                    $post_data[$name] = "@" . $filename;       
                }
            } else {
               if (is_array($post_data)) {
                  $data = array();
                  foreach($post_data as $key=>$val) {
                      array_push($data,urlencode($key) . "=" . urlencode($val));
                  }
                  $post_data = implode("&",$data);
               }       
            }
        }
        
        if (isset($options["http_header"])) {
            curl_setopt($this->_link,CURLOPT_HTTPHEADER,$options["http_header"]);
        }
        curl_setopt($this->_link,CURLOPT_POST,true);  
        curl_setopt($this->_link,CURLOPT_POSTFIELDS,$post_data);
      
        curl_setopt($this->_link,CURLOPT_URL,$url);
        curl_setopt($this->_link,CURLOPT_RETURNTRANSFER,true);
    
        $result = curl_exec($this->_link);
        if ($result === false) {
            return VWP::raiseError(curl_error($this->_link),get_class($this).":wpost",curl_errno($this->_link),false);
        }  
        return $result;
    }

    /**
     * Class constructor
     * 
     * @access public
     */       

    function __construct() 
    {
        parent::__construct();
        $this->_link = null;
        if (function_exists('curl_init')) {
            $this->_link = curl_init(); 
        }  
    }
 
    /**
     * Class destructor
     * 
     * @access public
     */
         
    function __destruct() 
    {
        if ($this->_link !== null) {
            @curl_close($this->_link);
        }
    }
    
    // end class VHTTPClient
}
