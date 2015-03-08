<?php

/**
 * VWP Application manager widget
 * 
 * @package    VWP
 * @subpackage Widgets
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @link http://www.vnetpublishing.com VNetPublishing.Com 
 * @copyright (c) Ralph Ritoch - All rights reserved
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License   
 */

VWP::RequireLibrary('vwp.ui.widget');

/**
 * VWP Application manager widget
 * 
 * @package    VWP
 * @subpackage Widgets
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @link http://www.vnetpublishing.com VNetPublishing.Com 
 * @copyright (c) Ralph Ritoch - All rights reserved
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License   
 */
  
class VWP_Widget_AppMgr extends VWidget 
{

    /**
     * Display Installed Applications List
     * 
     * @param mixed $tpl Optional
     */     
	
	function display($tpl = null) 
	{

        $shellob =& VWP::getShell();
  
        // Initialize tabs
        
        $current_widget = 'appmgr';  
        $shellob->setVar('current_widget',$current_widget);
 
        $menu = $this->getWidget("tabs");
    
        if (!VWP::isWarning($menu)) {
            $menu = $menu->build();   
        }
   
        $menu_foot = $this->getWidget("tabs");
    
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

        // Initalize application list
        
        $installedapps =& $this->getModel('install');

        if (VWP::isWarning($installedapps)) {
            $installedapps->ethrow();
            $application_list = array();
        } else {  
            $application_list = $installedapps->getApplications();
        }

        $this->assignRef('application_list',$application_list);
        
        // Display widget
        
        $screen = $shellob->getScreen();
        $this->assignRef('screen',$screen);        
        parent::display($tpl);
    }

    /**
     * Uninstall Application
     * 
     * @param mixed $tpl Optional
     */
    
     function uninstall_app($tpl = null) 
     {
     	// Initialize uninstall request
     	
        $shellob =& VWP::getShell();
        $selected = $shellob->getChecked('ck');
        $installedapps =& $this->getModel('install');

        // Process request
        
        if (count($selected) < 1) {
           VWP::raiseWarning('No applications selected!',get_class($this));
        } else {
        	// Uninstall selected applications
            $result = $installedapps->uninstall($selected);
            if (VWP::isWarning($result)) {
                $result->ethrow();
            } else {
                VWP::addNotice('Applications uninstalled!');
            }
        }

        // Display response
        
        $this->display($tpl);   
    }
    
    // end class VWP_Widget_AppMgr
} 
