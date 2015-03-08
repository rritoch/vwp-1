<?php

/**
 * Virtual Web Platform - Model
 *  
 * This file provides Web Application data Model support
 * 
 * The model controlls access to data. Applications and widgets
 * should attain all of their data from Model's. This abstraction
 * creates a layer between an application and the data. If the 
 * underlying database or file system is changed, only the
 * models would need to be adjusted.        
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
 * Require Database Support
 */
  
VWP::RequireLibrary('vwp.dbi.dbi');

/**
 * Require Filesystem Folder Support
 */
  
VWP::RequireLibrary('vwp.filesystem.folder');

/**
 * Virtual Web Platform - Model
 *  
 * This class provides the base class for data models. All data models
 * should extend this class. 
 * 
 * The model controlls access to data. Applications and widgets
 * should attain all of their data from Model's. This abstraction
 * creates a layer between an application and the data. If the 
 * underlying database or file system is changed, only the
 * models would need to be adjusted.        
 * 
 * @package VWP
 * @subpackage Libraries  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License    
 */
  
class VModel extends VObject 
{
   
    /**
     * Model ID
     * 
     * @var string $_model_id Model ID      
     * @access private   
     */
           
    protected $_model_id = null;
    
    /**
     * Database Link
     *         
     * @var VDBI $_dbi Link to database system
     * @access public
     */
   
    public $_dbi;
    
    /**
     * Folder Driver Link
     * 
     * @var VFolder $_vfolder Folder driver
     * @access public
     */         
    
    public $_vfolder;
   
    /**
     * File Driver Link
     * 
     * @var VFile $_vfile Folder driver
     * @access public
     */
    
    public $_vfile;
                
    /** 
     * @var mixed $_state Model's state 
     * @access private
     */    
          
    protected $_state = array();
   
    /**
     * Cache of loaded models
     * 
     * @var array $_models Model Cache
     * @access private     
     */
                 
    static $_models = array();
   
    /**
     * Include path
     * 
     * @var array $_inc Include path
     * @access public
     */
            
    static $_inc = array();
    
    /**
     * Method to get the model name
     *
     * The model name by default parsed using the classname, or it can be set
     * by passing a $config['name'] in the class constructor
     *     
     * @return string The Model ID
     * @access public     
     */
   
    function getID() 
    {
        $name = $this->_model_id;
        if (empty( $name )) {
            $className = $this->getType();
            $r = null;
            if (!preg_match('/_Model_(.*(model)?.*)$/i', $className, $r)) {
                VWP::raiseError ("Cannot get or parse model class $className name.",'VModel',500);
            }
            if ((count($r) > 2) && (strpos($r[2], "model"))) {
                VWP::raiseWarning("Your classname contains the substring 'model'. ".
                            "This causes problems when extracting the classname from the name of your objects model. " .
                            "Avoid Object names with the substring 'model'.",'VModel',500);   
            }
            
            $name = strtolower( $r[1] );
        }
        return $name;
    }
   
    /**
     * Get Class Prefix
     * 
     * @param string $widgetName Widget Name (reserved for future use)     
     * @access public
     */                   
   
    function getClassPrefix($widgetName = '') 
    {
   
        $className = $this->getType();
        $r = null;
        if (!preg_match('/^(.*?)_Model_(.*(model)?.*)$/i', $className, $r)) {
            $prefix = null;
            VWP::raiseError ("Cannot get or parse model class $className name.",'VModel',500);
        } else {
            if ((count($r) > 3) && (strpos($r[3], "model"))) {
                VWP::raiseWarning("Your classname contains the substring 'model'. ".
                      "This causes problems when extracting the classname from the name of your objects model. " .
                      "Avoid Object names with the substring 'model'.",'VModel',500);
            }            
            $prefix = strtolower( $r[1] );
        }
        return $prefix;  
    }
      
