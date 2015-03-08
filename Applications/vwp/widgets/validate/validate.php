<?php 

/**
 * VWP - Validate widget
 * 
 * The validate widget is responsible for providing
 * a link to configuration settings when 
 * no session handler is available. Otherwise
 * the widget response is blank.
 * 
 * @package    VWP
 * @subpackage Widgets
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @link http://www.vnetpublishing.com VNetPublishing.Com 
 * @copyright (c) Ralph Ritoch - All rights reserved
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License   
 */

/**
 * Require Route Support
 */

VWP::RequireLibrary('vwp.ui.route');

/**
 * VWP - Validate widget
 * 
 * The validate widget is responsible for providing
 * a link to configuration settings when 
 * no session handler is available. Otherwise
 * the widget response is blank.
 * 
 * @package    VWP
 * @subpackage Widgets
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @link http://www.vnetpublishing.com VNetPublishing.Com 
 * @copyright (c) Ralph Ritoch - All rights reserved
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License   
 */

class VWP_Widget_Validate extends VWidget 
{
	
	/**
	 * Display validation widget
	 * 
	 * @param mixed $tpl Optional
	 * @access public
	 */
	
	function display($tpl = null) {

		$sess =& VSession::getInstance();
		
		// Validate session
		
		$validated = $sess->isLive();		
				
		$configUrl = 'index.php?app=vwp';
		$configUrl = VRoute::getInstance()->encode($configUrl);

		$this->assignRef('configUrl',$configUrl);
		$this->assignRef('validated',$validated);
		
		// Display widget
		
		parent::display($tpl);
	}
	
    // End class VWP_Widget_Validate	
}
