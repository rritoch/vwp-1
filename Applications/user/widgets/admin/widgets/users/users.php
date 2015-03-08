<?php

/**
 * User Configuration Widget
 *  
 * @package    VWP.User.Admin
 * @subpackage Widgets
 * @author Ralph Ritoch <rritoch@gmail.com> 
 * @copyright (c) Ralph Ritoch - All rights reserved
 * @link http://www.vnetpublishing.com 
 */

VWP::RequireLibrary("vwp.ui.widget");

/**
 * User Configuration Widget 
 *  
 * @package    VWP.User.Admin
 * @subpackage Widgets
 * @author Ralph Ritoch <rritoch@gmail.com> 
 * @copyright (c) Ralph Ritoch - All rights reserved
 * @link http://www.vnetpublishing.com 
 */


class User_Admin_Widget_Users extends VWidget 
{


    function register_user($tpl = null) 
    {
  
        $shellob =& VWP::getShell();
  
        $userinfo = $shellob->getVar('userinfo',null);
    
        if (!is_array($userinfo)) {
            VWP::raiseWarning("Missing user data!",get_class($this));
            return $this->new_user($tpl);
        }

        $admin = $this->getModel("admin");
        $result = $admin->registerUser($userinfo);
      
        if (VWP::isWarning($result)) {
            $result->ethrow();
            return $this->new_user($tpl);
        }

        return $this->edit_user();
    }


    function new_user_reset($tpl = null) 
    {
        $shellob =& VWP::getShell();
        $userinfo = array(
            "name"=>'',
            "username"=>'',
            "email"=>'',
            "password"=>'',
            "confirm_password"=>'',
            "_admin"=>'0'
           );   

      
        $this->setLayout('register');  
        $this->assignRef('userinfo',$userinfo);
        $screen = $shellob->getScreen();
        $this->assignRef('screen',$screen);        
        parent::display();  
    }

    /**
     * Display new user widget
     * 
     * @param mixed $tpl Optional
     * @access public
     */

    function new_user($tpl = null) 
    {
        $shellob =& VWP::getShell(); 
        $userinfo = $shellob->getVar('userinfo',null);
        if (!is_array($userinfo)) {
            $userinfo = array(
                "name"=>'',
                "username"=>'',
                "email"=>'',
                "password"=>'',
                "confirm_password"=>'',
                "_admin"=>'0'
            );   
        }
      
        $this->setLayout('register');  
        $this->assignRef('userinfo',$userinfo);
        $screen = $shellob->getScreen();
        $this->assignRef('screen',$screen);      
        parent::display();  
    }
 
    /**
     * Save user settings
     * 
     * @param mixed $tpl Optional
     * @access public
     */   

    function save_user($tpl = null) 
    {
 
        $shellob =& VWP::getShell();
        $userinfo = $shellob->getVar('userinfo');
        if (!is_array($userinfo)) {
            VWP::raiseWarning("Missing input fields!",get_class($this));
            return $this->display();
        }
        if (empty($userinfo["password"]) && (empty($userinfo["confirm_password"]))) {
            unset($userinfo["password"]);
            unset($userinfo["confirm_password"]);  
        }
  
        $admin = $this->getModel("admin");
  
        $result = $admin->saveUser($userinfo);
        if (VWP::isWarning($result)) {
            $result->ethrow();
        } else {
            VWP::addNotice("User saved!");
        }
  
        return $this->edit_user($tpl); 
    }
 
    /**
     * Delete Users
     * 
     * @param mixed $tpl Optional
     * @access public
     */
 
    function delete_users($tpl = null) 
    {
 	    $shellob =& VWP::getShell();
 	    $list = $shellob->getChecked('ck');
 	    
 	    $admin = $this->getModel("admin");
 	    
 	    if (count($list) > 0) {
 	        $result = $admin->deleteUsers($list);
 	        if (VWP::isWarning($result)) {
 	        	$result->ethrow();
 	        } else {
 	        	VWP::addNotice('Users deleted!');
 	        }
 	    } else {
 	    	VWP::raiseWarning('No users selected',__CLASS__);
 	    }
 	    
 	    return $this->display();    	
    }
    
    /**
     * Edit user
     * 
     * @param mixed $tpl Optional
     * @access public
     */
         
    function edit_user($tpl = null) {
        $shellob =& VWP::getShell();
        $u = $shellob->getVar('userinfo',null);
        if (is_array($u)) {
            $selected = array($u["username"]);
        } else {    
            $ck = $shellob->getVar('ck',array());
            $selected = array();
            foreach($ck as $key=>$val) {
                if (strtolower($val) == "on") {
                    array_push($selected,$key);
                }
            }
        }
        
        if (count($selected) < 1) {
            VWP::raiseWarning("No user selected!",get_class($this));
            return $this->display($tpl);
        }
    
        $admin = $this->getModel("admin");
        $userinfo = $admin->getUser( $selected[0] );
    
        if (VWP::isWarning($userinfo)) {
            $userinfo->ethrow();
            return $this->display();
        }
  
        $this->setLayout('edit');
  
        $userinfo["password"] = '';
        $userinfo["confirm_password"] = '';
        $this->assignRef('userinfo',$userinfo);

        $screen = $shellob->getScreen();
        $this->assignRef('screen',$screen);
      
        parent::display();
    }
      
    /**
     * Display Login Widget
     * 
     * @param mixed $tpl Optional
     * @access public    
     */
     
    function display($tpl = null) 
    {
        $shellob =& VWP::getShell();
        $current_widget = 'admin.users';
        $shellob->setVar('current_widget',$current_widget);
  
        $screen = $shellob->getScreen();
        $this->assignRef('screen',$screen);
        
        // setup menu

        $menu = $this->getWidget("admin.menu");
    
        if (!VWP::isWarning($menu)) {
            $menu = $menu->build();   
        }
   
        $menu_foot = $this->getWidget("admin.menu");
    
        if (!VWP::isWarning($menu_foot)) {
            $menu_foot = $menu_foot->build('foot');   
        }
  
        if (VWP::isWarning($menu)) {
            $menu->ethrow();
            $menu = null;
        }

        if (VWP::isWarning($menu_foot)) {
            $menu_foot->ethrow();
            $menu_foot = null;
        }  

        $this->assignRef('menu',$menu);
        $this->assignRef('menu_foot',$menu_foot);
   
        $admin = $this->getModel("admin");
  
        if (VWP::isWarning($admin)) { 
            $users = array();   
            $admin->ethrow();
        } else {
            $users = $admin->getUsers();  
        }
        $this->assignRef('users',$users);  
        parent::display();
    }

    // end class User_Admin_Widget_Users
}
 