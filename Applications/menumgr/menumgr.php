<?php

/**
 * Menu Manager Entry Point 
 *  
 * This is the default entry for any components!  
 *  
 * @package    VWP.Menumgr
 * @subpackage Base
 * @author Ralph Ritoch  
 * @copyright (c) Ralph Ritoch <rritoch@gmail.com>
 * @link http://www.vnetpublishing.com
 * @todo Documentation and Licensing
 */
 
// No direct access

class_exists( 'VWP' ) or die( 'Restricted access' );

VWP::RequireLibrary('vwp.application');

class menumgrApplication extends VApplication {

    var $_app_default_theme_class = 'admin';
    var $_app_default_widget = 'menumgr';

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
  * Class constructor
  */
     
 function __construct() {
  $user =& VUser::getCurrent();
  if (!$user->allow('Access menu administration',$this->getResourceID())) {
   $this->blockAccess();  
  }
     
  VWP::setTheme($this->_app_default_theme_class); 
  //self::setDefaultWidgetName("root");
  parent::__construct();
 }

}