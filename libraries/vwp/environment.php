<?php

/**
 * Virtual Web Platform - Environment
 *  
 * This file provides Core Environment support   
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
 * Require File Support
 */
  
VWP::RequireLibrary('vwp.filesystem.file');

/**
 * Require Server Request Support
 */
  
VWP::RequireLibrary('vwp.server.request');

/**
 * Require Routing Support
 */
  
VWP::RequireLibrary('vwp.ui.route');

/**
 * Virtual Web Platform - Environment
 *  
 * This class provides Core Environment support   
 * 
 * @package VWP
 * @subpackage Libraries  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License   
 */

class VEnv extends VObject 
{
 
    /**
     * Environment ID
     * 
     * @var integer $id Environment ID
     * @access private         
     */
        
    var $id;
  
    /**
     * Environment Data
     * 
     * @var array $env Environment Data   
     * @access private      
     */
        
    var $env = array();
  
    /**
     * System Environments
     *
     * @var array $env_heap System Environments
     * @access private
     */              
  
    static $env_heap;

    /**
     * Parent environment
     *
     * @var VEnv $parent Parent environment   
     * @access private
     */
         
    var $parent = null;
 
    /**
     * Original GET method data
     * 
     * @var array $_get Original GET method data
     * @access private  
     */
 
    static $_get;
  
    /**
     * Original POST method data
     * 
     * @var array $_post Original POST method data
     * @access private  
     */
 
    static $_post;
 
    /**
     * Original route data
     * 
     * @var array $_route Original Route data
     * @access private  
     */
 
    static $_route;
  
    /**
     * File Driver Object
     * 
     * @var VFile $_vfile File object
     * @access private  
     */   
 
    static $_vfile;
             
    /**
     * Set Current Environment
     * 
     * @param VEnv $env Environment
     * @access private   
     */
             
    public static function _setCurrent($env) 
    {
        if (is_object($env)) {   
            self::$env_heap["current"] = $env->id;
        } else {
            self::$env_heap["current"] = null;
        }
    }
 
    /**
     * Get Current Environment ID
     *
     * @return integer Environment ID      
     * @access private
     */
                        
    protected static function _getCurrentEnvId() 
    {
        if (!isset(self::$env_heap["current"])) {
            self::_setCurrent(new VEnv);
        } 
        return self::$env_heap["current"];
    }
    
    /**
     * Open a new environment
     * 
     * Creates a child environment
     * 
     * @return object Child Environment Object     
     * @access public
     */
             
    public static function open() 
    {
        $old_env = self::_getCurrentEnvId();  
        $new_env = new VEnv;
        self::_setCurrent($new_env);  
        foreach(self::$env_heap["env"][$old_env]["data"]->env as $key=>$val) {
            if (!isset($new_env->env[$key])) {
                $new_env->env[$key] = $val;
            }
        }
        $new_env->parent = $old_env;
        return $new_env;  
    }

    /**
     * Close a child environment
     *
     * @param object $env Child Environment Object   
     * @access public
     */
 
    public static function close($env) 
    {  
        self::_setCurrent($env->parent);
    }

    /**
     * Flush Environment Variables
     * 
     * If $method is empty all methods will be flushed, otherwise
     * only the requested method is flushed.
     *                
     * @param string $method Method    
     * @access public
     */
                        
    public static function flush($method = null) 
    {
        $envptr = self::_getCurrentEnvId();
        
        if (empty($method)) {
            $methods = array_keys(self::$env_heap["env"][$envptr]["data"]->env);
        } else {
            $methods = array($method);
        }
        
        foreach($methods as $method) {            
            self::$env_heap["env"][$envptr]["data"]->env[$method] = array();    
        }        
    }
    
    /**
     * Get all Environment Variables of a method
     * 
     * @param string $method Method name, defaults to 'any'
     * @return array Environment variables
     * @access public   
     */
                   
    public static function &getChannel($method = 'any') 
    {   
        $envptr = self::_getCurrentEnvId();                
        return self::$env_heap["env"][$envptr]["data"]->env[$method];                  
    }
  
    /**
     * Get an environment Variable
     * 
     * @param string $vname Variable name
     * @param mixed $default Default value
     * @param string $method Method name, defaults to any
     * @return mixed Variable value
     * @access public      
     */
                     
