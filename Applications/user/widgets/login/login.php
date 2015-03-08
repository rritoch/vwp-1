<?php

/**
 * User Login Widget 
 *  
 * @package    VWP.User
 * @subpackage Widgets
 * @author Ralph Ritoch <rritoch@gmail.com> 
 * @copyright (c) Ralph Ritoch - All rights reserved
 * @link http://www.vnetpublishing.com 
 */

VWP::RequireLibrary("vwp.ui.widget");

/**
 * User Login Widget 
 *  
 * @package    VWP.User
 * @subpackage Widgets
 * @author Ralph Ritoch <rritoch@gmail.com> 
 * @copyright (c) Ralph Ritoch - All rights reserved
 * @link http://www.vnetpublishing.com 
 */


class User_Widget_Login extends VWidget 
{

    /**
     * Display Login Widget
     * 
     * @access public    
     */
     
    function display($tpl = null) 
    {
        $result = $this->runService('user');
        if (VWP::isWarning($result)) {
            $result->ethrow(); 
        }

        $route =& VRoute::getInstance();
        
        $register_url = 'index.php?app=user&widget=new';
        $remind_url = 'index.php?app=user&widget=remind';         	
        $confirm_url = 'index.php?app=user&widget=confirm';
        $register_url = $route->encode($register_url);
        $remind_url = $route->encode($remind_url);
        $confirm_url = $route->encode($confirm_url);
        $this->assignRef('register_url',$register_url);
        $this->assignRef('remind_url',$remind_url);
        $this->assignRef('confirm_url',$confirm_url);   
  
        parent::display();
    }
    
    // end class User_Widget_Login
} 