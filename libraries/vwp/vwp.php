<?php

/**
 * Virtual Web Platform - System Controller
 *  
 * This file provides the primary core system interface        
 * 
 * @package VWP
 * @subpackage Libraries  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License 
 */
 
/**
 * Require object support
 */
  
require_once(dirname(__FILE__).DS.'object.php');

/**
 * Virtual Web Platform - System Interface
 *  
 * This file provides the primary core system interface        
 * 
 * @package VWP
 * @subpackage Libraries  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */
 
class VWP extends VObject 
{
     
    /**
     * System paths
     * 
     * @var array $_paths;   
     * @access private
     */
             
    static $_paths = array();

    /**
     * Current Document
     * 
     * @var obect $_document;   
     * @access private
     */
  
    public $_document;  

    /**
     * Current Language
     * 
     * @var string $language;   
     * @access private
     */

    static $language;

    /**
     * Current Theme
     * 
     * @var string $theme;   
     * @access private
     */

    static $theme;
 
    /**
     * Current Theme Type
     * 
     * @var string $theme_type;   
     * @access private
     */
  
    static $theme_type;
 
    /**
     * System Error messages
     * 
     * @var array $_error_messages;   
     * @access private
     */
  
    static $_error_messages = array();

    /**
     * System Warnings
     * 
     * @var array $_warnings;   
     * @access private
     */

    static $_warnings = array();
  
    /**
     * System Notices
     * 
     * @var array $_notices System notices
     * @access private
     */      
               
    static $_notices = array();
  
    /**
     * Debug notices
     * 
     * @var array $_debug System debug notices
     * @access private
     */  
     
    static $_debug = array();
  
    /**
     * Warning status
     * 
     * @var boolean $_nowarn Block next warning
     * @access private
     */
     
    static $_nowarn = false;
  
    /**
     * Last Error
     * 
     * @var object $_last_error Last error
     * @access private
     */
                 
    static $_last_error;
 
    /**
     * Hold mainframe
     * 
     * Used for system switches
     * 
     * @var VWP $_hold_mainframe  
     * @access private
     */
             
    static $_hold_mainframe;

    /**
     * PHP Strict error reporting
     *      
     * @var boolean $_strict Strict error reporting  
     * @access private
     */
 
    static $_strict = true;


    /**
     * Library cache
     * 
     * @var array $_lib_cache Library cache 
     * @access private
     */

    static $_lib_cache = array();
 
 
    /**
     * Script Time Limit
     * 
     * @var integer $_time_limit Time limit
     * @access private  
     */

    static $_time_limit;
 
    /**
     * Original Script Time Limit
     * 
     * @var integer $_time_limit_start_time
     * @access private  
     */
 
    static $_time_limit_start_time;
 
    /**
     * Redirect URL
     * 
     * @var string $_redirect Redirect URL
     * @access private
     */
     
    static $_redirect = null;


    /**
     * Kernel
     * 
     * @var VKernel Kernel
     * @access private
     */
              
    public $_kernel = null;
    
    /**
     * VWP Instance
     * 
     * @var VWP $_vwp_inst Mainframe
     * @access private
     */
 
    static $_vwp_inst;
 
    /**
     * Set Redirect
     * 
     * @param string $url Redirect URL
     * @access public               
     */
     
    public static function redirect($url) 
    {
        if (empty(self::$_redirect)) {
            self::$_redirect = $url;
        }
    }

    /**
     * Get Redirect URL
     * 
     * @return string Redirect URL
     * @access public               
     */
 
    public static function getRedirectURL() 
    {
        return self::$_redirect;
    }
 
    /**
     * Set time limit
     * 
     * @param integer $timeout Seconds
     * @return integer|object Previous value on success, error or warning on failure  
     */
         
    public static function setTimeLimit($timeout) 
    {
  
        if (!is_numeric($timeout)) {
            return VWP::raiseWarning("Invalid time limit",'VWP::setTimeLimit',null,false);
        }
        self::$_time_limit_start_time = time();
  
        $old = self::$_time_limit;
        if ($timeout > 0) {
            self::$_time_limit = $timeout;   
        } else {
            self::$_time_limit = 0;
        }
        set_time_limit(self::$_time_limit);
        return $old;
    }
 
