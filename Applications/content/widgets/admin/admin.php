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


class Content_Widget_Admin extends VWidget 
{

    /**
     * Save configuration settings
     */
  
    function save_config() 
    {
        $shellob =& VWP::getShell();
  
        $admin = $this->getModel("admin");
        $config = array();  
        $required = $admin->getRequiredSettings();
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
     * Display Configuration Settings
     * 
     * @param mixed $tpl Optional
     * @access public    
     */
     
     function display($tpl = null) 
     {

     	$shellob =& v()->shell();
     	
        $admin = $this->getModel("admin");
  
        if (VWP::isWarning($admin)) { 
   
            $config = array();
            $admin->ethrow();
            $editors = array();
   
        } else {
            $editors = $admin->getEditors();
     
            $config = $admin->getConfig();
            if (VWP::isWarning($config)) {
                if (VWP::isError($config)) {
                    $config->ethrow();
                }
                $config = array(); 
            }
  
            $required = $admin->getRequiredSettings();
            foreach($required as $k) {
                if ((!isset($config[$k])) || (empty($config[$k]))) {
                    $config[$k] = '';
                }
            }       
        }
  
        $categories = $this->getModel("categories");
        $categories = $categories->getAll();
  
        $articles= $this->getModel("articles");
        $articles = $articles->getAll();
    
        $this->assignRef('articles',$articles);
        $this->assignRef('categories',$categories);
        $this->assignRef('editors',$editors);
        $this->assignRef('config',$config);
  
        $screen = $shellob->getScreen();
        $this->assignRef('screen',$screen);
        
        parent::display();
    }
    
    // end class Content_Widget_Admin
}
 