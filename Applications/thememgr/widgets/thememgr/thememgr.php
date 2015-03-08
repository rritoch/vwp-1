<?php

/**
 * Theme manager Widget 
 *  
 * @package    VWP.Thememgr
 * @subpackage Widgets
 * @author Ralph Ritoch <rritoch@gmail.com> 
 * @copyright (c) Ralph Ritoch - All rights reserved
 * @link http://www.vnetpublishing.com
 */

VWP::RequireLibrary("vwp.ui.widget");

/**
 * Theme manager Widget 
 *  
 * @package    VWP.Thememgr
 * @subpackage Widgets
 * @author Ralph Ritoch <rritoch@gmail.com> 
 * @copyright (c) Ralph Ritoch - All rights reserved
 * @link http://www.vnetpublishing.com 
 */


class ThemeMgr_Widget_ThemeMgr extends VWidget 
{

    /**
     * Display Theme List
     * 
     * @access public    
     */
     
    function display($tpl = null) 
    {
 
        $shellob =& VWP::getShell();
        // setup tabs
  
        $current_widget = 'thememgr';
        $shellob->setVar('current_widget',$current_widget);
    
        $tabs = $this->getWidget("tabs");
    
        if (!VWP::isWarning($tabs)) {
            $tabs = $tabs->build();   
        }
   
        $tabs_foot = $this->getWidget("tabs");
    
        if (!VWP::isWarning($tabs_foot)) {
            $tabs_foot = $tabs_foot->build('foot');   
        }
  
        if (VWP::isWarning($tabs)) {
            $tabs->ethrow();
            $tabs = null;
        }

        if (VWP::isWarning($tabs_foot)) {
            $tabs_foot->ethrow();
            $tabs_foot = null;
        }  

        $this->assignRef('tabs',$tabs);
        $this->assignRef('tabs_foot',$tabs_foot);
 
        // Build Widget
     
        $themes =& $this->getModel('themes');
        $theme_list = $themes->getAll();
        $this->assignRef('theme_list',$theme_list);
  
        if (!empty($layout)) {
            $this->setLayout($layout);
        }
  
        parent::display();
    }	
	
    /**
     * Save Defaults
     * 
     * @param mixed $tpl Optional
     * @access public
     */
    
    function save_defaults($tpl = null) {
        $shellob =& VWP::getShell();
        
        $defaults = $shellob->getVar('default_theme');
        
        if (!is_array($defaults)) {
        	VWP::raiseWarning('No theme defaults found!',get_class($this));
        	return $this->display($tpl);
        }

        $themes =& $this->getModel('themes');
        $result = $themes->updateDefaults($defaults);
        
        if (VWP::isWarning($result)) {
        	$result->ethrow();
        } else {
        	VWP::addNotice('Theme defaults saved!');
        }
        
        $this->display($tpl);
    }
    
    /**
     * Display Manage Theme Form
     * 
     * @param mixed $tpl Optional
     * @access public
     */

    function manage($tpl = null) 
    {
        $shellob =& VWP::getShell();
    
        $selected = $shellob->getChecked('ck');

        $data = $shellob->getVar('theme_info');

        if ((count($selected) < 1)||(!isset($data[$selected[0]]))) {
            VWP::raiseWarning('No theme selected!',get_class($this));
           return $this->display($tpl);
        }

        $themeId = $data[$selected[0]]["id"];
        $themeType = $data[$selected[0]]["type"];


        // setup tabs
  
        $current_widget = 'thememgr';
        $shellob->setVar('current_widget',$current_widget);
    
        $tabs = $this->getWidget("tabs");
    
        if (!VWP::isWarning($tabs)) {
            $tabs = $tabs->build();   
        }
   
        $tabs_foot = $this->getWidget("tabs");
    
        if (!VWP::isWarning($tabs_foot)) {
            $tabs_foot = $tabs_foot->build('foot');   
        }
  
        if (VWP::isWarning($tabs)) {
            $tabs->ethrow();
            $tabs = null;
        }

        if (VWP::isWarning($tabs_foot)) {
            $tabs_foot->ethrow();
            $tabs_foot = null;
        }  

        $this->assignRef('tabs',$tabs);
        $this->assignRef('tabs_foot',$tabs_foot);

        $targets =& $this->getModel('targets');
        $frames =& $this->getModel('frames');
        $themes =& $this->getModel('themes');

        $params = $themes->getParams($themeType,$themeId);
        $this->assignRef('params',$params);
        
        $this->setLayout('manage_theme');

        $target_list = $targets->getAllTargets($themeType,$themeId);

        if (VWP::isWarning($target_list)) {
            $target_list->ethrow();
            $target_list = array();
        }

        $frame_list = $frames->getAll();
        if (VWP::isWarning($frame_list)) {
            $frame_list->ethrow();
            $frame_list = array();
        }


        $this->assignRef('themeId',$themeId);
        $this->assignRef('themeType',$themeType);
        $this->assignRef('target_list',$target_list);
        $this->assignRef('frame_list',$frame_list);

        parent::display($tpl);
    }

    /**
     * Uninstall Themes
     * 
     * @param mixed $tpl Optional
     * @access public
     */