    /**
     * Reset time limit
     * 
     * Restores the time limit to the value set when
     * the first VWP object was created.
     */
           
    public static function resetTimeLimit() 
    {
        static $orig_limit;
  
        self::$_time_limit_start_time = time();
  
        if (!isset($orig_limit)) {
            VWP::noWarn();
            $orig_limit = @ ini_get('max_execution_time');
            VWP::noWarn(false);
            if (empty($orig_limit)) {
                $orig_limit = 45;
            } 
        }
        set_time_limit($orig_limit);
        self::$_time_limit = $orig_limit;  
    } 
 
    /**
     * Add Cleanup Task
     * 
     * @param string|array Task function
     * @param string|array Abort Function
     * @access public           
     */    

    public static function addCleanupTask($taskFunc, $abortFunc = null) 
    {
        VHibernateDriver::addCleanupTask($taskFunc, $abortFunc);
    }
 
    /**
     * Continue processing after connection closes
     * 
     * @param boolean $blockStop Continue after connection closes       
     * @access private    
     */  
  
    public static function noStop($blockStop = true) 
    {
        if ($blockStop) {
            ignore_user_abort(true);
        } else {
            ignore_user_abort(false);
        }
    }
                   
    /**
     * Set Strict PHP Mode
     *
     * @param boolean $val Strict Mode
     * @access public
     */         
 
    public static function setStrict($val = true) 
    {
        if ($val) {
            self::$_strict = true;
        } else {
            self::$_strict = false;
        }
    }
 
    /**
     * Pause VWP Application
     * 
     * @access public  
     */
 
    public static function pause() 
    {
    	unset(self::$_hold_mainframe);        
        self::$_hold_mainframe =& $GLOBALS['mainframe'];
        restore_error_handler();
        unset($GLOBALS['mainframe']); 
    }
  
    /**
     * Resume VWP Application
     *   
     * @access public  
     */

    public static function resume() 
    {        
        $GLOBALS['mainframe'] =& self::$_hold_mainframe;
        set_error_handler(  array("VWP","dispatchError"),E_ALL | E_STRICT);
    }
            
    /**
     * Get configuration object
     * 
     * @return VConfig Configuration object
     * @access public   
     */
                
    public static function &getConfig() 
    {
        static $config;
        
        if (!isset($config)) {
            $config = new VConfig;
        }
        return $config;
    } 
  
    /**
     * Get Applications Var Path
     * 
     * @param string $app Application name
     * @access public
     */
                 
    public static function getVarPath($app = null) 
    {
        $path = VPATH_BASE.DS.'var';
   
        if (!empty($app)) {
            $path .= DS.$app;
        } 
        return $path;
    }


    /**
     * Get last error
     * 
     * @return object Last Error
     */       
  
    public static function getLastError() 
    {
        return self::$_last_error;
    }
  
    /**
     * Set block next warning status
     * 
     * @param boolean $status True to block reporting system warning
     * @access public
     */
                 
    public static function noWarn($status = true) 
    {
        self::$_nowarn = $status;
    }

    /**
     * Get system error messages
     * 
     * @return array Error messages
     * @access public
     */
                   
    public static function getErrorMessages() 
    {
        return self::$_error_messages;
    }
 
    /**
     * Get system warnings
     * 
     * @return array System warnings
     * @access public
     */
                 
    public static function getWarnings() 
    {
        return self::$_warnings;
    }

    /**
     * Get system notices
     * 
     * @return array System notices
     * @access public
     */
  
    public static function getNotices() 
    {
        return self::$_notices;
    }

    /**
     * Add a new system notice
     * 
     * @param string $msg System notice   
     * @return boolean True on success
     * @access public
     */

    public static function addNotice($msg) 
    {  
        array_push(self::$_notices,$msg);
        return true;   
    }
  
