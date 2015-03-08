<?php

/**
 * User Management Menu
 *  
 * This file provides the user management menu for the
 * User application!  
 *  
 * @package    VWP.User.Admin
 * @subpackage Widgets
 * @author Ralph Ritoch <rritoch@gmail.com> 
 * @copyright (c) Ralph Ritoch - All rights reserved
 * @link http://www.vnetpublishing.com 
 */

// No direct access

class_exists( 'VWP' ) or die( 'Restricted access' );

VWP::RequireLibrary('vwp.ui.widget');

/**
 * User Management Menu
 *  
 * This class provides the user management menu for the
 * User application!  
 *  
 * @package    VWP.User.Admin
 * @subpackage Widgets
 * @author Ralph Ritoch <rritoch@gmail.com> 
 * @copyright (c) Ralph Ritoch - All rights reserved
 * @link http://www.vnetpublishing.com 
 */
 
class User_Admin_Widget_Menu extends VWidget {

 function foot($tpl = null) {    
  $this->setLayout('footer');  
  parent::display($tpl);
  
 }
 
   function display($tpl = null) 
   {
       $shellob =& VWP::getShell();
       $current_widget = $shellob->getVar('current_widget');
       $this->assignRef('current_widget',$current_widget);
  
       $urls = array(
        'users'=>'index.php?app=user&widget=admin.users',
        'configure'=>'index.php?app=user&widget=admin.admin'     
       );
       $vroute =& VRoute::getInstance();

       $urls['users'] = $vroute->encode($urls['users']);
       $urls['configure'] = $vroute->encode($urls['configure']);
       $this->assignRef('urls',$urls);
          
       parent::display($tpl);
   }

}