<?php

/**
 * Virtual Web Platform - Document support
 *  
 * This file provides the default API for
 * Documents.   
 * 
 * @package VWP
 * @subpackage Libraries.Documents  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License   
 */

// Restrict access
class_exists('VWP') || die();

// Require registry support
class_exists('Registry') || VWP::RequireLibrary('vwp.sys.registry');

/**
 * Virtual Web Platform - Document support
 *  
 * This class provides the default API for
 * Documents. This is the base class for all document types.   
 * 
 * <pre>
 *  
 *  Registry keys can be used to add support
 *  for new document types.
 *  
 *  Client Key: DOCUMENT\Types\[doctype] 
 *  
 *  The registry key should have a value named 'location'
 *   which is the file which will be loaded
 *   when the document type is requested.
 *  
 *  Required Headers:
 *   
 *    VWP::RequireLibrary('vwp.documents.document');
 *       
 *  Usage: 
 *        
 *    VDocument::RequireDocumentType(string $doctype);
 *                    
 * </pre>
 *   
 * @package VWP
 * @subpackage Libraries.Documents  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

class VDocument extends VObject 
{

	/**
	 * Meta Data
	 * 
	 * @var array $_meta Meta data
	 */
	
	public $_meta = array();
	
    /**
     * Default escape function
     *
     * @var string
     * @access private
     */
  
    public $_escape = 'htmlentities';

    /**
     * @var array $_aliases Alias buffer
     * @access private
     */
       
    public $_aliases = array();

    /**
     * @var array $_widgets Widgets buffer
     * @access private
     */

    public $_widgets = array();

    /**
     * @var array $_attribs Attributes buffer
     * @access private
     */
   
    public $_attribs = array();

    /**
     * @var array $errors Errors
     * @access public
     */
   
    public $errors = array();
 
    /**
     * @var array $notices Notices
     * @access public
     */
   
    public $notices = array();

    /**
     * @var array $warnings Warnings
     * @access public
     */
 
    public $warnings = array();

    /**
     * @var array $debug_notices Debug notices
     * @access public
     */
 
    public $debug_notices = array();

    /**
     * Scripts
     * 
     * @var array $scripts Registered Scripts
     * @access public
     */    
    
    public $scripts = array();
    
    /**
     * @var boolean $_require_secure_line Require Secure Connection
     * @access private
     */
 
    protected $_require_secure_line = false;
 
    /**  
     * @var array $_doctype_cache Document type cache
     * @access private
     */
     
    static $_doctype_cache = array();

    /**     
     * Screen buffer
     * 
     * @var array Screens
     * @access public
     */
    
    public $screens = array(); 

    /**
     * Set Meta Data
     */
    
    function setMetaData($name,$value) 
    {
    	$this->_meta[(string)$name] = $value; 
    }
    
    /**
     * Register Script
     * 
     * @param string $uri Script URI
     * @param string $type Script type
     * @access public
     */
    
    function registerScript($uri,$type="text/javascript") 
    {
        $this->scripts[$uri] = $type;	
    }
    
    /**
     * Unregister Script
     * 
     * @param string $uri Script URI
     * @access public
     */

    function unregisterScript($uri) 
    {
        unset($this->scripts[$uri]);	
    }
        
    /**
     * Get Document Type
     */
    
    function getDocumentType() {
       
       $dtype = strtolower(get_class($this));
       
       if (substr($dtype,strlen($dtype) - 8) == 'document') {
       	    $dtype = substr($dtype,0,strlen($dtype) - 8);
       }
       
       return $dtype;
    }
    
    /**
     * Require secure line
     * 
     * Note: Use null for require field to return 
     *       value without changing the current value
     * @param boolean $require Require secure line
     * @return boolean Current value of secure line requirement
     */
    
    function requireSecureLine($require = null) 
    {
        if ($require !== null) {
            $this->_require_secure_line = $require ? true : false;
        }
        return $this->_require_secure_line ? true : false;
    }
    
    /**     
     * Test if current connection is secure
     * 
     * @return boolean True if is a secure line
     * @access public
     */
    
    function isSecureLine() 
    {
        return false;
    }

    /**
     * Switch to a secure line     
     * 
     * @access public
     */
    
    function divertUnsecureLine() 
    {
         // Abstract Method
    }
    
    /**     
     * Create a screen buffer
     * 
     * @param array $cfg Configuration settings
     * @access public
     */
    
    public function createScreenBuffer($cfg) 
    {
        $screenInfo = array();
     
        foreach($cfg as $key=>$val) {
            $info = array(urlencode($key),urlencode($val));
            $info = urlencode(implode(':',$info));
            array_push($screenInfo,$info);
        }
     
        $screenId = implode(':',$screenInfo);
        if (!isset($this->screens[$screenId])) {
            $this->screens[$screenId] = '';
        }
        return $screenId; 
    }
 
    /**     
     * Append screen buffer
     * 
     * @param string $screenId Screen ID
     * @param string $data Data
     * @access public
     */
    
    public function appendBuffer($screenId,$data) 
    { 
        $this->screens[$screenId] .= $data;     
    }
    
    /**
     * Register Document Type
     * 
     * @param string $type
     * @param string $location Filename
     * @access public
     */
    
    public static function registerDocumentType($doctype,$location)
    {
    	$regkey = "DOCUMENT\\Types\\" . $doctype;
        
    	$localMachine =& Registry::LocalMachine();  
  
        $result = Registry::RegCreateKeyEx($localMachine,
                              $regkey,
                              0,
                              '',
                              0,
                              0,
                              0,
                              $registryKey,
                              $result); 
                              
        if (!VWP::isWarning($result)) {
            $result = true;
            
            $key = 'location';
            $val = $location;
                        
            $sresult= Registry::RegSetValueEx($registryKey,
                           $key,
                           0, // reserved 
                           REG_SZ, // string
                           $val,
                           strlen($val)); 
            if (VWP::isWarning($sresult)) {
                    $result = $sresult;                            
            }  
               
            Registry::RegCloseKey($registryKey);
            Registry::RegCloseKey($localMachine);

            return $result;
        }
  
        Registry::RegCloseKey($localMachine);
        return $result;     	
    }
    
    /**
     * Require a document type
     *
     * @static
     * @param string $doctype Document type
     * @return true|object True on success or error on failure
     * @access public  
     */ 
  
    public static function RequireDocumentType($doctype) 
    {
        $doctype = strtolower($doctype);
        if (empty($doctype) || $doctype == 'document') {
            return VWP::raiseWarning('Invalid document type requested!','VDocument',null,false);
        }

        if (!isset(self::$_doctype_cache[$doctype])) 
        {
  
            // Search Registry
   
            $localMachine = & Registry::LocalMachine();
            $key = "DOCUMENT\\Types\\" . $doctype;
   
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
                        self::$_doctype_cache[$doctype] = true;
                    }
                }  
            }  
  
            Registry::RegCloseKey($localMachine);
   
            // Search Core
   
            $filename = strtolower($doctype) . '.php';   
            $paths = VWP::getPaths('library');
   
            if (defined('VPATH_LIB')) {
                array_push($paths,VPATH_LIB);
            }
   
            foreach($paths as $libpath) {
                if (!isset(self::$_doctype_cache[$doctype])) {   
                    if (file_exists($libpath.DS.'vwp'.DS.'documents'.DS.$filename)) {     
                        require_once($libpath.DS.'vwp'.DS.'documents'.DS.$filename);
                        self::$_doctype_cache[$doctype] = true;           
                    }
                }
            }
   
            if (!isset(self::$_doctype_cache[$doctype])) {             
                self::$_doctype_cache[$doctype] = VWP::raiseError("Unable to locate $doctype document type!","NET",null,false);
            }
        }
        return self::$_doctype_cache[$doctype];
    }
 
    /**
     * Get data from buffer
     * 
     * @param string|false $screenId Screen ID
     * @return string Data
     * @access public  
     */               
 
    function &getBuffer($screenId) 
    {  
        return $this->screens[$screenId];  
    }

    /**
     * Display document
     */
     
    function render() 
    {
        // nothing to do!
    }
 
    /**
     * Register default settings
     */
     
    function registerDefaults() 
    {
        $this->errors = VWP::getErrorMessages();
        $this->warnings = VWP::getWarnings();
        $this->notices = VWP::getNotices();
        $this->debug_notices = VWP::getDebugNotices(); 
    }
 
    /**
     * Class constructor
     * 
     * @access public
     */
         
    function __construct() 
    {
        parent::__construct();
    }
 
    // end class VDocument
} 
