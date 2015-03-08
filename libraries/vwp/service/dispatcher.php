<?php

/**
 * Virtual Web Platform - Service Dispatcher
 *  
 * This file provides the service Dispatcher
 *        
 * @package VWP
 * @subpackage Libraries.Service  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch 2011 - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

/**
 * Virtual Web Platform - Service Dispatcher
 *  
 * This class provides the service Dispatcher
 *        
 * @package VWP
 * @subpackage Libraries.Service  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch 2011 - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */


class VServiceDispatcher 
{
	/**
	 * Dispatch
	 * 
	 * @param object $resource Resource
	 * @param mixed $callback Callback
	 * @return VServiceResource Resource
	 */
	
	function &dispatch($resource,$callback) 
	{
		if (isset($resource->handler)) {
		    $resource->handler->setCallback($callback);
		}
		return $resource;
	}
	
	/**
	 * Get Instance
	 * 
	 * @return VServiceDispatcher Service Dispatcher
	 * @access public
	 */
	
	public static function &getInstance() 
	{
	    static $serviceDispatcher;
	    if (!isset($serviceDispatcher)) {
	    	$serviceDispatcher = new VServiceDispatcher;
	    }
	    return $serviceDispatcher;	
	}
	
	// end class VServiceDispatcher
}