    /**
     * Get debug notices
     * 
     * @return array system notices
     * @access public         
     */
        
    public static function getDebugNotices() 
    {
        return self::$_debug;
    }

    /**
     * Add debug notice
     * 
     * @param string $debug_msg Debug message
     * @param string $errfile Source filename
     * @param integer $errline Source line number      
     * @access public         
     */
  
    public static function debug($debug_msg,$errfile = null, $errline = null) 
    {
        $msg = var_export($debug_msg,true);
        if ($errfile !== null)$msg .= " : in $errfile";
        if ($errline !== null) $msg .= " on line $errline";
        array_push(self::$_debug,$msg);
    }
  
    /**
     * Add a library path
     * 
     * @param string $path Library path   
     * @access public
     */
                 
    public static function add_library_path($path) 
    {
        if (!isset(self::$_paths["library"])) {
            self::$_paths["library"] = array();
        }
        self::$_paths["library"][] = $path; 
    }

    /**
     * Add an application path
     * 
     * @param string $path Application path   
     * @access public
     */
                 
    public static function add_application_path($path) 
    {
        if (!isset(self::$_paths["app"])) {
            self::$_paths["app"] = array();
        }
        self::$_paths["app"][] = $path; 
    }

  
    /**
     * Get a path list
     * 
     * @param string $type Path type   
     * @return array Path list  
     * @access public
     */
   
    public static function getPaths($type) 
    {
        if (!isset(self::$_paths[$type])) {
            return array();
        }
        return self::$_paths[$type];
    }
 
    /**
     * Get current theme
     * 
     * @return string Theme Name
     * @access public
     */           
 
    public static function getTheme() 
    {
  
        if (!isset(self::$theme)) {
            self::$theme_type = "site";
            self::$theme = VThemeConfig::getDefaultTheme(self::$theme_type);            
        }
        return self::$theme;
    }

    /**
     * Get Theme Type
     * 
     * @return string Theme Type
     * @access public    
     */   

    public static function getThemeType() 
    {
        if (!isset(self::$theme)) {            
            self::$theme_type = "site";
            self::$theme = VThemeConfig::getDefaultTheme(self::$theme_type);
        } 
        return self::$theme_type;
    }
 
    /**
     * Set current theme
     * 
     * @param string $type Theme type
     * @param string $theme Theme name
     * @return string Old theme name
     * @access public   
     */
                 
    public static function setTheme($type,$theme = null) 
    {
    	
    	if (isset(self::$theme)) {          
            $oldTheme = self::$theme;        
    	} else {
    		$oldTheme = null;
    	}
    	
    	self::$theme_type = $type;
        
        if (empty($theme)) {
            $theme = VThemeConfig::getDefaultTheme(self::$theme_type);
        }
  
        self::$theme = $theme;
           
        return $oldTheme;
    }

    /**
     * Get current language
     * 
     * @return object Language   
     * @access public
     */
             
    public static function &getLanguage() 
    {
        static $languages;
   
        if (!isset($languages)) {
            $languages = array();
        }
   
        if (!isset(self::$language)) {
            $cfg = new VConfig;
            self::$language = $cfg->default_language;
            if (self::$language === null) {
                self::$language = "en";
            }
        }
   
        if (!isset($languages[self::$language])) {   
            require_once(VPATH_BASE.DS.'languages'.DS.self::$language.'.php');
            $className = self::$language . "Language";
            $languages[self::$language] = new $className();    
        }
   
        return $languages[self::$language];
    }

    /**
     * Throw an error or warning
     * 
     * @param object $error Error or warning
     * @return object Error or warning
     */
                 
    public static function &ethrow(&$error) 
    {
        if (self::isError($error)) {
            $error->ethrow();
        } else {
            if (self::isWarning($error)) {
                $error->ethrow();
            }
        }
        return $error;
    }
 
    /**
     * Generate a system error
     * 
     * @param string $msg Error message
     * @param string $system Error system
     * @param integer $errno Error number
     * @param boolean $throw Throw error
     * @return object Error  
     * @access public
     */
                   
