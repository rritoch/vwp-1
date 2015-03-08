<?php

/**
 * Menu Entry Point 
 *    
 * @package    VWP.Menu
 * @subpackage Base
 * @author Ralph Ritoch  
 * @copyright (c) Ralph Ritoch <rritoch@gmail.com>
 * @link http://www.vnetpublishing.com
 * @todo Documentation and Licensing
 */

class menuApplication extends VApplication 
{
    var $_app_default_theme_class = null;
    var $_app_default_widget = null;
    
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
 }