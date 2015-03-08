<?php

/**
 * VWP Registry Library
 * 
 * This file provides the CLASSES_ROOT registry key.
 *  
 * @package VWP
 * @subpackage Libraries.System
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License
 */

/**
 * VWP Registry Library
 * 
 * This is the class for a CLASSES_ROOT registry key.
 *  
 * @package VWP
 * @subpackage Libraries.System
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License
 */
 
 class HKEY_CLASSES_ROOT extends HKEY 
 {
 

 	/**
 	 * Class Constructor
 	 * 
 	 * @access public
 	 */
 	
    function __construct() 
    {
     	$this->rootKey =& Registry::LocalMachine();

     	$lpSubKey = "\\Software\\Classes";     	
     	$lpClass = '';
     	$dwOptions = 0;
     	$samDesired = 0;
     	$lpSecurityAttributes = 0;
     	
     	$result = Registry::RegCreateKeyEx(
                        $this->rootKey,
                        $lpSubKey,
                         0,
                         $lpClass,
                         $dwOptions,
                         $samDesired,
                         $lpSecurityAttributes,
                         $rootKey, 
                         $result);
        if ($result === ERROR_SUCCESS) {
        	$this->rootNode = $rootKey->rootNode;
        }                          
    }
    
    // end class HKEY_CLASSES_ROOT
}

