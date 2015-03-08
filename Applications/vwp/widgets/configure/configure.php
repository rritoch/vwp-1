<?php

/**
 * VWP - Configuration widget
 * 
 * @package    VWP
 * @subpackage Widgets
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @link http://www.vnetpublishing.com VNetPublishing.Com 
 * @copyright (c) Ralph Ritoch - All rights reserved
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License   
 */

// No direct access

class_exists( 'VWP' ) or die( 'Restricted access' );

/**
 * Require widget support
 */

VWP::RequireLibrary('vwp.ui.widget');
 
/**
 * VWP - Configuration widget
 * 
 * @package    VWP
 * @subpackage Widgets
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @link http://www.vnetpublishing.com VNetPublishing.Com 
 * @copyright (c) Ralph Ritoch - All rights reserved
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License 
 */ 
 
class VWP_Widget_Configure extends VWidget 
{
	
	/**	 
	 * Display configuration form
	 * 
	 * @param mixed $tpl Optional
	 */    
    
    function display($tpl = null) 
    {      
    	$shellob =& VWP::getShell();
  
        // Initialize tabs
        
        $current_widget = 'configure';
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

        // Initialize configuration settings
        
        $configure = $this->getModel('configure');    
  
        if (VWP::isWarning($configure)) {
            $cfg = array();
            $configure->ethrow();
        } else {
            $cfg = $configure->getConfig();
        }  
        $this->assignRef('cfg',$cfg);
  
        // Display widget
        
        $screen = $shellob->getScreen();
        $this->assignRef('screen',$screen);   
        parent::display();
    }
	
	/**	 
	 * Save configuration
	 * 
	 * @param mixed $tpl Optional
	 */
	
    function save_config($tpl = null) 
    {

        $shellob =& VWP::getShell();
  
        // Initialize tabs
        
        $current_widget = 'configure';
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

        // Save configuration
        
        $configure = $this->getModel('configure');
        if (VWP::isWarning($configure)) {
            $cfg = array();
            $configure->ethrow();   
        } else {   
            $new_settings = $shellob->getVar("cfg");
            $result = $configure->saveConfig($new_settings);
   
            if (VWP::isWarning($result)) {
                $result->ethrow();
            } else {
                VWP::addNotice('Configuration saved!');
            }
   
            $cfg = $configure->getConfig();
        }
          
        $this->assignRef('cfg',$cfg);
  
        // Display result
        
        $screen = $shellob->getScreen();
        $this->assignRef('screen',$screen);   
        parent::display(); 
    }

    // End class VWP_Widget_Configure
}
