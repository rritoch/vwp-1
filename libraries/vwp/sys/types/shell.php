<?php 

/**
 * Virtual Web Platform - Shell Interface
 *  
 * This file provides a standard shell interface
 *   
 * @package VWP
 * @subpackage Libraries.System.Types  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License    
 */

/**
 * Require VWP Application Support
 */

VWP::RequireLibrary('vwp.application');

/**
 * Virtual Web Platform - Shell Interface
 *  
 * This interface provides standard shell functions
 *   
 * @package VWP
 * @subpackage Libraries.System.Types 
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License    
 */

abstract class VShell extends VApplication 
{
	
	/**
	 * Get Shell's User Object
	 * 
	 * @return VUser Shell user
	 * @access public
	 */
	
	abstract public function &user();
		
    /**
     * Set the value of a Shell Environment Variable
     * 
     * @param string $vname Variable Name
     * @param mixed $value Variable value
     * @param string $method Method, defaults to 'any'
     * @return boolean True on success               
     * @access public   
     */
		
	abstract public function setVar($name,$value = null,$method = 'any');
	
	/**
     * Get a Shell Environment Variable
     * 
     * @param string $vname Variable name
     * @param mixed $default Default value
     * @param string $method Method name, defaults to any
     * @return mixed Variable value
     * @access public      
     */
	
	abstract public function getVar($name,$default = null,$method = 'any');
	
	/**
	 * Get Current Screen ID
	 * 
	 * @return string Screen ID
	 * @access public
	 */
	 
	abstract public function getScreen();
	
	/**
	 * Get Shell Environment
	 * 
	 * Note: If method is not provided complete shell environment is returned
	 * 
	 * @return array Shell Environment
	 * @access public
	 */
	
    abstract public function &getEnv($method = null);
        
    /**
     * Get a Shell Environment Variable as a command
     * 
     * @param string $vname Variable name
     * @param mixed $default Default value
     * @param string Method name, defaults to any
     * @return string Command
     * @access public      
     */
  
  
    abstract public function getCmd($vname,$default = false, $method = 'any'); 
        

    /**
     * Get a Shell Environment Variable as a word
     * 
     * @param string $vname Variable name
     * @param mixed $default Default value
     * @param string Method name, defaults to any
     * @return string Word value
     * @access public      
     */
      
    abstract public function getWord($vname,$default = null, $method = 'any'); 
    
     /**
      * Get list of selected checkboxes from a Shell Environment Variable
      * 
      * @param string|array Checkboxes Environment Variable
      * @param string Method  
      * @return array Selected Checkboxes
      */         

    abstract public function getChecked($checkboxList,$method = 'any'); 

    /**
     * Execute Application
     * 
     * @param string $command Command to execute
     * @param array $envp Environment
     * @param object $stdio Stdio driver object 
     * @return string|error Application output on success      
     * @access private   
     */  
   
    abstract public function execute($command, &$envp, &$stdio); 
    
    /**
     * Shell Application Entry Point
     * 
     * @param array $args Arguments
     * @param array $env Environment Variables
     * @access public
     */
    
    abstract public function main($args,$env);
    
	// End class VShell
}
