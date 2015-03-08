<?php 

/**
 * VWP v1.0.1 Upgrade 
 *  
 * This is the default entry for the VWP Upgrade 
 *  
 * @package VWPUpgrade
 * @subpackage Base
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License 
 */

// no direct access
class_exists('VWP') || die();

/**
 * VWP v1.0.1 Upgrade 
 *  
 * This is the default entry for the VWP Upgrade 
 *  
 * @package VWPUpgrade
 * @subpackage Base
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License 
 */

class VWPUpgradeApplication extends VApplication 
{
	/**
	 * Define default theme
	 * 
	 * @var string $_app_default_theme_class Default theme
	 * @access public
	 */
	
    public $_app_default_theme_class = 'site';
    
	/**
	 * Define default widget
	 * 
	 * @var string $_app_default_widget Default theme
	 * @access public
	 */
        
    public $_app_default_widget = 'vwpupgrade';

    /**
     * VDom Application Entry Point
     * 
     * @param array $args Command line arguments
     * @param array $env Shell environment
     * @access public
     */
  
    function main($args,$env) 
    {                
    	$shellob =& v()->shell();

        $widgetId = $shellob->getVar('widget',$this->_app_default_widget);
                        
        if (empty($widgetId)) {                  
            return VWP::raiseWarning('No widget selected',get_class($this),null,false);
        }

        $widget =& $this->getWidget($widgetId);
        
        if (VWP::isWarning($widget)) {
            return $widget;
        }

        $in = $shellob->getVar('in','http');
        
        $widget->setRequestListener($in,$widgetId);
        
        $listener =& $widget->getRequestListener();
        
        $args =& $listener->getTaskParams();        
        $task = $listener->getTask();
                      
        $result = $widget->runTask($task,null,$args);                
        
        $widget->redirect();
              
        return $result;  
  	}

    // end class VWPUpgradeApplication
}
