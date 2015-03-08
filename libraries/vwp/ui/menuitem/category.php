<?php

/**
 * Virtual Web Platform - Menu Category
 *  
 * This file provides the menu category interface        
 * 
 * @package VWP
 * @subpackage Libraries.UI.MenuItem  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License
 */

/**
 * Virtual Web Platform - Menu Category
 *  
 * This class provides the menu category interface        
 * 
 * @package VWP
 * @subpackage Libraries.UI.MenuItem  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License
 */

class VMenuCategory extends VMenuItem 
{

    /**
     * Item Type
     * 
     * @var string $_type Item type
     * @access public
     */
       
    public $_type = "category";
 
    /**
     * Category Items
     * 
     * @var array $_items Items
     * @access public
     */
       
    public $_items = array();
 
    /**
     * Reserved for admin menus
     * 
     * @var mixed $_source Source
     * @access public
     */
           
    public $_source = null; // used for admin menus
 
    /**
     * Disabled flag
     * 
     * @var boolean $disabled Disabled category
     * @access public
     */
     
    public $disabled = false;
 
    /**
     * Expand action
     * 
     * @var string $expand_action Action on category expansion
     * @access public
     */
     
    public $expand_action = null;

    /**
     * Collapse Action
     * 
     * @var string $expand_action Action on category collapse
     * @access public
     */
   
    public $collapse_action = null;

    /**
     * Collapsed Icon
     * 
     * @var string $collapsed_icon URI of collapsed icon
     * @access public
     */
       
    public $collapsed_icon = null;

    /**
     * Disabled collapsed icon
     * 
     * @var string $collapsed_icon URI of collapsed+disabled icon
     * @access public
     */

    public $collapsed_disabled_icon = null;

    /**
     * Collapsed hover icon
     * 
     * @var string $collapsed_icon URI of collapsed+hover icon
     * @access public
     */

    public $collapsed_hover_icon = null;

    /**
     * Collapsed active icon
     * 
     * @var string $collapsed_icon URI of collapsed+active icon
     * @access public
     */

    public $collapsed_active_icon = null;

    /**
     * Expanded icon
     * 
     * @var string $expanded_icon URI of expaneded icon
     * @access public
     */
   
    public $expanded_icon = null;

    /**
     * Expanded disabled icon
     * 
     * @var string $expanded_disabled_icon URI of expanded+disabled icon
     * @access public
     */
  
    public $expanded_disabled_icon = null;

    /**
     * Expanded hover icon
     * 
     * @var string $expanded_hover_icon URI of expanded+hover icon
     * @access public
     */

    public $expanded_hover_icon = null;

    /**
     * Expanded active icon
     * 
     * @var string $expanded_hover_icon URI of expanded+active icon
     * @access public
     */

    public $expanded_active_icon = null;     

    // end class VMenuCategory
} 

