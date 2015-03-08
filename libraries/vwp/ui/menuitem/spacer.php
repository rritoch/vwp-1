<?php

/**
 * Virtual Web Platform - Menu Spacer
 *  
 * This file provides the menu spacer interface        
 * 
 * @package VWP
 * @subpackage Libraries.UI.MenuItem  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License
 */

/**
 * Virtual Web Platform - Menu Spacer
 *  
 * This class provides the menu spacer interface        
 * 
 * @package VWP
 * @subpackage Libraries.UI.MenuItem  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License
 */

class VMenuSpacer extends VMenuItem 
{

    /**
     * Item type
     * 
     * @var string $_type Item type
     * @access public
     */
           
    public $_type = "spacer";
    
    /**
     * Title
     * 
     * @var string $title Title
     */
    
    public $title = 'Spacer';
    
    // end class VMenuSpacer
} 