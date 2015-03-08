<?php

/**
 * Virtual Web Platform - Service Handler
 *  
 * This file provides the Service Handler 
 *        
 * @package VWP
 * @subpackage Libraries.Service  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch 2011 - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

/**
 * Virtual Web Platform - Service Handler
 *  
 * This class provides the Service Handler Base Class 
 *        
 * @package VWP
 * @subpackage Libraries.Service  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch 2011 - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

abstract class VServiceHandler extends VObject 
{
	
	/**
	 * Get Handler
	 * 
	 * Returns current object if this driver can 
	 * handle the provided port
	 * 
	 * @param VServiceResource $resource
	 * @return VServiceHandler Service handler
	 * @access public
	 */	
	
	abstract function &getHandler($portNode);
	
	/**
	 * Call Resource Method
	 * 
	 * @param VServiceResource $resource
	 * @param array $args Arguments
	 * @access public
	 */
	
	abstract function callMethod($resource,$method,$args);
	
	/**
	 * Set Callback
	 * 
	 * @param mixed $callback Callback
	 * @access public
	 */
	
	abstract function setCallback($callback);

	// end class VServiceHandler
}