    function uninstall($tpl = null) 
    {
        $shellob =& VWP::getShell();
    
        $selected = $shellob->getChecked('ck');

        $data = $shellob->getVar('theme_info');

        if ((count($selected) < 1)||(!isset($data[$selected[0]]))) {
            VWP::raiseWarning('No theme selected!',get_class($this));
           return $this->display($tpl);
        }

        $themes =& $this->getModel('themes');
        
        $themeList = array();
        foreach($selected as $idx) {
            $info = array(
              'themeType' => $data[$idx]["type"],
              'themeId' => $data[$idx]["id"]              
             );
            $themeList[] = $info;  
        }
        
        $result = $themes->uninstall($themeList);
        if ($result === true) {
            VWP::addNotice('Themes uninstalled!');	
        } else {
        	if (VWP::isWarning($result)) {
        		$result->ethrow();
        	} else {
        		VWP::raiseWarning('Uninstall themes ended with errors!',get_class($this));
        	}
        	
        }
        $this->display($tpl);
    }
    
    
    
    /**
     * Display Install Form
     * 
     * @access public    
     */
     
    function install($tpl = null) 
    {

        $shellob =& VWP::getShell();
 
        // setup tabs
  
        $current_widget = 'thememgr';
        $shellob->setVar('current_widget',$current_widget);
    
        $tabs = $this->getWidget("tabs");
    
        if (!VWP::isWarning($tabs)) {
            $tabs = $tabs->build();   
        }
   
        $tabs_foot = $this->getWidget("tabs");
    
        if (!VWP::isWarning($tabs_foot)) {
            $tabs_foot = $tabs_foot->build('foot');   
        }
  
        if (VWP::isWarning($tabs)) {
            $tabs->ethrow();
            $tabs = null;
        }

        if (VWP::isWarning($tabs_foot)) {
            $tabs_foot->ethrow();
            $tabs_foot = null;
        }  

        $this->assignRef('tabs',$tabs);
        $this->assignRef('tabs_foot',$tabs_foot);
 
        //  Build Widget
           
        $this->setLayout('install');
    
        parent::display();
    }    
    
    /**
     * Install Theme from package
     *      
     * @param mixed $tpl Optional
     */
    
    function install_from_package($tpl = null) 
    {
    	
    	$themes =& $this->getModel('themes');
    	
    	$pkg = v()->shell()->getVar('package');
    	
    	$result = $themes->doInstallPackage($pkg);
    	
    	if (VWP::isWarning($result)) {    		
    		$result->ethrow();
    	} else {
    		VWP::addNotice('Theme Installed!');
    	}
    	
    	$this->install($tpl);
    }

    /**
     * Install Theme from folder
     *      
     * @param mixed $tpl Optional
     */
    
    function install_from_folder($tpl = null) 
    {
    	$themes =& $this->getModel('themes');
    	
    	$folder = v()->shell()->getVar('folder');
    	
    	$result = $themes->doInstall($folder);

        if (VWP::isWarning($result)) {    		
    		$result->ethrow();
    	} else {
    		VWP::addNotice('Theme Installed!');
    	}
    	
    	$this->install($tpl);    	
    }

 
    /**
     * Close Theme
     *
     * @param mixed $tpl Optional
     * @access public
     */
 
    function close_theme($tpl = null) 
    {

        return $this->display($tpl); 
 
    }

    /**
     * Refresh Theme Page
     *
     * @param mixed $tpl Optional
     * @access public
     */
 
    function refresh_theme($tpl = null) 
    {
        return $this->manage($tpl);   
    }

    /**
     * Update Theme Settings
     *
     * @param mixed $tpl Optional
     * @access public
     */
 
    function update_theme_settings($tpl = null) 
    {
    	$shellob =& v()->shell();
    	
        $selected = $shellob->getChecked('ck');

        $data = $shellob->getVar('theme_info');

        if ((count($selected) < 1)||(!isset($data[$selected[0]]))) {
            VWP::raiseWarning('No theme selected!',get_class($this));
           return $this->display($tpl);
        }

        $themes =& $this->getModel('themes');
        
        $themeId = $data[$selected[0]]["id"];
        $themeType = $data[$selected[0]]["type"];    	
    	$params = $shellob->getVar('params');
    	    	
    	$result = $themes->updateParams($themeType,$themeId,$params);
    	
    	if (VWP::isWarning($result)) {
    		$result->ethrow();
    	} else {
    		VWP::addNotice('Updated theme settings!');
    	}
    	
        return $this->manage($tpl);
    }
    
    /**
     * Save Theme Targets
     */

    function save_theme_targets($tpl = null) 
    {

         $shellob =& VWP::getShell();
   
         $selected = $shellob->getChecked('ck');


         $data = $shellob->getVar('theme_info');

         if ((count($selected) < 1)||(!isset($data[$selected[0]]))) {
             VWP::raiseWarning('No theme selected!',get_class($this));
             return $this->display($tpl);
         }

         $themeId = $data[$selected[0]]["id"];
         $themeType = $data[$selected[0]]["type"];
         $target_list = $shellob->getVar('targets');

         $targets = $this->getModel('targets');
         $result = $targets->assignFrames($themeType,$themeId,$target_list);

         if (VWP::isWarning($result)) {
             $result->ethrow();
         } else {
             VWP::addNotice('Targets saved!');
         }

         $this->manage($tpl);     
    }
    
    // End ThemeMgr_Widget_ThemeMgr Class
} 