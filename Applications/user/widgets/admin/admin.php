<?php

/**
 * User Configuration Widget
 *  
 * @package    VWP.User
 * @subpackage Widgets
 * @author Ralph Ritoch <rritoch@gmail.com> 
 * @copyright (c) Ralph Ritoch - All rights reserved
 * @link http://www.vnetpublishing.com 
 */

VWP::RequireLibrary("vwp.ui.widget");

/**
 * User Configuration Widget 
 *  
 * @package    VWP.User
 * @subpackage Widgets
 * @author Ralph Ritoch <rritoch@gmail.com> 
 * @copyright (c) Ralph Ritoch - All rights reserved
 * @link http://www.vnetpublishing.com 
 */


class User_Widget_Admin extends VWidget {

      
 /**
  * Display Login Widget
  * 
  * @access public    
  */
     
 function display($tpl = null) {
   
  $w = $this->getWidget('admin.admin','user_Admin_Widget_');
  $out = $w->build();  
  $this->assignRef('out',$out);
  parent::display();
 }

} // end class