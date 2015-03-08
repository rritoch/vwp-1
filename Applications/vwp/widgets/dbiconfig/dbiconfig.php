<?php

/**
 * VWP Database Configuration Widget
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
 * VWP Database Configuration Widget
 * 
 * @package    VWP
 * @subpackage Widgets
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @link http://www.vnetpublishing.com VNetPublishing.Com 
 * @copyright (c) Ralph Ritoch - All rights reserved
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License   
 */

class VWP_Widget_DBIConfig extends VWidget 
{

	/**
	 * Display Database list
	 * 
	 * @param mixed $tpl Optional
	 * @access public
	 */
	
    function display($tpl = null) 
    {
    	
        $shellob =& VWP::getShell();

        // Initialize tabs
        
        $current_widget = 'dbiconfig';
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
 
        // Initialize database list
        
        $dbiconfig = $this->getModel('dbiconfig');
          
        $databases = array();
  
        if (VWP::isWarning($dbiconfig)) {
            $dbiconfig->ethrow();
            $databases = array();
        } else {
        	        	
            $databases = $dbiconfig->getDatabases();
                        
            if (VWP::isWarning($databases)) {            	
                $databases->ethrow();
                $databases = array();
            }
        }
       
        $this->assignRef('databases',$databases);
        
        // Display widget
        
        $screen = $shellob->getScreen();
        $this->assignRef('screen',$screen);
                
        parent::display();
    }
	
    /**
     * Reset form
     * 
     * @param mixed $tpl Optional
     * @access public
     */
    
    function reset($tpl = null) {
    	$shellob =& v()->shell();
    	
    	$reset_task = $shellob->getVar('reset_task');
    	
    	switch($reset_task) {
    		case "edit_db":
    			return $this->edit_db($tpl);
    			break;
    		case "create_db":
    			break;
    			
    		default:
    			VWP::raiseWarning('Ambiguous reset request!',get_class($this));
    			return $this->display($tpl);
    			break;    		
    	}
    	
        // Initialize tabs
        
        $current_widget = 'dbiconfig';
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

        // Initialize database settings
        
        $dbiconfig = $this->getModel('dbiconfig');
        $dbinfo = array();
        if (VWP::isWarning($dbiconfig)) {
            $dbiconfig->ethrow();
            $databases = array();
            $this->assignRef('databases',$databases);   
        } else {
            $dbid = '';
            $this->setLayout('editdb');
            $dbtypes = $dbiconfig->getDBTypes();
            if (VWP::isWarning($dbtypes)) {
                $dbtypes->ethrow();
                $dbtypes = array();
            }
            //$dbicfg = $shellob->getVar('dbicfg');      
            $dbinfo = array();
            $task = 'create_db';
            $this->assignRef('dbtypes',$dbtypes);
            $this->assignRef('dbid',$dbid);
            $this->assignRef('dbinfo',$dbinfo);
            $this->assignRef('task',$task);
        }
        
        // Display widget
        
        $screen = $shellob->getScreen();
        $this->assignRef('screen',$screen);    
        parent::display($tpl);     	
    	
    }
    
	/**	 
	 * Save Database Configuration
	 * 
	 * @param mixed $tpl Optional
	 * @access public
	 */
    
