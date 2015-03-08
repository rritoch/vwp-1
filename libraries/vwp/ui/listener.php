<?php

/**
 * Virtual Web Platform - Request Listener
 *  
 * This file provides the Request Listener system interface        
 * 
 * <pre>
 *    Request listeners are used to produce a task request from
 *    a users request. The request listener layer abstracts the 
 *    request protocol from the task being requested. This frees 
 *    the widget from having to identify the request protocol 
 *    and extracting the request parameters. 
 * </pre>
 * 
 * @package VWP
 * @subpackage Libraries.UI  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License
 */

/**
 * Virtual Web Platform - Request Listener
 *  
 * This class provides the Request Listener system interface
 * 
 * <pre>
 *    Request listeners are used to produce a task request from
 *    a users request. The request listener layer abstracts the 
 *    request protocol from the task being requested. This frees 
 *    the widget from having to identify the request protocol 
 *    and extracting the request parameters. 
 * </pre>
 * 
 * @package VWP
 * @subpackage Libraries.UI  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License
 */


class VRequestListener extends VObject
{	
	
	/**
	 * Requested Task 
	 * 
	 * @var string $task Task
	 * @access private
	 */
	
	protected $task;
	
	/**
	 * Task Arguments
	 * 
	 * @var array $args Task Arguments
	 * @access public
	 */
	
	protected $args = array();

	/**
	 * Class Prefix
	 * 
	 * @var string $_class_prefix
	 * @access public
	 */
	
	protected $_class_prefix;
	
	/**
	 * Class Suffix
	 * 
	 * @var string $_class_prefix Class Suffix
	 * @access public
	 */
		
	protected $_class_suffix;
	
	/**
	 * Widget Path
	 * 
	 * @var string $_widget_path Widget Path
	 * @access public
	 */
	
	protected $_widget_path;
	
	/**
	 * Set Task Params
	 * 
	 * This method should be called by the implementing request listener
	 * to assign parameters from the task request.
	 * 
	 * @param array $params Task Request Parameters
	 * @access protected
	 */
	
	protected function setTaskParams(&$params) 
	{
		$this->args =& $params;
	}
	
	/**
	 * Get Class Prefix
	 * 
	 * @return string Class prefix
	 * @access public
	 */
	
	public function getClassPrefix() 
	{
	    return $this->_class_prefix;	
	}

	/**
	 * Get Class Suffix
	 * 
	 * @return string Class suffix
	 * @access public
	 */
	
	
	public function getClassSuffix() 
	{
		return $this->_class_suffix;
	}
	
	/**
	 * Set Class Parts
	 * 
	 * This method should not be called directly. It is used
	 * internally to configure the request listener.
	 * 
	 * @param string $prefix Class prefix
	 * @param string $suffix Class suffix
	 * @access public
	 */
	
	public function setClassParts($prefix,$suffix) 
	{
		$this->_class_prefix = $prefix;
		$this->_class_suffix = $suffix;
	}
	
	/**
	 * Set Widget Path
	 * 
	 * This method should not be called directly. It is used
	 * internally to configure the request listener.
	 * 
	 * @param string $path Path
	 * @access public
	 */
	
	public function setWidgetPath($path) 
	{
		$this->_widget_path = $path;
	}
	
	/**
	 * Get Widget Path
	 * 
	 * @return string Widget path
	 * @access public	 
	 */
	
	public function getWidgetPath() 
	{
		return $this->_widget_path;
	}
	
	/**
	 * Get Widget Parameters
	 * 
	 * @param string $ref Parameter data reference
	 * @return VWidgetParams|object Widget parameters if found, error or warning otherwise
	 * @access public
	 */
	
	public function &getParams($ref) 
	{
		
		$className = $this->getClassPrefix() . '_WidgetParams_' . $this->getClassSuffix();
		
		$classFile = $this->getWidgetPath() .DS.'params.php';
		
		if (!class_exists($className)) {
			if (v()->filesystem()->file()->exists($classFile)) {
				require_once($classFile);
			}
		}
		
		if (class_exists($className)) 
		{
		    $params = new $className;
		    if (!empty($ref)) {
		    	$params->loadRef($ref);
		    }
		    return $params;	
		}
		
		$err = VWP::raiseWarning('Widget has no custom parameters!',__CLASS__,null,false);
		return $err;
	}
	
	/**
	 * Get Task Parameters
	 * 
	 * @return array Task Parameters
	 * @access public
	 */
	
	public function &getTaskParams() 
	{
		if (!isset($this->task)) { 
		    $method = $this->getTask();
				
		    if (method_exists($this,$method)) {
			    $this->$method();
			    $this->set('task',$method);
		    }
		}
		return $this->args;
	}

	/**
	 * Get Current Task Request Method Name
	 * 
	 * Note: This method should be overridden by all request listeners
	 * 
	 * @return string Task method name
	 * @access public
	 */
	
	public function getTask() 
	{
		return v()->shell()->getVar('task');
	}
	
	// end class VRequestListener	 
}
