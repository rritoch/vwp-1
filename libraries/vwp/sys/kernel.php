<?php

/**
 * Virtual Web Platform - Kernel
 *  
 * This file provides the system kernel        
 * 
 * @package VWP
 * @subpackage Libraries.System  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License   
 */

/**
 * Virtual Web Platform - Kernel
 *  
 * This class provides the system kernel        
 * 
 * @package VWP
 * @subpackage Libraries.System  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License   
 */

class VKernel extends VObject
{

	/**
	 * Process stack
	 * 
	 * @var array $_proc Process stack
	 * @access private
	 */
	
    public $_proc = array();
    
    /**
     * Get kernel instance
     * 
     * @return VKernel kernel
     * @access public
     */
    
    public static function &o() {
        static $kern;
        if (!isset($kern)) {
            $kern = new VKernel;
        }
        return $kern;
    }
    
    /**
     * Create a process
     * 
     * Note: There is a limit of 10 constructor arguments
     * 
     * @param string $className Class name
     * @param array $args Constructor arguments
     * @access public
     */
    
    function createProcess($className,$args = null) 
    {
        if (!class_exists($className)) {
            return VWP::raiseWarning("Class $className not found!",'VKernel',ERROR_CLASSNOTFOUND,false);
        }
        $args = func_get_args();
        array_shift($args);
        while(count($args) < 10) {
            array_push($args,null);
        }
        $pid = count($this->_proc);
        
        $this->_proc[$pid] = new $className($args[0],
                                            $args[1],
                                            $args[2],
                                            $args[3],
                                            $args[4],
                                            $args[5],
                                            $args[6],
                                            $args[7],
                                            $args[8],
                                            $args[9]);
        return $pid;
    }
    
    /**
     * Get process by ID
     * 
     * @param integer $pid Process id
     * @access public
     */
    
    function &getProc($pid) 
    {        
        return $this->_proc[$pid];
    }
    
    // End class VKernel
}
