<?php

/**
 * Virtual Web Platform - URI Interface
 *  
 * This file provides the URI interface
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
 * Require Server Request Support
 */

VWP::RequireLibrary('vwp.server.request');

/**
 * Virtual Web Platform - URI Interface
 *  
 * This class provides the URI interface
 *        
 * @package VWP
 * @subpackage Libraries  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License   
 */

class VURI extends  VObject 
{
    
    /**
     * Get Local Base URL
     * 
     * @return string Base URL
     * @access public    
     */
         
    public static function base() 
    {
                
        $https = VRequest::o()->get('https','');                
        $proto = strtolower($https) == 'on' ? 'https://' : 'http://';  
        $host = VRequestHeaders::o()->get('host','');
        $base = v()->filesystem()->path()->clean(dirname(VRequest::o()->get('script_name','')),'/');           
        return $proto . $host . $base;
    }

    /**
     * Get Current URL
     * 
     * @return string Current URL
     * @access public    
     */    
    
    public static function currentURI() {
        
    	$https = VRequest::o()->get('https','');                
        $proto = strtolower($https) == 'on' ? 'https://' : 'http://';  
        $host = VRequestHeaders::o()->get('host','');        
        $uri = VRequest::o()->get('uri','');           
        return $proto . $host . $uri;
    }
    
    /**
     * Get Current URL Offset
     * 
     * @return string Current URL
     * @access public    
     */    
    
    public static function currentURIOffset() {
        
    	$https = VRequest::o()->get('https','');                
        $proto = strtolower($https) == 'on' ? 'https://' : 'http://';  
        $host = VRequestHeaders::o()->get('host','');        
        $uri = VRequest::o()->get('uri','');           
        return substr($proto . $host . $uri,strlen(self::base()));
    }    
    
    /**
     * Get New Extension
     * 
     * @param string $url Source URL
     * @param string $ext Destination Extension
     * @access public
     */
    
    public static function changeExtension($url,$ext) {
   	    $u = self::parse($url);
   	    
   	    $parts = explode('/',$u['path']);
   	    $last = array_pop($parts);
   	    if (empty($last)) {
   	    	array_push($parts,'index.'.$ext);   	    	
   	    } elseif (false === ($ptr = strrpos($last,'.'))) {
   	    	array_push($parts,$last.'.'.$ext);
   	    } else {
   	    	$last = substr($last,0,$ptr);
   	    	array_push($parts,$last.'.'.$ext);
   	    }
   	    
   	    $u['path'] = implode('/',$parts);
   	    
   	    $ret = '';
   	    if (isset($u['proto'])) {
   	    	$ret = $u['proto'].'://' . $u['domain'] . $u['path'];   	    	
   	    } else {
            $ret = $u['path'];   	    	
   	    }
   	    
   	    if (isset($u['query'])) {
   	    	$ret .= '?' . $u['query'];   	    	
   	    }
   	    
   	    if (isset($u['anchor'])) {
   	    	$ret .= '#' . $u['anchor'];
   	    }
   	       	    
   	    return $ret;
    }
        
    /**
     * Parse URI
     * 
     * @todo make RFC Compliant     
     * @param string $uri URI
     * @return array URI Parts          
     * @access public          
     */
          
    public static function parse($uri) 
    {
         
        $ret = array();
   
        // parse anchor
   
        $tmpa = explode("#",$uri);   
        $page = array_shift($tmpa);   
        if (count($tmpa) > 0) {
            $ret["anchor"] = implode("#",$tmpa);
        }
   
        // parse query
   
        $tmpa = explode("?",$page);   
        $ret["base_url"] = array_shift($tmpa);   
        if (count($tmpa) > 0) {
            $ret["query"] = implode("?",$tmpa);
        }
    
        $dpath = explode("/",$ret["base_url"]);      
        $tmpa = explode(":",$dpath[0]);   
     
        if (count($tmpa) > 1) {
            $ret["proto"] = array_shift($tmpa);
            $ret["extra"] = implode(":",$tmpa);
            array_shift($dpath);        
            $ret["extra2"] = array_shift($dpath);
            if (count($dpath) > 0) {
                $ret["domain"] = $dpath[0];
            } else {
                $ret["domain"] = $ret["proto"];
                unset($ret["proto"]);
            }
            $dpath[0] = "";
            $ret["path"] = implode("/",$dpath);    
        } else {
            $ret["path"] = $ret["base_url"]; 
        }
        return $ret; 
    }
    
