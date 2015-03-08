<?php

/**
 * VWP - Event manager Widget 
 *  
 * @package    VWP
 * @subpackage Widgets
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @link http://www.vnetpublishing.com VNetPublishing.Com 
 * @copyright (c) Ralph Ritoch - All rights reserved
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License 
 */

VWP::RequireLibrary("vwp.ui.widget");

/**
 * VWP - Event manager Widget 
 *  
 * @package    VWP
 * @subpackage Widgets
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @link http://www.vnetpublishing.com VNetPublishing.Com 
 * @copyright (c) Ralph Ritoch - All rights reserved
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License 
 */

class VWP_Widget_EventMgr extends VWidget 
{
    
    /**
     * Display Event List
     * 
     * @access public    
     */
     
    function display($tpl = null) 
    {
  
 	    $shellob =& VWP::getShell();
  
 	    // Initialize tabs
 	    
        $current_widget = 'eventmgr';
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
 	
        // Initialize event list
        
        $events = $this->getModel("events");  
        $event_list = $events->getAll();
        $this->assignRef('event_list',$event_list);

        // Display widget
        
        $screen = $shellob->getScreen();
        $this->assignRef('screen',$screen);        
        parent::display();
    }
	
    /**     
     * Enable events
     * 
     * @param mixed $tpl Optional
     * @access public
     */
	
    function enable_events($tpl = null) 
    {
 
        $shellob =& VWP::getShell();
  
        // Enable events
        
        $selected = $shellob->getChecked('ck');
    
        if (count($selected) > 0) {
            $events = $this->getModel('events');
            if (VWP::isWarning($events)) {
                $events->ethrow();
            } else {
                $events->enable($selected);
                VWP::addNotice("Events enabled!");
            }  
        } else {
            VWP::raiseWarning("No Events Selected!",get_class($this));
        }
        
        // Display result
        
        $this->display();
    }

    /**     
     * Disable events
     * 
     * @param mixed $tpl Optional
     * @access public
     */
        
    function disable_events($tpl = null) 
    {
 
        $shellob =& VWP::getShell();
        
        // Disable events
        
        $selected = $shellob->getChecked('ck');

        if (count($selected) > 0) {
            $events = $this->getModel('events');
            if (VWP::isWarning($events)) {
                $events->ethrow();
            } else {
                $events->disable($selected);
                VWP::addNotice("Events disabled!");
            }  
        } else {
            VWP::raiseWarning("No Events Selected!",get_class($this));
        }

        // Display result
        
        $this->display();
    }

    /**     
     * Move event
     * 
     * @param mixed $tpl Optional
     * @access public
     */    

    function move_event($tpl = null) 
    {
        $shellob =& VWP::getShell();
  
        // Move event
        
        $evt = $shellob->getVar('eid');
        $dir = $shellob->getVar('arg1');
        if (empty($evt) || empty($dir)) {
            VWP::raiseWarning("Nothing to do?",get_class($this));   
        } else {
            $events = $this->getModel('events');
            if (VWP::isWarning($events)) {
                $events->ethrow();
            } else {
                $events->move($evt,$dir);
                VWP::addNotice("Event moved!");
            }    
        }
        
        // Display result
        
        $this->display();
    }
 
    /**     
     * Save event order
     * 
     * @param mixed $tpl Optional
     * @access public
     */
        
    function save_order($tpl = null) 
    {
        $shellob =& VWP::getShell();
  
        // Save event order
        
        $order = $shellob->getVar('_order');
        if (empty($order)) {
            VWP::raiseWarning("Nothing to do!",get_class($this));
        } else {
            $events = $this->getModel('events');
            $events->setOrdering($order);
            VWP::addNotice("Events sorted!");
        }
        
        // Display result
        
        $this->display();
    }
     
    // end class VWP_Widget_EventMgr
} 