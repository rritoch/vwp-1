<?php

/**
 * Theme Management Tabs
 *  
 * This file provides the user management menu for the
 * User application!  
 *  
 * @package    VWP.ThemeMgr
 * @subpackage Widgets
 * @author Ralph Ritoch <rritoch@gmail.com> 
 * @copyright (c) Ralph Ritoch - All rights reserved
 * @link http://www.vnetpublishing.com 
 */

// No direct access

class_exists( 'VWP' ) or die( 'Restricted access' );

VWP::RequireLibrary('vwp.ui.widget');

/**
 * User Management Menu
 *  
 * This class provides the user management menu for the
 * User application!  
 *  
 * @package    VWP.ThemeMgr
 * @subpackage Widgets
 * @author Ralph Ritoch <rritoch@gmail.com> 
 * @copyright (c) Ralph Ritoch - All rights reserved
 * @link http://www.vnetpublishing.com 
 */
 
class ThemeMgr_Widget_Tabs extends VWidget 
{

	/**
	 * Display Tab footer
	 * 
	 * @param unknown_type $tpl
	 * @access public
	 */
	
    function foot($tpl = null) 
    {    
        $this->setLayout('footer');  
        parent::display($tpl);  
    }
 
    /**
     * Display tab header
     * 
     * @param mixed $tpl Optional
     * @access public
     */
    
    function display($tpl = null) 
    {
 	 	
        $shellob =& VWP::getShell();
        $current_widget = $shellob->getVar('current_widget');
        $this->assignRef('current_widget',$current_widget);
  
        $tab_urls = array();
        
 	    $tab_urls['thememgr'] = "index.php?app=thememgr&widget=thememgr";
        $tab_urls['framemgr'] = "index.php?app=thememgr&widget=framemgr";
     
        $route =& VRoute::getInstance();
        
        foreach($tab_urls as $key=>$val) {
        	$tab_urls[$key] = $route->encode($tab_urls[$key]);
        }
     
        $this->assignRef('tab_urls',$tab_urls);
    
        parent::display($tpl);
    }
    
    // end class ThemeMgr_Widget_Tabs

}