<?php

/**
 * TinyMCE Editor Interface
 *        
 * @package    VWP.User
 * @subpackage Base
 * @author Ralph Ritoch <rritoch@gmail.com> 
 * @copyright (c) Ralph Ritoch - All rights reserved
 * @link http://www.vnetpublishing.com
 * @todo Documentation and Licensing 
 */

/**
 * TinyMCE Editor Interface
 *        
 * @package    VWP.User
 * @subpackage Base
 * @author Ralph Ritoch <rritoch@gmail.com> 
 * @copyright (c) Ralph Ritoch - All rights reserved
 * @link http://www.vnetpublishing.com 
 */

class TinymceApplication extends VApplication {
    var $_app_default_theme_class = 'site';
    var $_app_default_widget = 'editor';
    
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
 
  function __construct() {
   parent::__construct();
   
  }
   
 } // end class
 