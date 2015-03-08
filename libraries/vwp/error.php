<?php

/**
 * Virtual Web Platform - Error Handling
 *  
 * This file provides Error Handling support   
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
 * Require Warning Support
 */
  
VWP::RequireLibrary('vwp.warning');

/**
 * Error Code - No error
 */    
  
define('ERROR_SUCCESS',0);

/**
 * Error Code - More data available
 */
     
define('ERROR_MORE_DATA',2);

/**
 * Error Code - Request failed
 */

define('ERROR_FAILED',-1);

/**
 * Error Code - Unsupported feature
 */
       
define('ERROR_UNSUPPORTED',-2);

/**
 * Error Code - No more items available
 */
  
define('ERROR_NO_MORE_ITEMS',-3);


/**
 * Error Code - File not found
 */
  
define('ERROR_FILENOTFOUND',-4);

/**
 * Error Code - Class not found
 */
  
define('ERROR_CLASSNOTFOUND',-5);



/**
 * Virtual Web Platform - Errors
 *  
 * This class provides Errors for the Error Handling system   
 * 
 * @package VWP
 * @subpackage Libraries  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License   
 */
  
class VError extends VWarning 
{

    /**
     * Generate an Error
     * 
     * @param string $msg Error Message
     * @param string $system System Name
     * @param integer $errno Error code
     * @param boolean $throw Throw error
     * @return VError Error object
     * @access public   
     */
                          
    function raiseError($msg,$system = null,$errno = null,$throw = true) 
    {   
        return new VError($msg,$system,$errno,$throw);  
    }
 
    // end class VError   
}
