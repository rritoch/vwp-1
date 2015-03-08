<?php

/**
 * Virtual Web Platform - Filesystem Support
 *  
 * @package VWP
 * @subpackage Libraries.Filesystem  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License   
 */

// Restrict access

class_exists('VWP') || die();

/** 
 * boolean True if a Windows based host 
 */

define('VPATH_ISWIN', (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'));

/** 
 * boolean True if a Mac based host 
 */

define('VPATH_ISMAC', (strtoupper(substr(PHP_OS, 0, 3)) === 'MAC'));

if (!defined('DS')) {
	
	/** 
	 * string Shortcut for the DIRECTORY_SEPARATOR define 
	 */
	
	define('DS', DIRECTORY_SEPARATOR);
}


/**
 * Require FilesystemDriver Support
 */

VWP::RequireLibrary('vwp.filesystem.driver');

/**
 * Virtual Web Platform - Filesystem Support
 *  
 * @package VWP
 * @subpackage Libraries.Filesystem  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License   
 */


class VFilesystem extends VObject
{

	
	/**	 
	 * File Driver
	 * 
	 * @var VFile $_vfile
	 * @access protected
	 */
	
	protected $_vfile;

	/**	 
	 * Folder driver
	 * 
	 * @var VFolder $_vfolder
	 * @access protected
	 */	
	
	protected $_vfolder;
	
	/**	 
	 * Path driver
	 * 
	 * @var VPath $_vpath
	 * @access protected
	 */
	
	protected $_vpath;
	
	/**
	 * Client Driver
	 *  	 
	 * @var object $_client;
	 * @access protected
	 */
	
	protected $_client;
	
	/**	 
	 * Filesystem Drivers
	 * 
	 * @var array Filesystem Drivers
	 * @access public
	 */
	
	static $_drivers = array();
		
	/**
	 * Get instance of filesystem driver
	 * 
	 * @param object $client Client Driver
	 * @param string $type Filesystem type
	 */
	
	public static function &getInstance(&$client,$type = 'local') 
	{
		
		// Load Filesystem Type
				
        $type = (string)$type;        
		if (empty($type)) {
		    $type = 'local';
		}
		
        $prefix = ($type == 'local') ?  'V' : 'V' . ucfirst($type);
				
		$parts = array('file','folder','path');
		
		foreach($parts as $p) {
			$vname = '_v'.$p;
			$className = $prefix . ucfirst($p);
			if (!class_exists($className)) {
				VWP::RequireLibrary('vwp.filesystem.'.$type.'.'.$p);
			}
			if (!class_exists($className)) {
				$err = VWP::raiseWarning("Missing $className",get_class($this),null,false);
				return $err;
			}					
		}

		// Get Driver
		
		$idx = count(self::$_drivers);
		self::$_drivers[$idx] = new VFilesystem($client,$type);
        
		// Return Driver
		
		return self::$_drivers[$idx];
	}

	/**
	 * File Driver
	 * 
	 * @return VFile File driver
	 * @access public
	 */
	
	function &file() {
		return $this->_vfile;
	}

	/**
	 * Folder Driver
	 * 
	 * @return VFolder Folder driver
	 * @access public
	 */
	
	function &folder() {
		return $this->_vfolder;
	}	
	
	/**
	 * Path Driver
	 * 
	 * @return VPath Path driver
	 * @access public
	 */
	
	function &path() {
		return $this->_vpath;
	}	
	
	/**
	 * Client Driver
	 * 
	 * @return object Client driver
	 * @access public
	 */
	
	function &client() {
		return $this->_client;
	}
	
	/**
	 * Local filesystem
	 * 
	 * @access public
	 */
	
    public static function &local() {
    	global $drivers;
    	    	
    	if (!isset($drivers)) {
    		$drivers = array();
    	}
    	
    	if (!isset($drivers['local_fs'])) {   			
   		    $client = new stdClass();
   		    $drivers['local_fs'] =& self::getInstance($client,'local');   		    
   		}
    	return $drivers['local_fs'];
    }
	
	/**	 
	 * Class Constructor
	 * 
	 * @param object $client Client Driver
	 * @param string $type Filesystem type
	 * @access public
	 */
	
	function __construct(&$client,$type = 'local') 
	{
		$this->_client =& $client;

	    $type = (string)$type;        
		if (empty($type)) {
		    $type = 'local';
		}
				
        $prefix = ($type == 'local') ?  'V' : 'V' . ucfirst($type);
        		
		$parts = array('file','folder','path');
		
		foreach($parts as $p) {
			$vname = '_v'.$p;
			$className = $prefix . ucfirst($p);
			if (class_exists($className)) {
			    $this->$vname = new $className($this);
			} else {
				$this->$vname = VWP::raiseWarning("Missing $className!",get_class($this),null,false);
			}
		}		
	}
	
	// end class VFilesystem
}



if (!defined('VPATH_ROOT')) 
{
	/** 
	 * string The root directory of the file system in native format 
	 */
	
	define('VPATH_ROOT', VFilesystem::local()->path()->clean(VPATH_SITE));
}