    static function raiseError($msg,$system = null,$errno = null,$throw = true) 
    {
        if (empty($msg)) {
            $msg = 'Undefined error';
        }
        return new VError($msg,$system,$errno,$throw);           
    }

    /**
     * Generate a system warning
     * 
     * @param string $msg Error message
     * @param string $system Error system
     * @param integer $errno Error number
     * @param boolean $throw Throw error
     * @return object Error  
     * @access public
     */
  
    static function raiseWarning($msg,$system = null,$errno = null,$throw = true) 
    {
        if (empty($msg)) {
            $msg = 'Undefined warning';
        }
        return new VWarning($msg,$system,$errno,$throw);    
    }
 
    /**
     * Test if value is an error
     * 
     * @param mixed $ob Test subject
     * @return boolean True if test subject is an error
     * @access public  
     */
            
    public static function isError($ob) 
    {
        return VError::isError($ob);
    }

    /**
     * Test if value is a warning
     * 
     * @param mixed $ob Test subject
     * @return boolean True if test subject is a warning or error
     * @access public  
     */
  
    public static function isWarning($ob) 
    {
        return VWarning::isWarning($ob);
    }
 
    /**
     * System Abort
     * 
     * @param string Abort message
     * @access public
     */       
    
    public static function vexit($message = 'Halt!') 
    {
        die($message);
    }
 
    /**
     * Require a PHP Extension
     * 
     * @param string $ext PHP Extension ID
     * @param boolean $throw Throw warning on railure
     * @return boolean|object True on success, error or warning otherwise
     * @access public
     */
    
    public static function RequireExtension($ext,$throw = true) 
    {
    	if (($ext == 'mssql') && (version_compare(PHP_VERSION, '5.3.0') >= 0)) {    		
    		$ext = 'sqlsrv';    		
    	}
    	
        if (!extension_loaded($ext)) {
        	if (function_exists('dl')) {        	
                self::noWarn();   
                if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {            	            	
                    $r = @dl('php_' . $ext . '.dll');                     
                } else {            	
                    $r = @dl($ext.'.so');
                }
                self::noWarn(false);    
                if (!$r) {
                    return VWP::raiseWarning("Extension $ext failed to load",'VWP',null,$throw);
                }
        	} else {
        		return VWP::raiseWarning("dl() not supported! Unable to load extension $ext",'VWP',null,$throw);
        	}
        }
        return true;
    }
 
    /**
     * Require a system library
     * 
     * Note: Library names are in the form of libname.[subs].interfacename
     *    
     * @param string $lib Library name
     * @return true|object True on success or error on failure
     * @access public  
     */
          
    public static function RequireLibrary($lib) 
    {
  
        if (isset(self::$_lib_cache[$lib])) {
            return true;
        }
  
        $filename = str_replace(".",DS,$lib) . '.php';
   
        if (isset(self::$_paths["library"])) {
            $paths = self::$_paths["library"];
        } else {
            $paths = array();
        }
   
        if (defined('VPATH_LIB')) {
            array_push($paths,VPATH_LIB);
        }
   
        foreach($paths as $libpath) {   
            if (file_exists($libpath.DS.$filename)) {     
                require_once($libpath.DS.$filename);
                self::$_lib_cache[$lib] = true;     
                return true;
            }
        }
             
        return self::raiseError("Unable to locate $lib Library!","VWP");
    }

    /**
     * Test if library exists
     * 
     * Note: Library names are in the form of libname.[subs].interfacename
     *    
     * @param string $lib Library name
     * @return boolean True if library found
     * @access public  
     */  
  
    public static function libraryExists($lib) {
        $parts = explode('.',$lib);
   
        $filename = implode(DS,$parts).'.php';
   
        $paths = array();
        if (isset(self::$_paths["library"])) {
            $paths = self::$_paths["library"];
        }
   
        if (defined('VPATH_LIB')) {
            array_push($paths,VPATH_LIB);
        }
   
        foreach($paths as $libpath) {
            if (file_exists($libpath.DS.$filename)) {     
                return true;
            }
        }
             
        return false;          
    }
 