    function save_db($tpl = null) 
    {
 
        $shellob =& VWP::getShell();
  
        // Initialize tabs
        
        $current_widget = 'dbiconfig';
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
        
        $dbiconfig = $this->getModel('dbiconfig');
        $dbinfo = array();
        if (VWP::isWarning($dbiconfig)) {
            $dbiconfig->ethrow();
            $databases = array();
            $this->assignRef('databases',$databases);   
        } else {
        	
            $dbid = $shellob->getVar('dbid');
            
            if (empty($dbid)) {
            	$selected = $shellob->getChecked('ck');
            	if ((count($selected) > 0) && (!empty($selected[0]))) {
            	    $dbid = $selected[0];	
            	}
            }
            
            $this->setLayout('editdb');
   
            $dbtypes = $dbiconfig->getDBTypes();
            if (VWP::isWarning($dbtypes)) {
                $dbtypes->ethrow();
                $dbtypes = array();
            }
            $dbicfg = $shellob->getVar('dbicfg');   
            $newid = $dbiconfig->saveDatabase($dbicfg,$dbid);
            if (VWP::isWarning($newid)) {
                $newid->ethrow();
            } else {
            	VWP::addNotice('Database configuration saved!');
                $dbid = $newid;
            }   
            $dbinfo = $dbiconfig->getDatabaseInfo($dbid);
            $task = 'edit_db';
            $this->assignRef('dbtypes',$dbtypes);
            $this->assignRef('dbid',$dbid);
            $this->assignRef('dbinfo',$dbinfo);
            $this->assignRef('task',$task);
        }
        
        // Display result
        
        $screen = $shellob->getScreen();
        $this->assignRef('screen',$screen);        
        parent::display($tpl);  
    }

    /**     
     * Display create database form
     * 
     * @param mixed $tpl Optional
     * @access public
     */
    
    function create_db($tpl = null) 
    {
        $shellob =& VWP::getShell();
   
        // Initialize tabs
        
        $current_widget = 'dbiconfig';
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

        // Initialize database settings
        
        $dbiconfig = $this->getModel('dbiconfig');
        $dbinfo = array();
        if (VWP::isWarning($dbiconfig)) {
            $dbiconfig->ethrow();
            $databases = array();
            $this->assignRef('databases',$databases);   
        } else {
            $dbid = '';
            $this->setLayout('editdb');
            $dbtypes = $dbiconfig->getDBTypes();
            if (VWP::isWarning($dbtypes)) {
                $dbtypes->ethrow();
                $dbtypes = array();
            }
            $dbicfg = $shellob->getVar('dbicfg');      
            $dbinfo = array();
            $task = 'create_db';
            $this->assignRef('dbtypes',$dbtypes);
            $this->assignRef('dbid',$dbid);
            $this->assignRef('dbinfo',$dbinfo);
            $this->assignRef('task',$task);
        }
        
        // Display widget
        
        $screen = $shellob->getScreen();
        $this->assignRef('screen',$screen);    
        parent::display($tpl);  
    }

    /**     
     * Display edit database form
     * 
     * @param mixed $tpl Optional
     */
    
    function edit_db($tpl = null) 
    {
        $shellob =& v()->shell();
  
        // Initialize tabs
        
        $current_widget = 'dbiconfig';
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
 
        // Load configuration settings
        
        $dbiconfig = $this->getModel('dbiconfig');
        $dbinfo = array();
        if (VWP::isWarning($dbiconfig)) {
            $dbiconfig->ethrow();
            $databases = array();
            $this->assignRef('databases',$databases);   
        } else {
        	        	        
            $dbid = $shellob->getVar('dbid');
            
            if (empty($dbid)) {
                $selected = $shellob->getChecked('ck');
                if (count($selected) > 0) {
                    $dbid = $selected[0];	
                }                	
            }
            
            if (empty($dbid)) {
                VWP::addNotice('No database selected!');
                return $this->display($tpl);                	
            } else {
                $this->setLayout('editdb');
                $dbtypes = $dbiconfig->getDBTypes();
                if (VWP::isWarning($dbtypes)) {
                    $dbtypes->ethrow();
                    $dbtypes = array();
                }
                $dbinfo = $dbiconfig->getDatabaseInfo($dbid);
                $task = 'edit_db';
                $this->assignRef('dbtypes',$dbtypes);
                $this->assignRef('dbid',$dbid);
                $this->assignRef('dbinfo',$dbinfo);
                $this->assignRef('task',$task);
            }
        }
        
        // Display widget
        
        $screen = $shellob->getScreen();
        $this->assignRef('screen',$screen);  
        parent::display($tpl);
    }
 
    // End class VWP_Widget_DBIConfig
}
