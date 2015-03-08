<?php

/**
 * Virtual Web Platform - Application Type
 *  
 * This file provides the base class for Web Applications
 * 
 * The class name for the
 * entry point of an application must use the naming 
 * convention [appname]Application and must extend
 * the VApplication class.  
 * 
 * The entry point for the application is the
 * method main($argv,$env) where $argv are the virtual
 * command line arguments, and $env is set of two dimensional
 * environment variables $env[method][variable]. The entry point
 * may be public or protected but should not be static or private.                 
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
 * Requires Widget Support
 */ 

VWP::RequireLibrary('vwp.ui.widget');

/**
 * Virtual Web Platform - Application Type
 *  
 * This is the base class for Web Applications
 * 
 * The class name for the
 * entry point of an application must use the naming 
 * convention [appname]Application and must extend
 * the VApplication class.  
 * 
 * The entry point for the application is the
 * method main($argv,$env) where $argv are the virtual
 * command line arguments, and $env is set of two dimensional
 * environment variables $env[method][variable]. The entry point
 * may be public or protected but should not be static or private.                 
 * 
 * @package VWP
 * @subpackage Libraries  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License   
 */
 
class VApplication extends VWidget 
{

    /**       
     * @var integer $_nosave_piID Parent Instance ID
     * @access private     
     */
     
    private $_nosave_piID;
              
    /**
     * 
     * 
     * @var integer $_nosave_current Current application instance ID
     * @access private        
     */
        
    static $_nosave_current = null;

    /**
     * @var string $_nosave_current_app_path Current application path
     * @access private        
     */
        
    static $_nosave_current_app_path;

    /**
     * @var array $_nosave_applications Application instance buffer
     * @access private        
     */
    
    static $_nosave_applications = array();

    /**
     * @var array $_nosave_application_paths Loaded application paths
     * @access private        
     */
        
    protected static $_nosave_application_paths = array();
  
    /**
     * Set Parent Instance ID
     *           
     * @param integer $piID Parent Instance ID     
     * @access private          
     */
  
    protected function _setpiID($piID) 
    {
        $this->_nosave_piID = $piID;
    }

    /**
     * Get Parent Instance ID
     * 
     * @return integer Parent PID
     * @access public              
     */
              
    protected function getpiID() 
    {
        return $this->_nosave_piID; 
    }    
    
    
    /**
     * Get Current Application
     * 
     * @return VApplication Current Application
     * @access public   
     */        

    public static function &getCurrent() 
    {
        return self::$_nosave_applications[self::$_nosave_current];
    }
        
    /**
     * Get Application Path
     * 
     * @return string Application path
     * @access public
     */
              
    public function getBasePath() 
    {
        return $this->_basePath;
    } 
  
    /**
     * Get Application Id
     * 
     * @return string Application Id
     * @access public        
     */
                       
    function getID() 
    {
        $className = $this->getType();
        $p = explode("Application",$className);        
        return $p[0];
    }

    /**
     * Execute Application - Internal
     * 
     * @param array $argv Arguments
     * @param array $envp Environment
     * @return mixed Application Return Value             
     * @access private   
     */
                 
    protected function _execute(&$argv,&$envp) 
    {

        if ($this->_blockAccess) {
            return VWP::raiseWarning(VText::_('Access Forbidden'),$this->getType(), 403, false);
        }
  

        if (method_exists($this,'main')) {         
            $result = $this->main($argv,$envp);
        } else {
            return VWP::raiseError('Entry point not found!',$this->getType(),500,false); 
        }
                  
        // Redirect if set by the application
          
        $this->redirect();
          
        return $result;    
    }         
    
    /**
     * Get Current Application ID
     * 
     * @static
     * @return string Application ID
     * @access public     
     */
                         
    public static function getCurrentApplicationID() 
    {
        if (self::$_nosave_current === null) {
            return null;
        } 
        
        if (!is_object(self::$_nosave_applications[self::$_nosave_current])) {
            return null;
        }
               
        return self::$_nosave_applications[self::$_nosave_current]->getID();
    }
       
    /**
     * Get Current Application Path
     * 
     * @static
     * @return string Current application path
     * @access public      
     */
        
    public static function getCurrentApplicationPath() 
    {
        return self::$_nosave_current_app_path;
    }
  
    /**
     * Class Constructor
     * 
     * @param array $config Configuration settings
     * @access public
     */
                             
    function __construct($config = array()) 
    {        
        parent::__construct($config);                                                                
        parent::registerDefaultTask('main');
    }
    
    // end VApplication class
      
} 