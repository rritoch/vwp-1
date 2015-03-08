<?php

/**
 * VWP - Tabs widget
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
 * VWP - Tabs widget
 * 
 * @package    VWP
 * @subpackage Widgets
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @link http://www.vnetpublishing.com VNetPublishing.Com 
 * @copyright (c) Ralph Ritoch - All rights reserved
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License 
 */

class VWP_Widget_Tabs extends VWidget 
{

	/**
	 * Display tabs header
	 * 
	 * @param mixed $tpl Optional
	 * @access public
	 */
	
    function display($tpl = null) 
    {
        $shellob =& VWP::getShell();
  
        // Initialize tabs

        $route =& VRoute::getInstance();
        
        $tab_urls = array(
            'configure'=>"index.php?app=vwp&widget=configure",
            'eventmgr'=>"index.php?app=vwp&widget=eventmgr", 
            'dbiconfig'=>"index.php?app=vwp&widget=dbiconfig",
            'appmgr'=>"index.php?app=vwp&widget=appmgr",
            'install'=>"index.php?app=vwp&widget=install"
         );
         
        foreach($tab_urls as $key=>$val) 
        {
            $tab_urls[$key] = $route->encode($val);	
        }
        
        
        $current_widget = $shellob->getVar('current_widget');
        $this->assignRef('current_widget',$current_widget);
        
        $this->assignRef('tab_urls',$tab_urls);
        // Display tabs
        
        parent::display($tpl);
    }

	/**
	 * Display tabs footer
	 * 
	 * @param mixed $tpl Optional
	 * @access public
	 */
        
    function foot($tpl = null) 
    {
        $shellob =& VWP::getShell();
  
        // Initialize tabs footer        

        $this->setLayout('footer');

        // Display footer
        
        parent::display($tpl);  
    }
 
    // end class VWP_Widget_Tabs
} 
