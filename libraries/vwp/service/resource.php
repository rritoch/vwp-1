<?php
/**
 * Virtual Web Platform - Service Resource
 *  
 * This class provides the Service Resource Object 
 *        
 * @package VWP
 * @subpackage Libraries.Service  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch 2011 - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

/**
 * Virtual Web Platform - Service Resource
 *  
 * This class provides the Service Resource Object 
 *        
 * @package VWP
 * @subpackage Libraries.Service  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch 2011 - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

class VServiceResource 
{
	/**	 
	 * Service
	 *  
	 * @var VWSDL Wsdl Service
	 * @access public
	 */
		
	public $service;

	/**
	 * Handler
	 * 
	 * @var VServiceHandler Handler
	 * @access public
	 */
	
	public $handler;
	
	/**
	 * Service Name
	 *
	 * @var string $serviceName Service Name
	 * @access public
	 */
	
	public $serviceName;
	
	
	/**
	 * Port Name
	 * 
	 * @var string $portName Port Name
	 * @access public
	 */
	
	public $portName;
	
	/**
	 * Call resource operation
	 * 
	 * @param string $name Operation Name
	 * @param array $args Arguments
	 */
	
	function __call($name,$args) 
	{		
		return $this->handler->callMethod($this,$name,$args);
	}

	// end class VServiceResource
}
