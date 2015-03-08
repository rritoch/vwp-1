<?php

/**
 * Persistent Loop Support
 * 
 * Reserved for future use!
 * 
 * @todo Implement Persistent Loop Support
 * @package VWP
 * @subpackage Libraries.System  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License
 */

/**
 * Persistent Loop Support
 * 
 * Reserved for future use!
 *  
 * @package VWP
 * @subpackage Libraries.System  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License
 */

class VLoop extends VObject 
{
	/**
	 * Loop Type
	 * 
	 * @var string $_type Loop type
	 * @access private
	 */
	
	protected $_type;
	
	/**
	 * Source Array
	 * 
	 * @var array $_array Source array
	 * @access private
	 */
	
	protected $_array;
	
	/**
	 * Loop body callback
	 * 
	 * @var mixed $_body_cb Callback
	 * @access private
	 */
	
	protected $_body_cb;

	/**
	 * Loop end callback
	 * 
	 * @var mixed $_end_cb Callback
	 * @access private
	 */
		
	protected $_end_cb;

	/**
	 * Loop condition callback
	 * 
	 * @var mixed $_cond_cb Callback
	 * @access private
	 */	
	
	protected $_cond_cb;
	
	/**
	 * Loop initialization callback
	 * 
	 * @var mixed $_init_cb Callback
	 * @access private
	 */		
	
	protected $_init_cb;
	
	/**
	 * Loop prepare next loop interation callback
	 * 
	 * @var mixed $_next_cb Callback
	 * @access private
	 */
	
	protected $_next_cb;

	/**
	 * Start Loop
	 * 
	 * @access public
	 */
	
	function start() 
	{
		
	}
	
	/**
	 * Save Loop state
	 * 
	 * @access public
	 */
	
	function save() 
	{
		
	}

	/**
	 * Load Loop state
	 * 
	 * @access public
	 */	
	
	function load() 
	{
		
	}
	
	/**
	 * Resume Loop
	 * 
	 * @access public
	 */
	
	function resume()
	{
		
	}
	
	// end class VLoop
}
