<?php

/**
 * Virtual Web Platform - Menu Application Link
 *  
 * This file provides the menu application link interface        
 * 
 * @package VWP
 * @subpackage Libraries.UI.MenuItem  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License
 */

/**
 * Virtual Web Platform - Menu Application Link
 *  
 * This class provides the menu application link interface        
 * 
 * @package VWP
 * @subpackage Libraries.UI.MenuItem  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License
 */

class VMenuApplink extends VMenuItem 
{

    /**
     * Item type
     * 
     * @var string $_link Item type
     * @access private   
     */
             
    public $_type = "applink";
 
    /**
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
       
    public $url = null;

    /**
     * Widget
     * 
     * @var string $widget Widget
     * @access public
     */
       
    public $widget = null;
 
    /** 
     * Action
     * 
     * @var string $action Action
     * @access public
     */
     
    public $action = null;
 
    /**
     * Icon
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
     * @var string $disabled_icon URI of hover icon
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
     * Link text
     * 
     * @var string $text Link text
     * @access public
     */ 
 
    public $text = '';
 
    /**
     * Reference
     * 
     * @var string $ref Refrence
     * @access public
     */
  
    public $ref = null;
    
    /**
     * Get Application Link Properties
     * 
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
     * @param DOMNode Node
     * @return DOMNode Node
     * @access public
     */
    
    function _loadNode($node) 
    {
        $ret = parent::_loadNode($node);
        $this->title = $this->text;
        $parts = explode(".",$this->widget);
        $app = array_shift($parts);
        $this->url = "index.php";
  
        if (!empty($app)) {
            $this->url .= "?app=$app";    
            if (count($parts) > 0) {
                $widget = implode(".",$parts);
                $this->url .= "&widget=$widget";
                if (!empty($this->ref)) {
                    $this->url .= "&ref=".$this->ref;
                }
            }
        }
  
        return $ret;
    }
    
    // end class VMenuApplink   
} 