    /**
     * Set a model state variable
     * 
     * @param string $s_var State variable name
     * @param mixed $val variable value
     * @return mixed State variable value
     * @access public
     */          
     
    function setState($s_var, $val) 
    {
        $this->_state[$s_var] = $val;
        return $val;
    }
   
    /**
     * Test if the provided value is an Error
     *    
     * @param mixed $val Value to test
     * @return boolean True if the value is an error
     */
                 
    function isError($val) 
    {
        return VWP::isError($val);
    }
   
    /**
     * Test if the provided value is a Warning
     *    
     * @param mixed $val Value to test
     * @return boolean True if the value is a warning
     */
                 
    function isWarning($val) 
    {
        return VWP::isWarning($val);
    }
   
     
    /**
     * Get an instance of the requested model
     * 
     * @param string $appId Application ID          
     * @param string $modelName Model ID   
     * @param array $config Configuration settings
     * @return object An instance of the model or a warning if the request failed
     * @access public        
     */
                       
    public static function &getInstance($appId, $modelName, $config = array()) 
    {                        
        $classFile = $modelName.'.php';        
        
        $vpath =& v()->filesystem()->path();
        
        $parts = explode('.',$appId);
        $file = false;
        while ((!$file) && (count($parts) > 0)) {
            $checkAppId = implode('.',$parts);
            if (isset(self::$_inc[$checkAppId])) {
                                
                $file = $vpath->find(self::$_inc[$checkAppId],$classFile);
            }
            if (!$file) array_pop($parts);
        }
    
        if (!$file) {
            $err = VWP::raiseWarning("Model $appId::$modelName not found!",'VModel',ERROR_FILENOTFOUND,false);
            return $err;
        }
   
        $classPrefix = '';
        foreach($parts as $seg) {
            $classPrefix .= ucfirst($seg) . '_';
        }
        $classPrefix .= 'Model_';
    
        $modelsPath = dirname($file);      
        if (!isset(self::$_models[$modelsPath][$modelName])) {
    
            /**
             * Load requested Model
             */
                  
            require_once($modelsPath.DS.$classFile);
    
            $className = $classPrefix.ucfirst($modelName);     
            if (class_exists($className)) {
                if (!isset(self::$_models[$modelsPath])) {
                    $models[$modelsPath] = array();
                }    
                self::$_models[$modelsPath][$modelName] = new $className($config);
            } else {
                if (!isset(self::$_models[$modelsPath][$modelName])) {    
                    self::$_models[$modelsPath][$modelName] = VWP::raiseWarning("Model $modelName ($className) not found! _inc=" .var_export(self::$_inc,true),"VModel",ERROR_CLASSNOTFOUND,false);
                }                  
            }   
        }
           
        return self::$_models[$modelsPath][$modelName];   
    }
   
    /**
     * Add Include Path
     * 
     * @param $path Include Path
     * @param $appID Application ID 
     * @access public         
     */
                 
    public static function addIncludePath($path,$appID) 
    {
        if (isset(self::$_inc[$appID])) {
            $old = self::$_inc[$appID];
      
            self::$_inc[$appID] = array($path);
            foreach($old as $path) {
                if (!in_array($path,self::$_inc[$appID])) {
                    array_push(self::$_inc[$appID],$path);
                }
            }      
        } else {
            self::$_inc[$appID] = array($path);
        }    
    }
    
    /**
     * Class constructor
     * 
     * @param array $config Configuration settings
     * @access public   
     */
                 
    function __construct($config = array()) 
    {
        parent::__construct();
        $this->_state = $config;
        $this->_dbi = & VDBI::getInstance();
        $this->_vfolder =& v()->filesystem()->folder();
        $this->_vfile =& $this->_vfolder->getFileInstance();
     
        if (isset($config["id"])) {
            $this->_model_id = $config["id"];  
        } else {
            $this->_model_id = $this->getID();
        }
    }
    
    // end VModel class 
}