    /**
     * Make Query Item
     *    
     * @param $key Urlencoded key name
     * @param $val Value
     * @return array Query items  
     * @access private  
     */
            
    public static function _makeQueryItem($key,$val) 
    {
        
        if (is_string($val)) {
            return array($key . '=' . urlencode($val));
        }
     
        $ret = array();
     
        if (is_array($val)) {
            $namedKeys = false;
      
            $ctr = 0;
            $keys = array_keys($val);
            $last = count($keys);
            for($ctr = 0; $ctr < $last; $ctr++) {        
                if ((is_array($val[$keys[$ctr]])) || 
                 (! (is_numeric($keys[$ctr]) && ($keys[$ctr] == $ctr)))) {
                    $ctr = $last;
                    $namedKeys = true;
                }    
            }
        
            if ($namedKeys) {    
                foreach($val as $k=>$v) {
                    $ret = array_merge($ret,self::_makeQueryItem($key.'[' . urlencode($k) . ']',$v));
                }   
            } else {    
                foreach($val as $v) {
                    $ret = array_merge($ret,self::_makeQueryItem($key.'[]',$v));
                }   
            }     
        }
        
        $args = func_get_args();  
        return $ret;  
    }
    
    /**
     *  Create a query string
     *  
     *  @param array $vars Query Variables
     *  @return string Query string
     *  @access public
     */                               
    
    public static function createQuery($vars) 
    {
        if (!is_array($vars)) {
            return null;
        }
     
        $items = array();  
        foreach($vars as $key=>$val) {
            $i = self::_makeQueryItem(urlencode($key),$val);   
            $items = array_merge($items,$i);
        }  
        return implode('&',$items);  
    }
    
    /**
     * Parse a query string
     * 
     * @param string $query Query string
     * @return array Query Variables
     * @access public     
     */
                              
    public static function parseQuery($query) 
    {
        $vars = array();   
        $parts = explode('&',$query);
        $p = null;
        foreach($parts as $part) {
            // Split key and value
      
            if (false !== ($klen = strpos($part,'='))) {
                $val = urldecode(substr($part,$klen + 1));
                $key = substr($part,0,$klen);
            } else {
                $val = null;
                $key = $part;
            }
            
            $kparts = explode('[',$key);
            if (strlen($kparts[0]) > 0) {
                if (count($kparts) < 2) {
   
                    // Simple key
                    $k = trim($key);
                    if (!isset($vars[$k])) {
                        $vars[$k] = $val;
                    } 
   
                } else {
       
                    // Array key
             
                    unset($p);
                    $p = &$vars;          
                    $skip = false;
                    $lastk = false;
                    foreach($kparts as $rawk) {
                        if (!$skip) {
                            if ($lastk !== false) {
                                $p =& $p[$lastk];
                            }
                            $k = rtrim(trim($rawk),']');
                            if (strlen($k) > 0) {
                                if (!isset($p[$k])) {
                                    $p[$k] = array();
                                }
                                if (is_array($p[$k])) {
                                    $lastk = $k;
                                } else {
                                    $skip = true;
                                }
                            } else {
                                $lastk = count($p);
                                $p[$lastk] = array();                        
                            }
                        }
                    }
                    
                    if (!$skip) {
                        // add value to vars      
                        $p[$lastk] = $val;            
                    }
        
                }
            }
        }
        return $vars;
    }
    
    // end class VURI           
} 
