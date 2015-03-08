<?php

/**
 * VWP Registry Library
 * 
 * This file provides Registry Key Support
 *   
 * @package VWP
 * @subpackage Libraries.System
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License
 */

/**
 * VWP Registry Library
 * 
 * This is the base class for Registry Key Objects.
 *   
 * @package VWP
 * @subpackage Libraries.System
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License
 */
 
 class HKEY extends VObject 
 {
 
     /**
      * DomDocument Containing Key Data
      * 
      * @var object $doc Dom Document
      * @access public   
      */
              
     var $doc;
 
     /**
      * Root Element for this Key
      * 
      * @var object $rootNode Root Node for this key
      * @access public   
      */
  
     var $rootNode;
  
     /**
      * Key Node Name
      * 
      * @var string $rootKey Key Node Name
      * @access public         
      */
        
     var $rootKey;
  
     /**
      * Close this key
      * 
      * @access public
      */
        
     function Close() 
     {
         unset($this->doc);
         unset($this->rootNode);
         unset($this->rootKey);
     }
  
     /**
      * Initialize key from XML Source
      * 
      * @param string $src XML Source
      * @access public
      */
        
     function loadXML($src) 
     {
         $this->doc->loadXML($src);
     }
  
     /**
      * Generate XML document from Root Key
      *
      * @return boolean True on success
      * @access public
      */
        
     function saveXML() 
     {
         return $this->doc->saveXML();
     }
  
     /**
      * Get A value from this key
      * 
      * @param integer $val_idx Index
      * @return Value on success or false if value index not found
      * @access public
      */
                 
     function getValue($val_idx) 
     {   
         $nodeList = $this->rootNode->getElementsByTagName('value');
         if ($nodeList->length == 0) {
             return false;
         }
   
         $idx = $val_idx;
         while($idx < 0) {
             $idx = $nodeList->length + $idx; 
         }
  
         if ($idx >= $nodeList->length) {
             return false;
         }
         
         $ret = array();
         $cur_node = $nodeList->item($idx);
         $ret["name"] = $cur_node->getAttribute("name");
         $ret["type"] = $cur_node->getAttribute("type");
         $ret["data"] = $cur_node->nodeValue;
         return $ret;
    }

    /**
     * Get A Subkey
     * 
     * @param string $path Relative Path to key
     * @return object|false Returns a registry key on success or false on failure
     * @access public   
     */
  
    function getKey($path) 
    {
  
        $parts = explode("\\",strtoupper($path));
   
        if (isset($this->rootNode)) {
            $cur_node = $this->rootNode;
        }
   
        foreach($parts as $item) {
            if (isset($cur_node)) {
                $nodeList = $cur_node->getElementsByTagName('key');
                for ($idx = 0; $idx < $nodeList->length; $idx++) {
                    if (!isset($new_node)) {
                        if ($nodeList->item($idx)->getAttribute('name') == $item) {
                            $new_node = $nodeList->item($idx);
                        }
                    }     
                }
                
                if (isset($new_node)) {
                    $cur_node = $new_node;
                } else {
                    unset($cur_node);
                }   
            }    
            unset($new_node);
        }
   
        if (isset($cur_node)) {
            $key = new HKEY();
            $key->doc = & $this->doc;
            $key->rootNode = $cur_node;
            if (isset($this->rootKey)) {
                $key->rootKey = & $this->rootKey;
            } else {
                $key->rootKey = & $this;
            }
            return $key;
        }
         
        return false;
    }

    /**
     * List Keys
     *    
     * @return array List of keys
     * @access public
     */
   
    function listKeys() {  
        $keyList = array();
        if (isset($this->rootNode)) {       
            $cur_node = $this->rootNode;
            if (!isset($cur_node->childNodes)) {
                //print_r(array($cur_node));
            }
            
            // $nodeList = $cur_node->getElementsByTagName('key');   
            for ($idx = 0; $idx < $cur_node->childNodes->length; $idx++) {
                $child = $cur_node->childNodes->item($idx);
                if ($child->nodeType == XML_ELEMENT_NODE && $child->nodeName == 'key') {    
                    $keyList[] = $child->getAttribute('name');
                }     
            }
        }   
        return $keyList;   
    }
          
    /**
     * Set a value
     * 
     * @param string $name Value identifier
     * @param string $value Value
     * @param integer $type Data type
     * @return true|false True on success or false on failure
     */
                       
    function setValue($name,$value,$type) 
    {
        $nodeList = $this->rootNode->getElementsByTagName('value');
        $ref = strtolower($name);
        $done = false;
   
        $fixval = $value;
        $fixval = str_replace("&","&amp;",$fixval);
        $fixval = str_replace("<","&lt;",$fixval);
        $fixval = str_replace(">","&gt;",$fixval);
     
        for($ptr = 0; $ptr < $nodeList->length; $ptr++) {
            if ($nodeList->item($ptr)->getAttribute('name') == $ref) {     
                $nodeList->item($ptr)->nodeValue = $fixval;
                $nodeList->item($ptr)->setAttribute('type',$type);
                $done = true;
            }
        }
   
        if (!$done) {
            $new_node = $this->doc->createElement('value',$fixval);    
            $new_node->setAttribute('name',$ref);
            $new_node->setAttribute('type',$type);
            $this->rootNode->appendChild($new_node);
        }

        if (isset($this->rootKey)) {
            $this->rootKey->dirty = true;
        } else {
            $this->dirty = true;
        }       
        return true;
    }
  
    /**
     * Create a key
     * 
     * @param string Relative path of new key
     * @return object|false Registry key on success or false on failure
     * @access public   
     */
                 
    function createKey($path) 
    {
  
        $parts = explode("\\",strtoupper($path));
   
        if (isset($this->rootNode)) {
            $cur_node = $this->rootNode;
        }
   
        foreach($parts as $item) {
            if (isset($cur_node)) {
                $nodeList = $cur_node->getElementsByTagName('key');
                for ($idx = 0; $idx < $nodeList->length; $idx++) {
                    if (!isset($new_node)) {
                        if ($nodeList->item($idx)->getAttribute('name') == $item) {
                            $new_node = $nodeList->item($idx);
                        }
                    }     
                }
                if (!isset($new_node)) {           
                    $new_node = $this->doc->createElement('key');
                    $new_node->setAttribute('name',$item);
                    $cur_node->appendChild($new_node);
                    if (isset($this->rootKey)) {
                        $this->rootKey->dirty = true;
                    } else {
                        $this->dirty = true;
                    }      
                }
                $cur_node = $new_node;   
            }    
            unset($new_node);
        }
   
        if (isset($cur_node)) {
            $key = new HKEY();
            $key->doc = & $this->doc;
            $key->rootNode = $cur_node;
            if (isset($this->rootKey)) {
                $key->rootKey = & $this->rootKey;
            } else {
                $key->rootKey = & $this;
            }
            return $key;
        } 
        return false;
    }

    /**
     * Class Constructor
     * 
     * @access public
     */           
  
    function __construct() 
    {
   
    }
    
    // end class HKEY
} 
