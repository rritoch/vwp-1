<?php

/**
 * User System Entry Point 
 *  
 * This is the entry point for the User Application!
 * The user system provides a front end interface to 
 * the VWP user system.    
 *  
 * @package    VWP.User
 * @subpackage Base
 * @author Ralph Ritoch <rritoch@gmail.com> 
 * @copyright (c) Ralph Ritoch - All rights reserved
 * @link http://www.vnetpublishing.com
 * @todo Documentation and Licensing 
 */



/**
 * User System Entry Point 
 *  
 * This class is the entry point for the User Application!
 * The user system provides a front end interface to 
 * the VWP user system.    
 *  
 * @package    VWP.User
 * @subpackage Base
 * @author Ralph Ritoch <rritoch@gmail.com> 
 * @copyright (c) Ralph Ritoch - All rights reserved
 * @link http://www.vnetpublishing.com 
 */
 
 class userApplication extends VApplication {
    var $_app_default_theme_class = 'site';
    var $_app_default_widget = 'user';
    
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
   $user =& VUser::getCurrent();
   $shellob =& VWP::getShell();
      
   $p = explode(".",$shellob->getVar('widget'));
   $widget = array_shift($p);
   if ($widget == "admin") {
    $user =& VUser::getCurrent();
        
    if (!$user->allow('Access user administration',$this->getResourceID())) {
     $this->blockAccess();  
    }
    VWP::setTheme('admin');    
   } elseif ($widget == "settings") {
       $user =& VUser::getCurrent();
       
       $S_Id = array("widget"=>"settings","mode"=>"deny");
       
       if ($user->is_foreign()) {
           $this->blockAccess();
       } elseif(empty($user->username)) {
           $this->blockAccess();
       } elseif ($user->deny('Block access to changing user settings.',$this->getResourceID(),$S_Id)) {
           $this->blockAccess();
       }
   }
   parent::__construct();
  }
   
 } // end class
 