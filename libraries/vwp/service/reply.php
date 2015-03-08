<?php

/**
 * Virtual Web Platform - Service Reply
 *  
 * This file provides the Service Reply Interface 
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
 * This class provides the Service Reply Object 
 *        
 * @package VWP
 * @subpackage Libraries.Service  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch 2011 - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

class VServiceReply extends VObject 
{
	/**
	 * Ready Flag
	 * 
	 * @var boolean True if reply is ready
	 * @access public
	 */
	
	protected $ready;
	
	/**
	 * Reply Value
	 * 
	 * @var mixed Reply
	 * @access public
	 */
	
	protected $reply;
	
	/**
	 * Raw Reply
	 * 
	 * @var string Raw Reply
	 * @access public
	 */
	
	protected $raw_reply;
	
	/**
	 * Reply Handler
	 * 
	 * @var object Reply Handler
	 * @access public
	 */
	
	protected $replyHandler;

	/**
	 * Resource
	 * 
	 * @var VServiceResource Resource
	 * @access public
	 */
	
	protected $resource;

	/**
	 * Set Reply Value
	 * 
	 * @param string $src Raw reply data
	 * @access public
	 */
	
	function setReply($src)
	{
		$this->raw_reply = $src;
		
		if (isset($replyHandler)) {		
		    $this->reply = $this->replyHandler->decodeReply($src);
		} else {
			$this->reply = $src;
		}
		
		$this->ready = true;
	}
	
	/**
	 * Wait For Reply
	 * 
	 * @param integer $timeout Timeout in seconds
	 * @access public
	 */
	
	function wait($timeout) 
	{
		if (!$this->ready) {
		    $this->requestHandler->poll();
		}
		return $this->ready;
	}
	
	/**
	 * Get Return Value
	 *
	 * @return mixed Return value on success, error or warning otherwise
	 * @access public
	 */
	
	function &getReturnValue() 
	{
		if (!$this->ready) {
			return VWP::raiseWarning('Not ready!',__CLASS__,null,false);
		}
		
		return $this->reply;		
	}
	
	/**
	 * Class Constructor
	 * 
	 * @param VServiceResource $resource Resource
	 * @param object $replyHandler Reply Handler
	 * @access public
	 */
	
	function __construct($resource,$replyHandler) 
	{
	    $this->replyHandler = $replyHandler;
	    $this->resource = $resource;	
	}
	
	// end class VServiceReply
}
