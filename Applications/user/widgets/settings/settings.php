<?php

/**
 * User Settings Widget 
 *  
 * @package    VWP.User
 * @subpackage Widgets
 * @author Ralph Ritoch <rritoch@gmail.com> 
 * @copyright (c) Ralph Ritoch - All rights reserved
 * @link http://www.vnetpublishing.com 
 */

VWP::RequireLibrary("vwp.ui.widget");

/**
 * New User Widget 
 *  
 * @package    VWP.User
 * @subpackage Widgets
 * @author Ralph Ritoch <rritoch@gmail.com> 
 * @copyright (c) Ralph Ritoch - All rights reserved
 * @link http://www.vnetpublishing.com 
 */

class User_Widget_Settings extends VWidget {

 /**
  * Register new user
  * 
  * @param mixed $tpl Optional  
  * @access public    
  */
       
 function save_settings($tpl = null) {
  $shellob =& VWP::getShell();
  
  $user = $this->getModel("user");
  
  $credentials = $shellob->getVar("acctinfo");
  
  if ($credentials["password"] == $credentials["confirm_password"]) {
   
   $userinfo = $credentials;
   unset($userinfo["confirm_password"]);
   $result = $user->updateSettings($userinfo);
   if (VWP::isWarning($result)) {
    $result->ethrow();
    $acctinfo = $credentials;
   } else {
    VWP::addNotice("Account settings updated!");    
    $acctinfo = $user->getSettings();
   }  
  } else {

   $acctinfo = $credentials;
   VWP::raiseWarning("Passwords do not match, please confirm your password!");
  }
  
  
  $this->assignRef('acctinfo',$acctinfo);
   $shellob =& VWP::getShell();
   
  $screen = $shellob->getScreen();
  $this->assignRef('screen',$screen);
  parent::display();
 }

 /**
  * Display user settings widget
  * 
  * @param mixed $tpl Optional  
  * @access public    
  */

 function display($tpl = null) {
  
  $shellob =& VWP::getShell();
   
  $register = array();
  $user = $this->getModel("user");
  $acctinfo = $user->getSettings();
  $this->assignRef('acctinfo',$acctinfo);
  
  $screen = $shellob->getScreen();
  $this->assignRef('screen',$screen);
  parent::display();
 }

} // end class