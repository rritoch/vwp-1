<?php

/**
 * Content Manager Entry Point 
 *  
 * @package    VWP.Content
 * @subpackage Base
 * @author Ralph Ritoch <rritoch@gmail.com> 
 * @copyright (c) Ralph Ritoch 2011 - All rights reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License 
 */
 
// No direct access
class_exists( 'VWP' ) or die( 'Restricted access' );

/**
 * Require Widget Support
 */

VWP::RequireLibrary('vwp.ui.widget');

/**
 * Content Manager Entry Point 
 *  
 * @package    VWP.Content
 * @subpackage Base
 * @author Ralph Ritoch <rritoch@gmail.com> 
 * @copyright (c) Ralph Ritoch 2011 - All rights reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License 
 */

class contentApplication extends VApplication 
{

	/**
	 * Default Theme Class
	 * 
	 * @var string $_app_default_theme_class Theme Class
	 * @access public
	 */
	
    public $_app_default_theme_class = 'site';
    
    /**
     * Default Widget Name
     * 
     * @var string $_app_default_widget Widget Name
     * @access public
     */
    
    public $_app_default_widget = 'home';
    
    /**
     * Application Entry Point
     * 
     * @param array $args Arguments
     * @param array $env Environment Variables
     * @return mixed Result
     * @access public
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
     * @access public
     */
    
    function __construct() 
    {
 	  
        $user =& VUser::getCurrent();
        $shellob =& VWP::getShell();
    
        $p = explode(".",$shellob->getVar('widget'));
        $widget = array_shift($p);  
        $S_Id = array('base_widget'=>$widget);
  
        switch($widget) {
            case "admin":
              if (!$user->allow('Access content administration',$this->getResourceID(),$S_Id)) {
                  $this->blockAccess();  
              }  
              VWP::setTheme('admin');
              break;
            case "catmgr":   
            case "articlemgr":
                 if (!$user->allow('Access content administration',$this->getResourceID(),$S_Id)) {
                     $this->blockAccess();  
                 }
                 break;      
        }

        parent::__construct();
   
        $admin =& $this->getModel('admin');
        if (VWP::isWarning($admin)) {
           $admin->ethrow();
        } else {
  	
            switch($widget) {
                case "catmgr":   
                case "articlemgr":
                    $cfg = $admin->getConfig();
                    if (VWP::isWarning($cfg)) {
                    	$cfg->ethrow();
                    } else {
                        if (isset($cfg['edit_mode_theme_type']) && $cfg['edit_mode_theme_type'] == 'admin') {
                            VWP::setTheme('admin');
                        }
                    }
                    break;                 
            }
        }
    }

    // end class contentApplication
}
