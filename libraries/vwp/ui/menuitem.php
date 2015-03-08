<?php

/**
 * Virtual Web Platform - Menu Item
 *  
 * This file provides the menu item interface        
 * 
 * @package VWP
 * @subpackage Libraries.UI  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License
 */

/**
 * Require Application Link Menu Item support
 */

VWP::RequireLibrary('vwp.ui.menuitem.applink');

/**
 * Require Link Menu Item support
 */

VWP::RequireLibrary('vwp.ui.menuitem.link');

/**
 * Require Spacer Menu Item support
 */

VWP::RequireLibrary('vwp.ui.menuitem.spacer');

/**
 * Require Category Menu Item support
 */

VWP::RequireLibrary('vwp.ui.menuitem.category');


/**
 * Virtual Web Platform - Menu Item
 *  
 * This class provides the menu item interface        
 * 
 * @package VWP
 * @subpackage Libraries.UI  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License
 */

class VMenuItem extends VObject 
{

    /**
     * Child items
     * 
     * @var array $_items
     * @access public
     */
     
    public $_items;
    
    /**
     * Item type
     * 
     * @var string $_type Item type
     * @access public
     */
         
    public $_type = null;
 
    /**
     * NoSave Flag
     * 
     * @var boolean $_nosave Flag to block item from being saved
     * @access public
     */
   
    public $_nosave = false;

    /**
     * Visible Flag
     * 
     * @var boolean $visible Flag to block item from being displayed
     * @access public
     */
  
    public $visible = true;

    /**
     * Item Title
     * 
     * @var string $title Item title
     * @access public
     */
  
    public $title = '';
      
    /**
     * Get item properties
     * 
     * @param boolean $public Return only public properties
     * @return array Item properties
     * @access public
     */          
  
    function getProperties($public = true) 
    {
        $prop = parent::getProperties($public);
        if (isset($this->_items) && is_array($this->_items)) {
            $prop["_items"] = array();
            foreach($this->_items as $item) {
                array_push($prop["_items"],$item->getProperties());
            }
        }
        $prop["type"] = $this->_type;
        return $prop;  
    }
 
    /**
     * Function _saveNode
     * 
     * @param object DomDocument
     * @return object DomNode
     * @access public      
     */
  
    function _saveNode($doc,$node,$depth = 1) 
    {
        $p = $this->getProperties();  
        $type = $p["type"];
        unset($p["type"]);
        $node->setAttribute('type',$type);
        foreach($p as $key=>$val) {
            if (is_string($val)) {
                $i = $doc->createElement($key,XMLDocument::xmlentities($val));
                $node->appendChild($doc->createTextNode("\n".str_repeat(' ',$depth)));
                $node->appendChild($i);
            }         
        }
        if (isset($this->_items) && is_array($this->_items)) {
            // create items node
            $inode = $doc->createElement('items');        
            foreach($this->_items as $item) {
                if (!$item->_nosave) {
                    $i = $doc->createElement('item');    
                    $i = $item->_saveNode($doc,$i,$depth + 2);
                    $i->appendChild($doc->createTextNode("\n".str_repeat(' ',$depth + 1)));
                    $inode->appendChild($doc->createTextNode("\n".str_repeat(' ',$depth + 1)));    
                    $inode->appendChild($i);
                }
            }
            $inode->appendChild($doc->createTextNode("\n".str_repeat(' ',$depth)));
            $node->appendChild($doc->createTextNode("\n".str_repeat(' ',$depth)));
            $node->appendChild($inode);  
        }
        return $node;
    }
    
    /**
     * Load node from document
     * 
     * @param object $node Document node  
     * @access private
     */
         
    function _loadNode($node) 
    {
    
        for($p = 0; $p < $node->childNodes->length;$p++) {
            $nodeName = $node->childNodes->item($p)->nodeName;
            $nodeValue = $node->childNodes->item($p)->nodeValue;
            if ($nodeName == "items") {
                $this->_items = array();
                $items = $node->childNodes->item($p)->getElementsByTagName('item');
                for($i=0;$i < $items->length; $i++) {
                    $item = $items->item($i);
                    $type = $item->getAttribute("type");
                    if (empty($type)) {
                        $elem = new VMenuItem;       
                        $elem->_loadNode($item);
                        array_push($this->_items,$elem);
                    } else {
                        if (strtolower($type) == "menu") {
                            $className = "VMenu";
                        } else {
                            $className = "VMenu" . ucfirst($type);
                        }
                        if (class_exists($className)) {
                            $elem = new $className;        
                            $elem->_loadNode($item);
                            array_push($this->_items,$elem);
                        } else {
                            $elem = new VMenuItem;       
                            $elem->_loadNode($item);
                            array_push($this->_items,$elem);        
                        }      
                    }  
                }
            } else {
                if ($nodeName !== "#text") {
                    $this->$nodeName = $nodeValue;
                }
            }
        }
        return true;  
    }
 
    // end class VMenuItem  
} 
