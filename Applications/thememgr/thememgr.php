<?php

/**
 * Theme Manager Entry Point 
 *  
 *  
 * @package    VWP.Thememgr
 * @subpackage Base
 * @author Ralph Ritoch <rritoch@gmail.com> 
 * @copyright (c) Ralph Ritoch - All rights reserved
 * @link http://www.vnetpublishing.com 
 */

/**
 * Theme Manager Entry Point 
 *  
 *  
 * @package    VWP.Thememgr
 * @subpackage Base
 * @author Ralph Ritoch <rritoch@gmail.com> 
 * @copyright (c) Ralph Ritoch - All rights reserved
 * @link http://www.vnetpublishing.com
 * @todo Documentation and Licensing
 */

class ThememgrApplication extends VApplication {

    var $_app_default_theme_class = 'admin';
    var $_app_default_widget = 'thememgr';

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
 
 function __construct() {
  $user =& VUser::getCurrent();        
  if (!$user->allow('Access theme administration',$this->getResourceID())) {
   $this->blockAccess();  
  } 
  VWP::setTheme($this->_app_default_theme_class);
  parent::__construct();  
 }
}
