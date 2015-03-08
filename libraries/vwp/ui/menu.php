<?php

/**
 * Virtual Web Platform - Menu support
 *  
 * This file provides the menu system interface        
 * 
 * @todo Implement multi-tier menu support
 * @todo Implement live widget connected menus
 * @todo Make menus DOM 3 compliant
 * @package VWP
 * @subpackage Libraries.UI  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License
 */

/**
 * Require XML Support
 */

VWP::RequireLibrary('vwp.documents.xml');

/**
 * Require Filesystem Support
 */

VWP::RequireLibrary('vwp.filesystem');

/**
 * Require Menu Item Support
 */

VWP::RequireLibrary('vwp.ui.menuitem');


/**
 * Virtual Web Platform - Menu 
 *  
 * This class provides the menu interface        
 * 
 * @package VWP
 * @subpackage Libraries.UI
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License
 */

class VMenu extends VMenuItem 
{

    /**
     * Menu Cache
     * 
     * @var array $_menus Menu Cache
     * @access private   
     */
    
    static $_menus = array();
 
    /**
     * Item type
     * 
     * @var string $_type Item type    
     * @access public
     */
       
    public $_type = "menu";
 
    /**
     * Application ID
     *   
     * @var $_app Application ID
     * @access public
     */
       
    public $_app = "vwp";
 
    /**
     * Menu ID
     * 
     * @var string $_id Menu ID
     * @access public
     */
     
    public $_id = null;
 
    /**
     * Item list
     * 
     * @var array $_items Item list
     * @access public
     */
       
    public $_items = array();
 
    /**
     * Menu Title 
     * @var string $title Menu title
     */
     
    public $title = '';
 
    /**
     * Menu location
     * 
     * @var string $location Default menu location
     * @access public
     */
     
    public $location = null;
 
    /**
     * Disabled
     * 
     * @var boolean $disabled Menu disabled
     * @access public
     */   
 
    public $disabled = false;
 
    /**
     * Default security policy
     * 
     * @var string Default security policy
     * @access public
     */
    
    public $default_security_policy = 'deny';
 
    /**
     * New menu cache
     * 
     * @var array $_new_menus
     * @access private
     */
    
    static $_new_menus;
 
    /**
     * Get menu
     * 
     * @todo Implement static get live menu method
     * @param object $widget Widget
     * @access private
     */
         
    public static function getMenu($widget) 
    {
 
    }
 
    /**
     * Delete Menu Item
     *   
     * <pre>
     * The MF_BYCOMMAND flag is the default flag if neither the MF_BYCOMMAND nor MF_BYPOSITION flag is specified.
     *     
     * MF_BYCOMMAND 0x00000000L Indicates that uPosition gives the identifier of the menu item. 
     * MF_BYPOSITION 0x00000400L  
     * </pre>
     * 
     * @todo Implement static delete live menu method
     * @param object $hMenu Menu
     * @param integer $uPosition Item position
     * @param $uFlags Flags
     * @access private
     */
           
    public static function deleteMenuItem($hMenu, $uPosition, $uFlags) 
    {
   
    }

    /**
     * Get a sub menu
     * Reserved for future use
     * 
     * @todo implement static get Sub Menu
     * @param object $hMenu Menu
     * @param integer $uPos Position
     * @access private
     */
      
    public static function getSubMenu($hmenu, $uPos) 
    {
 
    }
 
    /**
     * Assigns a new menu to the specified widget
     * Reserved for future use   
     * @param object $widget Widget
     * @param object $hMenu Menu
     */
       
    static function setMenu($widget, $hMenu) 
    {
 
    }

    /**
     * Remove a live menu
     * 
     * @todo Implement static destroy live menu 
     * @access private
     */
 
    public static function destroyMenu($hmenu) 
    {
 
    }
 
    /**
     * Create a new menu 
     * 
     * @return object Menu
     * @access public
     */
        
    public static function &createMenu() 
    {
        $ptr = count(self::$_new_menus);
        self::$_new_menus[] = new VMenu;
        return self::$_new_menus[$ptr]; 
    }

    /**
     * Insert a menu item
     * 
     * @todo Implement insert live menu item method
     * @access private
     */
 
    static function insertMenuItem($hmenu,$before = null,$position_item,$menuItemInfo) 
    {
     
    }
 
    /**
     * Insert item 
     * 
     * @param array|object $item Item
     * @param integer $before Before ID
     * @access public      
     */
  
    function insertItem($item,$before = null) 
    {
        if (is_array($item)) {
            $info = $item;
            $type = $item["type"];
            unset($item["type"]);
            if ($type == "menu") {
                $className = 'VMenu';
            } else {
                $className = 'VMenu'.ucfirst($type);
            }
   
            if (!class_exists($className)) {
                return VWP::raiseWarning('Invalid item type!',get_class($this).'::insertItem',null,false);   
            }
   
            $item = new $className;
            $item->setProperties($info);   
        } elseif (!is_object($item)) {
            return VWP::raiseWarning('Invalid item type!',get_class($this).'::insertItem',null,false);        	
        } else {
        	
        	$className = get_class($item);
        	
        	if (!method_exists($item,'is')) {
        	    return VWP::raiseWarning("Invalid item type '$className'!",get_class($this).'::insertItem',null,false);	
        	}
        	
        	if (!$item->is('VMenuItem')) {
        		return VWP::raiseWarning("Invalid item type '$className'!",get_class($this).'::insertItem',null,false);
        	}
        }
  
        if (is_null($before)) {
            $before = count($this->_items);   
            array_push($this->_items,$item);   
        } else {
            $old_list = $this->_items;
            $done = false;
            $this->_items = array();
            for($p = 0; $p < count($old_list);$p++) {
                if ($p == $before) {
                    array_push($this->_items,$item);
                    $done = true;
                }
                array_push($this->_items,$old_list[$p]);
            }
            if (!$done) {
                $before = count($this->_items);
                array_push($this->_items,$item);
            }  
        }
        return $before;
    }  
 
