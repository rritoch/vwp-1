<?php

/**
 * Content Manager Entry Point 
 *  
 * This is the default entry for any components
 * there is no need to modify this file in any way!  
 *  
 * @package    VWP.Content
 * @subpackage Base
 * @author Ralph Ritoch <rritoch@gmail.com> 
 * @copyright (c) Ralph Ritoch - All rights reserved
 * @link http://www.espirithosting.com
 * @todo Documentation and Licensing 
 */
 
// No direct access

class_exists( 'VWP' ) or die( 'Restricted access' );

VWP::RequireLibrary('vwp.ui.widget');

class contentApplication extends VApplication {

    var $_app_default_theme_class = 'site';
    var $_app_default_widget = 'home';
    
  function main($args,$env) {
        
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