     public static function getVar($vname,$default = null,$method = 'any') 
     {      
       $envptr = self::_getCurrentEnvId();   
       if (isset(self::$env_heap["env"][$envptr]["data"]->env[$method][$vname])) {
           return self::$env_heap["env"][$envptr]["data"]->env[$method][$vname];
       }   
       return $default;      
     }

     /**
      * Get list of selected checkboxes
      * 
      * @param string|array $checkboxList Checkboxes
      * @param string $method Method  
      * @return array Selected Checkboxes
      */         

     public static function getChecked($checkboxList,$method = 'any') 
     {
        if (!is_array($checkboxList)) {  
            if (!is_string($checkboxList)) {
                return array();
            }
            $checkboxList = array($checkboxList);
        }
              
        $checked = array();
  
        foreach($checkboxList as $ck) {
            $data = self::getVar($ck,null,$method);

            if (!empty($data)) {
                if (is_array($data)) {
                    $current = array_keys($data);     
                } else {
                    $current = array($ck);
                }
    
                $checked = array_merge($checked,$current);
            }
        }
        return $checked;  
    }
 

    /**
     * Get a file upload filename
     *        
     * @param string $vname Upload name
     * @return string|object Filename on success, warning or error on failure.  
     */         

    public static function getUploadFilename($vname) 
    {
        if (!isset($_FILES[$vname])) {
            return VWP::raiseError("File Not Found",null,null,false);
        }
        return $_FILES[$vname]["name"];
    }

    /**
     * Get a file upload
     *
     * If destination not provided returns file contents.
     * If a destination is provided returns true on success.
     * This function may also return a warning or error if the file is not found.
     *        
     * @param string $vname Upload name
     * @param string $dest Destination filename
     * @return string|true|object Result  
     */         

    public static function getUpload($vname,$dest = null) 
    {
        if (!isset($_FILES[$vname])) {
            return VWP::raiseError("File Not Found",null,null,false);
        }
  
        $src = $_FILES[$vname]["tmp_name"];
        if (!file_exists($src)) {
            return VWP::raiseWarning("File has already been taken!",null,null,false);
        }
  
        if (empty($dest)) {

            $result = self::$_vfile->read($src);                        
            if (VWP::isWarning($result)) {
                $err = VWP::getLastError();
                return $result;
            }
            return $result;
        }
  
        VWP::noWarn();
        $result = @move_uploaded_file($src,$dest);
        VWP::noWarn(false);
        if ($result === false) {
            $err = VWP::getLastError();
            return VWP::raiseError($err[1],null,null,false);
        }
        return true;     
    }
 
    /**
     * Get an environment Variable as a word
     * 
     * @param string $vname Variable name
     * @param mixed $default Default value
     * @param string $method Method name, defaults to any
     * @return string Word value
     * @access public      
     */
      
    public static function getWord($vname,$default = null, $method = 'any') 
    {
        $val = self::getVar($vname,$default,$method);
        if (is_array($val)) {
            $val = array_shift($val);
        }   
        if (!is_string($val)) {
            return $default;
        }         
        $words = explode(" ",$val);
        return $words[0];
    }

    /**
     * Get an environment Variable as a command
     * 
     * @param string $vname Variable name
     * @param mixed $default Default value
     * @param string $method Method name, defaults to any
     * @return string Command Value
     * @access public      
     */
  
  
    public static function getCmd($vname,$default = false, $method = 'any') 
    {
        $val = self::getVar($vname,$default,$method);

        if (is_array($val)) {
            $val = array_shift($val);
        } 
           
        if (!is_string($val)) {
            return $default;
        }
   
        $words = explode(" ",$val);
        return $words[0];
    }
    
    /**
     * Set the value of a variable
     * 
     * @param string $vname Variable Name
     * @param mixed $value Variable value
     * @param string $method Method, defaults to 'any'
     * @return boolean True on success               
     * @access public   
     */
          
    public static function setVar($vname,$value,$method = 'any') 
    {
   
        $envptr = self::_getCurrentEnvId();            

        if (!isset(self::$env_heap["env"][$envptr]["data"]->env[$method])) {
            self::$env_heap["env"][$envptr]["data"]->env[$method] = array();
        }
      
        self::$env_heap["env"][$envptr]["data"]->env["any"][$vname] = $value;
        self::$env_heap["env"][$envptr]["data"]->env[$method][$vname] = $value;   
        return true;
    }
 
    /**
     * Strip Slashes
     * 
     * @param string|array $val String or array
     * @return string|array String or strings with slashes removed
     * @access private  
     */
            
