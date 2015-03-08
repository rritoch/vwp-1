<?php

/**
 * VWP Application Entry Point 
 *  
 * This is the default entry for system configuration!  
 *  
 * @package    VWP
 * @subpackage Base
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @link http://www.vnetpublishing.com VNetPublishing.Com 
 * @copyright (c) Ralph Ritoch - All rights reserved
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License 
 * @todo Documentation and Licensing 
 */
 
// No direct access

class_exists( 'VWP' ) or die( 'Restricted access' );

/**
 * Requires Application Support
 */

VWP::RequireLibrary('vwp.application');

/**
 * VWP Application Entry Point 
 *  
 * This is the default entry for system configuration!  
 *  
 * @package    VWP
 * @subpackage Base
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @link http://www.vnetpublishing.com VNetPublishing.Com 
 * @copyright (c) Ralph Ritoch - All rights reserved
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License 
 */


class vwpApplication extends VApplication 
{

	/**	 
	 * Default Theme Type
	 * 
	 * @var string $_app_default_theme_class Theme Type
	 */
	
    var $_app_default_theme_class = 'admin';

	/**	 
	 * Default Widget
	 * 
	 * @var string $_app_default_widget Widget ID
	 */    
    
    var $_app_default_widget = 'configure';

    /**     
     * Application entry point
     * 
     * @param array $args Command line arguments
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

    /**     
     * Class Constructor
     * 
     * Initialize theme type and restrict access
     * 
     * @access public
     */
    
    function __construct() 
    {
        parent::__construct();
        $user =& VUser::getCurrent();
        if (!$user->allow('Access VWP Administration',$this->getResourceID())) {
            $this->blockAccess();  
        }    
      
        VWP::setTheme($this->_app_default_theme_class);  
        
    }

    // End Class vwpApplication
}

