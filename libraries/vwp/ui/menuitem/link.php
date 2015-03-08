<?php

/**
 * Virtual Web Platform - Menu Link
 *  
 * This file provides the menu link interface        
 * 
 * @package VWP
 * @subpackage Libraries.UI.MenuItem  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License
 */

/**
 * Virtual Web Platform - Menu Link
 *  
 * This class provides the menu link interface        
 * 
 * @package VWP
 * @subpackage Libraries.UI.MenuItem  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License
 */

class VMenuLink extends VMenuItem 
{

    /**
     * Item type
     * 
     * @var string $_link Item type
     * @access public   
     */
             
    public $_type = "link";
 
    /**
     * Disabled Flag
     * 
     * @var boolean $disabled Link disabled
     * @access public
     */     
 
    public $disabled = false;

    /**
     * Link URL
     * 
     * @var string $url Link URL
     * @access public
     */
       
    public $url = "#";
 
    /**
     * Action 
     *  
     * @var string $action Action
     */
     
    public $action = null;
 
    /**
     * Icon URI
     * 
     * @var string $icon URI of icon
     * @access public
     */
     
    public $icon = null;
 
    /**
     * Disabled Icon
     * 
     * @var string $disabled_icon URI of disabled icon
     * @access public
     */
   
    public $disabled_icon = null;

    /**
     * Hover Icon
     * 
     * @var string $hover_icon URI of hover icon
     * @access public
     */ 
 
    public $hover_icon = null;
 
    /**
     * Active Icon
     * 
     * @var string $active_icon URI of active icon
     * @access public
     */
    
    public $active_icon = null;

    /**
     * Link Text
     * 
     * @var string $text Link text
     * @access public
     */ 
 
    public $text = '';
 
    /**
     * Get Properties
     * 
     * @param boolean $public Public only
     * @return array Properties
     * @access public
     */
    
    function getProperties($public = true) 
    {
        $this->title = $this->text;
        $p = parent::getProperties($public);
        return $p;    
    }
 
    /**
     * Load Node
     * 
     * @return DOMNode Link Node
     * @access public
     */
    function _loadNode($node) 
    {
        $ret = parent::_loadNode($node);
        $this->title = $this->text;
        return $ret;
    }
    
    // end class VMenuLink   
} 