    public static function _stripslashes($val) 
    {
        if (is_array($val)) {
            $new_val = array();
            foreach(array_keys($val) as $key) {
                $new_val[$key] = self::_stripslashes($val[$key]);
            }
            return $new_val;
        } else {
            return stripslashes($val);
        } 
    }
   
    public static function reroute() 
    {
        if (!isset(self::$_vfile)) {
             $envptr = self::_getCurrentEnvId();   
        }
        
    	// Yes I know this notify should be in the router, but its better called from here.
    	
    	$uri = VURI::currentURI();    	    	    
    	VRoute::setCurrentURI($uri);    	    	    	
    	VNotify::Notify('decode_url','route'); // Shhh... Don't tell anyone its here!    	
    	$uri = VRoute::getCurrentURI();
    	
    	
    	$parts = explode('/',$uri);
    	array_shift($parts);
    	array_shift($parts);
    	$parts[0] = '';    	
    	$uri = implode('/',$parts);

    	unset($parts);
    	
        $format = null; 
        $path = '';
        $segments = array();

        $router =& VRoute::getInstance();
        
        self::$_get = $_GET;
        
        if ($uri !== null) {                
            $script_name = VRequest::getScriptName();
            $parts = explode('?',$uri);            
                                           
            if ($script_name !== null) {
                $l = strlen($script_name);
                                                
                if (substr($parts[0],0,$l) != $script_name || (strlen($parts[0]) > $l)) {
                    $format = self::$_vfile->getExt($parts[0]);
                    if (substr($parts[0],0,$l) == $script_name) {
                        $path = self::$_vfile->stripExt(substr($parts[0],$l));
                    } else {
                        $cparts = explode('/',$script_name);
                        array_pop($cparts);
                        $base = implode('/',$cparts);
                        $l = strlen($base);
                        $path = self::$_vfile->stripExt(substr($parts[0],$l));
                    }
                    $segments = explode('/',$path);                            
                }
                if (count($parts) > 1) {
        	           $q = VUri::parseQuery($parts[1]);
        	           self::$_get = $q; 
                } else {
        	           self::$_get = array();
                }                
            }                
        }
                

        
        foreach(self::$env_heap["env"] as $envptr=>$value) {
            self::$env_heap["env"][$envptr]['data']->env["get"] = self::$_get;
            foreach(self::$_get as $key=>$val) {
            	if (!isset(self::$env_heap["env"][$envptr]['data']->env["any"][$key])) {
                    self::$env_heap["env"][$envptr]['data']->env["get"][$key] = $val;
            	}
            }               	
        }

        self::$_route = $router->decode($segments);        

        self::$_route["format"] = $format;

        foreach(self::$env_heap["env"] as $envptr=>$value) {
            self::$env_heap["env"][$envptr]['data']->env["route"] = self::$_route;
            foreach(self::$_route as $key=>$val) {
            	if (!isset(self::$env_heap["env"][$envptr]['data']->env["any"][$key])) {
                    self::$env_heap["env"][$envptr]['data']->env["any"][$key] = $val;
            	}
            }               	
        }             
    }
    
    /**
     * Class Constructor
     * 
     * @access public
     */
                
    function __construct() 
    {
        parent::__construct();
  
        if (!isset(self::$_vfile)) {
            self::$_vfile = VFilesystem::local()->file();
        }
       
        if (!isset(self::$_post)) {
            self::$_post = $_POST;
        }
  
        if (!isset(self::$_get)) {
            self::$_get = $_GET;
        }
  
        if (!isset(self::$env_heap)) {
            self::$env_heap = array("env"=>array());
        }
        
        $this->id = count(self::$env_heap["env"]);
        $this->env = array();
        $this->env["get"] = self::$_get;
        $this->env["any"] = self::$_get;
        if (get_magic_quotes_gpc()) {
            $this->env["post"] = array();
            foreach(self::$_post as $key=>$val) {
                $this->env["any"][$key] = self::_stripslashes($val);
                $this->env["post"][$key] = self::_stripslashes($val);    
            }
        } else {
            $this->env["post"] = array();
            foreach(self::$_post as $key=>$val) {
                $this->env["any"][$key] = $val;
                $this->env["post"][$key] = $val;
            }   
        }  
  
        array_push(self::$env_heap["env"],array("data"=>$this));    
    }

    // end class VEnv
} 
