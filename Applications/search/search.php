<?php

/**
 * VWP Search Entry Point 
 *  
 * @package VWP.Search
 * @subpackage Base
 * @author Ralph Ritoch <rritoch@gmail.com> 
 * @copyright (c) Ralph Ritoch 2011 - All rights reserved
 * @link http://www.vnetpublishing.com
 * @todo Documentation and Licensing 
 */
 
// No direct access

class_exists( 'VWP' ) or die( 'Restricted access' );

/**
 * Require Widget Support
 */

VWP::RequireLibrary('vwp.ui.widget');

/**
 * VWP Links Entry Point 
 *  
 * @package VWP.Search
 * @subpackage Base
 * @author Ralph Ritoch <rritoch@gmail.com> 
 * @copyright (c) Ralph Ritoch 2011 - All rights reserved
 * @link http://www.vnetpublishing.com
 * @todo Documentation and Licensing 
 */

class searchApplication extends VApplication 
{

	/**
	 * Default Theme Type
	 * 
	 * @var string Default theme type
	 * @access public
	 */
	
    public $_app_default_theme_class = 'site';
    
    /**
     * Default Widget 
     * 
     * @var string Widget id
     * @access public
     */
    
    public $_app_default_widget = 'search';
    
    /**
     * Application entry point
     * 
     * @param array $args Arguments
     * @param array $env Environment variables
     */
    
    function main($args,$env) 
    {
        
        $user =& VUser::getCurrent();
        $shellob =& $user->getShell();
                                                        
        $widgetId = $shellob->getVar('widget',$this->_app_default_widget);
                        
        if (empty($widgetId)) {                  
         return VWP::raiseWarning('No widget selected',get_class($this),null,false);
        }
   
        $widget =& $this->getWidget($widgetId);
        
        if (VWP::isWarning($widget)) {
            return $widget;
        }
   
        $task = $shellob->getVar('task');
      
        $result = $widget->runTask($task);                
        
        $widget->redirect();
              
        return $result;  
    }
 

    // end class searchApplication
}