    /**
     * Get instance of a menu
     * 
     * @param $menuID Menu ID
     * @param $app Application ID
     * @access public
     */
           
    static function &getInstance($menuID = null, $app = "vwp") 
    {
    
        if ($menuID == null) {
            $ptr = count(self::$_new_menus);
            self::$_new_menus[] = new VMenu;
            return self::$_new_menus[$ptr];
        }
  
        if (!isset(self::$_menus[$app])) {
            self::$_menus[$app] = array();
        }
  
        if (!isset(self::$_menus[$app][$menuID])) {
            if ($app == "vwp") {
                $filename = VWP::getVarPath('vwp').DS.'menus'.DS.$menuID.'.xml';
                $vfile =& v()->filesystem()->file();
                if (!$vfile->exists($filename)) {
                    $err1 = VWP::raiseWarning("Menu $menuID not found!","VMenu::getInstance",null,false);
                    return $err1;
                }
                $_menus[$app][$menuID] = new VMenu;
                $err2 = $_menus[$app][$menuID]->load($menuID);
                if (VWP::isWarning($err2)) {
                    return $err2;
                }
                $_menus[$app][$menuID]->_app = $app;
            } else {
                $err3 = VWP::raiseWarning("Menu $app:$menuID not found!","VMenu::getInstance",null,false);
                return $err3;    
            } 
        }
        return $_menus[$app][$menuID];  
    }
 
    /**
     * Load a menu from menu file
     * 
     * @param string $filename Menu filename
     * @access public
     */
           
    function load($menuID) 
    {
 
        $filename = VWP::getVarPath('vwp').DS.'menus'.DS.$menuID.'.xml';
     
        $vfile =& v()->filesystem()->file();
        $data = $vfile->read($filename);
        if (VWP::isWarning($data)) {
            return $data;
        }
     
        $doc = new DomDocument;
        VWP::noWarn();
        $lr = @ $doc->loadXML($data);
        VWP::noWarn(false);
        if (!$lr) {
            $err = VWP::getLastError();
            return VWP::raiseWarning($err[1],get_class($this).":load",null,false);   
        }
    
        $rootNode = $doc->documentElement;
        $this->_loadNode($rootNode);
        $this->_id = $menuID;
        return true;
    }
 
    /**
     * Save Menu
     * 
     * @return True on success, error or warning otherwise
     * @access public
     */
   
    function save() 
    {      
        $menuID = $this->_id;        
        $filename = VWP::getVarPath('vwp').DS.'menus'.DS.$menuID.'.xml';
  
        $doc = new DomDocument; 
  
        $src = '<' . '?xml version="1.0" encoding="utf-8" ?' .'>' . "\n"
                . '<menu></menu>';
        $doc->loadXML($src);   
        $this->_saveNode($doc,$doc->documentElement);
        $doc->documentElement->appendChild($doc->createTextNode("\n"));
        $data = $doc->saveXML();
        $vfile =& v()->filesystem()->file();
        return $vfile->write($filename,$data);  
    }
 
    /**
     * Get an item by index
     * 
     * @param integer $idx Index
     * @return VMenuItem|object Menu Item on success, error or warning otherwise
     * @access public   
     */
 
    function &getItem($idx) 
    {
        if ($idx >= count($this->_items)) {
            $err = VWP::raiseWarning("No more items!",get_class($this)."::getItem",ERROR_NO_MORE_ITEMS,false);
            return $err;
        }
        return $this->_items[$idx];  
    }

    /**
     * Delete Item
     * 
     * @param integer $idx Item index
     * @return boolean|object True on success, error or warning otherwise
     * @access public
     */
 
    function deleteItem($idx) 
    {
        if ($idx >= count($this->_items)) {
            return VWP::raiseWarning("Item not found!",get_class($this)."::delete_item",ERROR_NO_MORE_ITEMS,false);
        }
  
        $old_items = $this->_items;
        $this->_items = array();
        for($p = 0; $p < count($old_items);$p++) {
            if ($p != $idx) {
                array_push($this->_items,$old_items[$p]);
            }
        }
        return true;
    }

    /**
     * Replace menu item
     * 
     * @param integer $idx Index of old item
     * @param VMenuItem $item New Item
     * @access public
     */ 
 
    function replaceItem($idx,$item) 
    {
        $this->insertItem($item,$idx);
        $this->deleteItem($idx + 1);
    }  
 
    /**
     * Get a copy of the menu
     * 
     * @todo Implement get copy of live menu
     * @access private
     */
     
    function getCopy() 
    {
 
    }
    
    // end class VMenu
 
} 