    /**
     * Get current document
     * 
     * @return VDocument Current Document
     * @access public  
     */
           
    public static function &getDocument() 
    {        
        return self::o()->_document[0];
    } 
 
    /**
     * Current document Vector
     * 
     * @return VDocument Current Document
     * @access public  
     */
           
    public static function &document() 
    {        
        return self::o()->_document[0];
    }     
    
    /**
     * Shell Vector
     * 
     * Usage: v()->shell()->
     * 
     * @return VShell Current Shell     
     * @access public
     */
                        
    public function &shell() {
        $user =& VUser::getCurrent();
        $shell =& $user->getShell();
        return $shell;
    }

    
    /**
     * Database Vector
     * 
     * Usage: v()->dbi()->
     * 
     * @return VDBI Database Interface     
     * @access public
     */

    function &dbi() 
    {
    	if (!class_exists('VDBI')) {
    		self::RequireLibrary('vwp.dbi.dbi');
    	}    	
    	$dbi =& VDBI::getInstance();
    	return $dbi;
    }
    
    /**
     * Filesystem
     * 
     * @param object $driver
     * @return VFilesystem Filesystem
     * @access public
     */
    
    function &filesystem($driver = null,$test = false) 
    {
    	static $local;
    	
        if (empty($driver)) {
        	   if (!isset($local)) {                      		
        	       $local =& VFilesystem::local($test);	
        	   }
            return $local;	
        }
        return $driver;
    }
    
    /**
     * Get Current Shell
     * 
     * @return VShell Current Shell Object
     * @access public          
     */
              
    public static function &getShell() {
        $user =& VUser::getCurrent();
        $shell =& $user->getShell();        
        return $shell;    
    }
        
    /**
     * Access Mainframe object
     * 
     * @return VWP Mainframe object
     * @access public               
     */
              
    public static function &o() 
    {
                        
          if (!isset(self::$_vwp_inst)) {
              self::$_vwp_inst = array();
              self::$_vwp_inst[0] = new VWP; 
          }
          return self::$_vwp_inst[0];
    }
 
    /**
     * Get instance of VWP object
     * 
     * @return VWP Instance of VWP object
     * @access public      
     */
       
    public static function &getInstance() 
    { 
          if (!isset(self::$_vwp_inst)) {
              self::$_vwp_inst = array();
              self::$_vwp_inst[0] = new VWP; 
          }
          return self::$_vwp_inst[0];
    }

 
    /**
     * Execute an application
     *      
     * This execute function places the output of the buffer
     * into the current documents buffer and returns the
     * value returned by the application.     
     *           
     * @param string $app Application ID
     * @param array $envp System environment
     * @return mixed Result
     * @access public
     */         
 
    function execute($command,&$envp) 
    {
        
        if (isset($envp['any']["alias"])) {
            $scfg = array("alias"=>$envp['any']["alias"]);
        } else {
            $argv = explode(' ',$command);
                
            $appId = $argv[0];
      
            $mods = explode('.',$appId);
  
            $app = array_shift($mods);  
  
            $widgetId = implode('.',$mods);
            
            $scfg = array(
              "app"=>$appId            
            );        
            if (!empty($widgetId)) {
                $scfg['widget'] = $widgetId;
            }
        
        }
  
        $doc =& self::getDocument();
        
        $screen = $doc->createScreenBuffer($scfg); 
       
        $stdio = new VStdio;
        $stdio->setOutBuffer($doc,$screen);
        
        $user =& VUser::getCurrent();
        $shell =& $user->getShell();
        if (is_object($shell)) {                
            $result = $shell->execute($command,$envp,$stdio);
        } else {
            $result = self::raiseWarning('NO SHELL!','VWP',null,false);
        }  
        
        return $result;     
    }
 
    /**
     * Dispatch user request
     * 
     * Note: This should be called by the script entry point, normally index.php
     * 
     * @access public    
     */
       
