<?php

/**
 * Virtual Web Platform - Callback
 *  
 * This file provides Callback support   
 * 
 * @package VWP
 * @subpackage Libraries.System 
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License    
 */

/**
 * Virtual Web Platform - Callback
 *  
 * This class provides Callback support which links a callback
 * to a shell.   
 * 
 * @package VWP
 * @subpackage Libraries.System
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License    
 */

class VCallback extends VType 
{
	/**	 
	 * System callback
	 * 
	 * @var array Callback
	 * @access private
	 */
	
    protected $cb;    
    
    /**     
     * Callback shell buffer
     * 
     * @var array Shell Buffer
     */
    
    protected $_shell;
    
    /**
     * Get system callback
     * 
     * @return array Callback
     * @access public
     */
    
    function getCallback() 
    {
        return $this->cb;
    }
    
    /**
     * Get action
     * 
     * @return array callback action
     * @access public
     */
    
    function getAction() 
    {
        return array($this,'doAction');
    }
    
    /**
     * Perform callback action
     * 
     * @access public
     */
    
    function doAction() 
    {    
        $user =& VUser::getCurrent();
        $user->doCallback($this);
    }

    /**
     * Get callback shell
     * 
     * @return object callback shell
     * @access public
     */
    
    function &getShell() 
    {
        return $this->_shell[0];
    }
    
    /**
     * Class Constructor
     *
     * @param array $callback System callback
     */
    
    function __construct($callback) 
    {        
        $this->cb = $callback;
        $this->_shell = array(VWP::getShell());
    }
    
    // end class VCallback
}