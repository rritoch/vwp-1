<?php

/**
 * Virtual Web Platform - UI Frame Item
 *  
 * This file provides theme frame item support   
 * 
 * @package VWP
 * @subpackage Libraries.UI  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

/**
 * Virtual Web Platform - UI Frame item
 *  
 * This class provides theme frame item support   
 * 
 * @package VWP
 * @subpackage Libraries.UI  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

class VUIFrameItem extends VObject 
{
  
    /**
     * NoSave Flag
     * 
     * @var boolean $_nosave Flag to block item from being saved
     * @access private
     */
   
    public $_nosave = false;

    /**
     * Visible flag
     * 
     * @var boolean $visible Flag to block item from being displayed
     * @access public
     */
  
    public $visible = true;

    /**
     * Frame Title
     * @var string $title Item title
     * @access public
     */
  
    public $title = '';

    /**
     * Disabled Flag
     * 
     * @var boolean $disabled Link disabled
     * @access public
     */     
 
    public $disabled = false;

    /**
     * Default security policy
     * 
     * @var string Default security policy
     * @access public
     */
    
    public $default_security_policy = 'allow';
        
    /**
     * Link URL
     * 
     * Reserved for future use
     * 
     * @var string $url Link URL
     * @access public
     */
       
    public $url = null;

    /**
     * Widget ID
     * 
     * @var string $widget Widget
     * @access public
     */
       
    public $widget = null;
 
    /**
     * Action
     * 
     * Reserved for future use
     * 
     * @var string $action Action
     * @access public
     */
     
    public $action = null;
 
    /**
     * Widget Reference
     * 
     * @var string $ref Refrence
     * @access public
     */
  
    public $ref = null;

    /**
     * Allow Access
     * 
     * @param string $frame_id Frame ID
     * @param string $id Item ID
     * @return boolean True if access allowed, false otherwise
     * @access public 
     */
    
    function allowAccess($frame_id = 'any',$id = '-1') 
    {
        if ($this->disabled) {
        	return false;
        }

        $mode = $this->default_security_policy == 'deny' ? 'allow' : 'deny';
        
        $R_Id = 'index.php?app=vwp';
        $R_Id = VRoute::getInstance()->encode($R_Id);
        
        $S_Id = array(
            "mode"=>$mode,
            "widget"=>$this->widget,
            "frame"=>$frame_id,
            "item"=>$id,            
        );
        
        $user =& VUser::getCurrent();
        
        if ($mode == 'deny') {
        	return !$user->deny('Control access to frame item',$R_Id,$S_Id);
        }
                
        return $user->allow('Control access to frame item',$R_Id,$S_Id);
    }
    
    /**
     * Function _saveNode
     * 
     * @param DOMDocument DomDocument
     * @param DOMNode Node
     * @param integer $depth Node Depth
     * @return DOMNode DomNode
     * @access public      
     */
  
    function _saveNode($doc,$node,$depth = 1) 
    {
        $p = $this->getProperties();  
        foreach($p as $key=>$val) {
            if (is_string($val)) {
                $i = $doc->createElement($key,XMLDocument::xmlentities($val));
                $node->appendChild($doc->createTextNode("\n".str_repeat(' ',$depth)));
                $node->appendChild($i);
            }         
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
            if ($nodeName !== "#text") {
                $this->$nodeName = $nodeValue;
            }   
        }
  
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
        return true;  
    }
 
    // end class VUIFrameItem  
}