    function dispatch() 
    {

    	if (version_compare(PHP_VERSION, '5.2.4', '>=')) {
    	    ini_set('display_errors', 1);
    	}
    	
    	$fs =& $this->filesystem();
    	  
        // Load session
        
        $sess =& VSession::getInstance();
                          
        self::$_error_messages = $sess->get('error_messages',array(),'messages');
        self::$_warnings = $sess->get('warnings',array(),'messages');               
        self::$_notices = $sess->get('notices',array(),'messages');     
        self::$_debug = $sess->get('debug',array(),'messages');
                                    	
        // Load Required Libraries
        
        VWP::RequireLibrary('vwp.user');
        VWP::RequireLibrary('vwp.documents.document');
        VWP::RequireLibrary('vwp.stdio');
        VWP::RequireLibrary('vwp.server.request');
        VWP::RequireLibrary('vwp.sys.notify');
        VWP::RequireLibrary('vwp.sys.hibernate');                
                
        // Wake System
        
        $sleep_key = VHibernateDriver::wake();

        VEnv::reroute();
        
        // Prepair output driver (document)
        
        $cfg = self::getConfig();
        
        $format = VEnv::getWord("format",isset($cfg->default_document_type) && !empty($cfg->default_document_type) ? $cfg->default_document_type : "html");
          
        if (self::isWarning(VDocument::RequireDocumentType($format))) {
        	
        	VWP::raiseWarning("Missing document type '$format'. Attempting to use default document type!",__CLASS__);
        	
            $format = $cfg->default_document_type;
            if (self::isWarning(VDocument::RequireDocumentType($format))) {
                VWP::vexit("Critical error! $format Document type not found and default document type is missing!");
            }  
        }

        $documentClassName = strtoupper($format).'Document';
     
        $this->_document = array(new $documentClassName());         
       
        
        // Load User Session
        
        $user =& VUser::getCurrent();
  
        VNotify::Notify('init','VWP');
        
        $result = $user->logon();
        
        if (self::isWarning($result)) {
            $result->ethrow();
        }

        VNotify::Notify('before_render','VWP');
        
                  
        VWP::getDocument()->render();
        
        VNotify::Notify('after_render','VWP');


        VNotify::Notify('shutdown','VWP');
                    
        VHibernateDriver::sleep($sleep_key);             
    }

    /**
     * Test if is an associative array
     * 
     * @param mixed $array
     * @return boolean True if data is an associative array
     * @access public
     */
    
    public static function is_associative($array) {
        if ( is_array($array) && ! empty($array) ) {
            for ( $i = count($array) - 1; $i; $i-- ) {
                if ( ! array_key_exists($i, $array) ) { 
            	    return true; 
                }
            }
            return ! array_key_exists(0, $array);
        }
        return false;
    }    
    
    /**
     * System error handler
     * 
     * @param integer $errno Error number
     * @param string $errstr Error message
     * @param string $errfile Error filename
     * @param integer $errline Line error occurred
     * @return boolean True to supress default system error handling
     * @access public
     */
                   
