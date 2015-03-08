<?php

/**
 * Virtual Web Platform - UI Frames
 *  
 * This file provides frame support.   
 * Frames are containers which can hold multiple widgets.
 *  
 * @package VWP
 * @subpackage Libraries.UI
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

/**
 * Require Frame Item Support
 */

VWP::RequireLibrary('vwp.ui.frameitem');

/**
 * Require XML Support
 */

VWP::RequireLibrary('vwp.documents.xml');

/**
 * Require Filesystem Support
 */

VWP::RequireLibrary('vwp.filesystem');


/**
 * Virtual Web Platform - UI Frames
 *  
 * This class provides frame support.
 * Frames are containers which can hold multiple widgets.   
 * 
 * @package VWP
 * @subpackage Libraries.UI
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */



class VUIFrame extends VObject 
{

    /**
     * Child items
     * 
     * @var array $_items Child items
     * @access private
     */
     
    protected $_items = array();
 
    /**
     * Frame ID
     * 
     * @var string $title Item title
     * @access public
     */
  
    public $id = null;

    /**
     * Frame Cache
     * 
     * @var array $_frames Frame Cache
     * @access private   
     */
    
    static $_frames = array();
 
    /**
     * New Frames Cache
     * 
     * @var array $_new_frames New Frame Cache
     * @access private   
     */ 
  
    static $_new_frames = array();
 
    /**
     * Frame Disabled Flag
     * 
     * @var boolean $disabled Frame disabled
     * @access public
     */   
 
    public $disabled = false;

    /**
     * Frame Visible Flag
     * 
     * @var boolean $visible Frame visible
     * @access public
     */   
 
    public $visible = true;
 
    /**
     * Frame Id
     * 
     * @var string $_id Frame Id
     * @access private
     */
     
    public $_id = null;
     
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
        return $prop;  
    }
   
    /**
     * Save Node
     * 
     * @param DOMDocument $doc
     * @param DOMNode $node
     * @param integer $depth Node depth
     * @access private
     */
    
    protected function _saveNode($doc,$node,$depth = 1) 
    {
  
        $p = $this->getProperties();  
  
        foreach($p as $key=>$val) {
            if (is_string($val)) {
                $i = $doc->createElement($key,XMLDocument::xmlentities($val));
                $node->appendChild($doc->createTextNode("\n".str_repeat(' ',$depth)));
                $node->appendChild($i);
            }         
        }
  
        if (isset($this->_items) && is_array($this->_items)) 
        {
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
         
    protected function _loadNode($node) 
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
                    $elem = new VUIFrameItem;       
                    $elem->_loadNode($item);
                    array_push($this->_items,$elem); 
                }
            } else {
                if ($nodeName !== "#text") {
                    $this->$nodeName = $nodeValue;
                }
            }
        }
        return true;  
    }
    
    /**
     * Create a new frame
     * 
     * @return VUIFrame Frame
     * @access public
     */
        
    public static function &createFrame() 
    {    
        $ptr = count(self::$_new_frames);
        self::$_new_frames[] = new VUIFrame;
        return self::$_new_frames[$ptr]; 
    }
 
    /**
     * Insert item 
     * 
     * @param array|object $item Item
     * @param integer $before Before ID
     * @return integer item index        
     */
  
    function insertItem($item,$before = null) 
    {
        if (is_array($item)) {
            $info = $item;
            $item = new VUIFrameItem;
            $item->setProperties($info);   
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
     * List all frames
     * 
     * @param boolean $onlyloaded Only list loaded frames
     * @access public
     */
  
    static function getFrameList($onlyloaded = false) 
    {
        if ($onlyloaded) {
            return array_keys(self::$_frames);
        }
    
        $basePath =  VWP::getVarPath('vwp').DS.'frames';
  
        $vfolder =& v()->filesystem()->folder();
  
        $files = $vfolder->files($basePath);  
        $frames = array();
        if (!VWP::isWarning($files)) {
            foreach($files as $file) {
                if (substr($file,strlen($file) - 4) == '.xml') {
                    $frameId = substr($file,0,strlen($file) - 4);
                    array_push($frames,$frameId);
                }
            }
        }
        return $frames;  
    }
    
    /**
     * Get instance of a frame
     * 
     * @param $frameID Frame ID
     * @return VUIFrame Frame
     */
           
    static function &getInstance($frameID = null) 
    {
    
        if ($frameID == null) {
            $ptr = count(self::$_new_frames);
            self::$_new_frames[] = new VUIFrame;
            return self::$_new_frames[$ptr];
        }
  
        if (!is_string($frameID)) {
            $err = VWP::raiseWarning('Invalid Frame Id:'.var_export($frameID,true),'VUIFrame::getInstance()',false);
            return $err;
        }  

        if (!isset(self::$_frames[$frameID])) {
   
            $filename = VWP::getVarPath('vwp').DS.'frames'.DS.$frameID.'.xml';
            $vfile =& v()->filesystem()->file();
            if (!$vfile->exists($filename)) {
                $err1 = VWP::raiseWarning("Frame $frameID not found!","VUIFrame::getInstance",null,false);
                return $err1;
            }
            self::$_frames[$frameID] = new VUIFrame;
            $err2 = self::$_frames[$frameID]->load($filename);
            self::$_frames[$frameID]->_id = $frameID;
            if (VWP::isWarning($err2)) {
                return $err2;
            }   
        }
  
        return self::$_frames[$frameID];  
    }
 
    /**
     * Load a menu from menu file
     * 
     * @param string $filename Menu filename
     * @return boolean|object True on success, error or warning otherwise
     * @access public
     */
           
    function load($filename) 
    {   
        $vfile =& v()->filesystem()->file();
  
        if (empty($filename)) {
            $filename = VWP::getVarPath('vwp').DS.'frames'.DS.$this->_id.'.xml';
        }
  
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
        return true;
    }
 
    /**
     * Save Frame to file
     * 
     * @param string $filename Filename
     * @access public
     */
   
    function save($filename = null) 
    {
 
        if (empty($filename)) {
            $filename = VWP::getVarPath('vwp').DS.'frames'.DS. $this->_id .'.xml';  
        }
  
        $doc = new DomDocument; 
  
        $src = '<' . '?xml version="1.0" encoding="utf-8" ?' .'>' . "\n"
             . '<frame></frame>';
        $doc->loadXML($src);   
        $this->_saveNode($doc,$doc->documentElement);
        $doc->documentElement->appendChild($doc->createTextNode("\n"));
        $data = $doc->saveXML();
        $vfile =& v()->filesystem()->file();
        return $vfile->write($filename,$data);  
    }

    /**
     * Delete Frame
     * 
     * @return boolean|object True on success, error or warning otherwise
     * @access public
     */
   
    function delete() 
    {  
        $filename = VWP::getVarPath('vwp').DS.'frames'.DS. $this->_id .'.xml';  
        $vfile =& v()->filesystem()->file();
        return $vfile->delete($filename);
    }

 
    /**
     * Get an item by index
     *
     * @param integer $idx Index
     * @return VUIFrameItem|object Frame on success, error or warning otherwise
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
     * Replace item
     * 
     * @param integer $idx Item index
     * @param VUIFrameItem New item
     * @access public
     */ 
 
    function replaceItem($idx,$item) 
    {
        $this->insertItem($item,$idx);
        $this->deleteItem($idx + 1);
    }  
 
 
    // end class VUIFrame
}