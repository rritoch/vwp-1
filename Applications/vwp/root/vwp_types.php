<?php

/**
 * Virtual Web Platform Data Type
 *
 * This file provides support for MIT style value objects
 *
 * @package VWP
 * @subpackage Base
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License
 * @access private
 */

/**
 * Virtual Web Platform Data Type
 *
 * This class provides support for MIT style value objects
 *
 * @package VWP
 * @subpackage Base
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License
 * @access private
 */

class VType 
{

    /**
     * @var $_sys_datatypes All Data Types
     * @access private
     */
       
    protected $_sys_datatypes;

   /**
    * @var $_ob_type Current Data Type
    * @access private
    */

    protected $_ob_type;
    
    /**
     * @return string Data Type
     * @access public     
     */
     
    public function getType() 
    {                        
         return $this->_ob_type;                
    }
 
    /**
     * Check if this object is of the requested type
     * 
     * @param object $ob Test object
     * @param string $type Type
     * @return boolean True if object is of the requested type
     * @access public                
     */

    public static function isType(&$ob, $type)
    {
     
        if (!is_object($ob)) {
            return false;
        }
     
        if (!is_string($type)) {
            return false;
        }
        
        if (!method_exists($ob,'is')) {
            return false;
        }
        
        return $ob->is($type);
    }

    /**
     * Get Parent Class Names
     * 
     * @param string $class Subject class
     * @param string $parents Extra parent classes          
     * @access public
     */                   

    public function getParents($class=null, $parents=array())     
    {
        $class = $class ? $class : $this;
        $parent = get_parent_class($class);
        if($parent) {
            $parents[] = $parent;
            $parents = self::getParents($parent, $parents);
        }
        return $parents;
    }

    /**
     * Check if this object is of the requested type
     * 
     * @param string $type Type
     * @return boolean True if object is of the requested type           
     */


    public function is($type) 
    {
        if (!is_string($type)) {
            return false;  
        }
        
        if (!is_array($this->_sys_datatypes)) {
            self::__construct();
        }
        
        return in_array($type,$this->_sys_datatypes);  
    }   


    /**
     * Class Destructor
     * 
     * @access public          
     */
     
    public function __destruct() {
    
    }
  
    /**
     * Class Constructor
     * 
     * @access public          
     */
              
    public function __construct() {
        $this->_sys_datatypes = $this->getParents();
        $this->_ob_type = get_class($this);
        array_unshift($this->_sys_datatypes, $this->_ob_type);        
    }
    
    // end of class VType
}