    public static function dispatchError($errno, $errstr, $errfile = null, $errline = null) 
    {
        self::$_last_error = func_get_args();
        $msg = $errstr;
    
        if ($errfile !== null) {
            $msg .= " : in $errfile";
        }
        
        if ($errline !== null) {
            $msg .= " on line $errline";
        }
     
        switch ($errno) {
  
            case E_WARNING:
                if (!self::$_nowarn) {
                    array_push(self::$_warnings,$msg);
                }
                break;
    
            case E_NOTICE:     
                array_push(self::$_notices,$msg);
                break;
      
            case E_USER_ERROR:
                array_push(self::$_error_messages,$msg);
                break;
    
            case E_RECOVERABLE_ERROR:
                array_push(self::$_error_messages,$msg);
                break;
    
            case E_USER_WARNING:
                if (self::$_nowarn) {
                    self::$_nowarn = false;
                } else {    
                    array_push(self::$_warnings,$msg);
                }
                break;

            case E_USER_NOTICE:
                array_push(self::$_notices,$msg);
                break;
    
            case E_STRICT:
                if (self::$_strict) {
                    array_push(self::$_debug,$msg);
                }
                break;
    
            default:
                array_push(self::$_error_messages,"Unknown error type :" . $msg);
                break;
        }

        if (defined('VWP_ENABLE_DEBUG_BACKTRACE') && 
            VWP_ENABLE_DEBUG_BACKTRACE &&
            (function_exists('debug_backtrace')) &&
            (!self::$_nowarn)) {
         
         
        	$backtrace = debug_backtrace(false);
        	$dmsg = $msg . "\nbacktrace:\n";
        	for($i=0;$i<count($backtrace);$i++) {
        	   $keys = array_keys($backtrace[$i]);
               $dmsg .= '*';
               $dmsg .= in_array('file',$keys) ? $backtrace[$i]["file"] : '';
               $dmsg .= '#';               
               $dmsg .= in_array('line',$keys) ? $backtrace[$i]["line"] : ''; 
               $dmsg .= '->'; 
               $dmsg .= in_array('function',$keys) ? $backtrace[$i]["function"] : '';
               $dmsg .= "\n";                                            		
        	}
        	if (defined('VWP_ENABLE_ECHO_DEBUG_BACKTRACE') && VWP_ENABLE_ECHO_DEBUG_BACKTRACE) {
        		print_r($dmsg);
        	}
        	if (defined('VWP_ENABLE_STRICT_DIE') && VWP_ENABLE_STRICT_DIE) {        		
        		die('Strict mode enforced!');
        	}
        	array_push(self::$_debug,$dmsg);
        	        	
        }
                
        
        /* Don't execute PHP internal error handler */
        return true;
    }

    /**
     * DOM Implementation Registry
     * 
     * @return VDOMImplementationRegistry DOM Implementation Registry
     * @access public
     */
    
    public function &dom() 
    {
  	    if (!class_exists('VDOMImplementationRegistry')) 
   	    {
   	    	self::RequireLibrary('vwp.dom.domimplementationregistry');
   	    	
   	    }
    	$_dom_implementation_registry =& VDOMImplementationRegistry::getInstance();    	
    	return $_dom_implementation_registry;
    }

    /**
     * Current Session 
     * 
     * @access public
     */
    
    public function &session() 
    {
    	$sess =& VSession::getInstance();
    	return $sess;
    }
    
    /**
     * Class Constructor
     * 
     * @access public
     */
         
    function __construct() 
    {

        // Setup Kernel   
        self::resetTimeLimit();
        parent::__construct();
        self::RequireLibrary('vwp.sys.kernel');
        $this->_kernel =& VKernel::o();
        set_error_handler(  array("VWP","dispatchError"),E_ALL | E_STRICT);
        
        // Load Libraries  
        self::RequireLibrary('vwp.error');
        self::RequireLibrary('vwp.filesystem');
        self::RequireLibrary('vwp.environment');        
        self::RequireLibrary('vwp.application');        
        self::RequireLibrary('vwp.language.language');
        self::RequireLibrary('vwp.session');
        self::RequireLibrary('vwp.themes.config');
        self::RequireLibrary('vwp.ui.ref');
                     
    }

    /**
     * Get Version
     *
     * @return string Version
     * @access public
     */
    
    function version() 
    {
    	return "1.0.1";
    }
    
    /**
     * Get Version Status
     *
     * @return string Version
     * @access public
     */
    
    function version_status() 
    {
    	return "beta developer release";
    }    
    
    /**
     * Class Destructor
     * 
     * @access public
     */ 
 
    function __destruct() 
    {   
        parent::__destruct();
    }
    
    // end VWP class
}  

/**
 * Platform Vector
 * 
 * Usage: v()->
 * 
 * @return VWP Core platform
 * @access public
 */

function &v() 
{
    $vwp =& VWP::getInstance();
    return $vwp;
}
