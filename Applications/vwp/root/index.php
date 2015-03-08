<?php


/**
 * Virtual Web Platform
 *  
 * VWP Server Entry Point
 * 
 * @package VWP
 * @subpackage Base  
 * @author Ralph Ritoch 
 * @copyright Ralph Ritoch <rritoch@gmail.com>
 * @link http://www.vnetpublishing.com    
 */

date_default_timezone_set('UTC');

if (!version_compare(PHP_VERSION, '5.0.0', '>=')) {
 trigger_error("System requires PHP Version >= 5.0.0" ,E_USER_ERROR);
 die();
}

/**
 * Base Path
 */

   
define('VPATH_BASE',dirname(__FILE__));



/**
 * Require Data Type Support
 * 
 * Note: Aka. MIT Value Objects  
 */ 

require_once('vwp_types.php');

/**
 * Require Configuration Settings
 */
  
require_once('vwp_config.php');

$cfg = new VConfig;


if (!defined('DIRECTORY_SEPARATOR')) {

/**
 * Directory Separator
 */
  
 define('DIRECTORY_SEPARATOR',$cfg->path_separator);
}




if (isset($cfg->shared_path)) {

/**
 * Shared Path
 */
  
 define('VPATH_SHARED',$cfg->shared_path);
}

// Set default timezone

date_default_timezone_set($cfg->timezone);

unset($cfg);

/**
 * Directory Separator
 */
  
define('DS',DIRECTORY_SEPARATOR);


/**
 * Library base path
 */
  
define('VPATH_LIB',VPATH_BASE.DS.'libraries');

/**
 * Site Base Path
 */
  
define('VPATH_SITE',VPATH_BASE);




// Launch

if (file_exists(VPATH_LIB.DS.'vwp'.DS.'vwp.php')) {
/**
 * Require Platform from Libraries
 */ 

 require_once(VPATH_LIB.DS.'vwp'.DS.'vwp.php');
} else if (file_exists(VPATH_SHARED.DS.'vwp'.DS.'libraries'.DS.'vwp'.DS.'vwp.php')) {

/**
 * Require Platform from Shared Path
 */

 require_once(VPATH_SHARED.DS.'vwp'.DS.'libraries'.DS.'vwp'.DS.'vwp.php');
 VWP::add_library_path(VPATH_SHARED.DS.'vwp'.DS.'libraries');
} else {
 die("Virtual Web Platform Not found!");
}


global $mainframe;

$mainframe =& v();

v()->dispatch();

