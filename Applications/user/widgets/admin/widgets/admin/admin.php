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


class User_Admin_Widget_Admin extends VWidget 
{

    /**
     * Save configuration settings
     */
  
    function save_config() 
    {
        $shellob =& VWP::getShell();
  
        $admin = $this->getModel("admin");
        $config = array();  
        $required = array("user_database",
                          "table_prefix",
                          "require_email_verification",
                          "email_from_name",
                          "email_address",
                          "smtp_host",
                          "smtp_port",
                          "smtp_username",
                          "smtp_password"
                          );
        
        foreach($required as $k) {
            $config[$k] = $shellob->getVar($k,'');
        }
        $r = $admin->saveConfig($config);
        if (VWP::isWarning($r)) {
            $r->ethrow();
        } else {
            VWP::addNotice("Configuration saved!");
        }
        $this->display();
    }
      
    /**
     * Display Login Widget
     * 
     * @access public    
     */
     
    function display($tpl = null) 
    {

        $shellob =& VWP::getShell();
        $current_widget = 'admin.admin';
        $shellob->setVar('current_widget',$current_widget);

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
            $dblist = array();
            $config = array();
            $admin->ethrow();
        } else {
     
            $config = $admin->getConfig();
            if (VWP::isWarning($config)) {
                if (VWP::isError($config)) {
                    $config->ethrow();
                }
                $config = array(); 
            }
  
            $required = array("user_database","table_prefix");
            foreach($required as $k) {
                if ((!isset($config[$k])) || (empty($config[$k]))) {
                    $config[$k] = '';
                }
            }
  
            $dblist = $admin->getDBList();  
        }
       
        $this->assignRef('config',$config);
        $this->assignRef('dblist',$dblist);
        $screen = $shellob->getScreen();
        $this->assignRef('screen',$screen);   
        parent::display();
    }
    
    // end class User_Admin_Widget Admin
} 