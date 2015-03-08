<?php

/**
 * Virtual Web Platform - Object
 *  
 * This file provides the base class for all VWP objects
 * 
 * Note: This is the implementation of MIT Style
 *       Data Access Objects (DAO)
 *                 
 * @package VWP
 * @subpackage Libraries  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

// Restrict access                                
class_exists('VType') || die(); // restrict access  

/**
 * Virtual Web Platform - Object
 *  
 * This file provides the base class for all VWP objects
 *        
 * @package VWP
 * @subpackage Libraries  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License   
 */

class VObject extends VType 
{

    /**
     * Object Errors
     * 
     * @var array $_nosave_errors Errors
     * @access private  
     */
         
    protected $_nosave_errors = array();


    /**
     * Object Original State
     * 
     * @var array $_nosave_original_state Original State
     * @access private  
     */

    protected $_nosave_original_state = null;
  
    /**
     * Class Constructor
     * 
     * @access public    
     */
      
    function __construct() 
    {        
        parent::__construct();
        $vars = get_object_vars($this);        
        $this->_nosave_original_state = array();
        foreach(array_keys($vars) as $k) {
            $this->_nosave_original_state[$k] = $this->$k;  
        }
         
    }
 
    /**
     * Class Destructor
     *   
     * @access public
     */
       
    public function __destruct() 
    {
        parent::__destruct(); 
    }

    /**
     * Get value
     * 
     * @param string $vname Value name
     * @param mixed $default Default value
     * @return mixed Value
     * @access public  
     */           

    public function &get($vname, $default=null) 
    {
        if (isset($this->$vname)) {
            return $this->$vname;
        }
        return $default;
    }


    /**
     * Get all object values
     *                      
     * @param boolean $public Return public values
     * @return array Object values indexed by name
     * @access public  
     */         

    function getAll( $public = true ) 
    {
        $vars = get_object_vars($this);
        if ($public) {
            foreach ($vars as $key => $value) {
                if ('_' == substr($key, 0, 1)) {
                    unset($vars[$key]);
                }
            }
        }
        return $vars;
    }

    /**
     * Bind values to this object
     * 
     * @param array|object $to Source object or data
     * @access public
     */
           
    function bind($to) 
    {
        if (is_object($to)) {
            $vars = get_object_vars($to);
            foreach($vars as $key => $value) {
                $this->$key = & $to->$key;
            }
        }
        if (is_array($to)) {
            foreach($to as $key => $value) {
                $this->$key = & $to[$key];
            }   
        }  
    }

    /**
     * Get object methods
     *
     * @param boolean $public Return public methods
     * @return array Object methods
     * @access public  
     */         

    function getMethods( $public = true ) 
    {
        
        if ($public) {
            $raw_methods = $vars = get_class_methods($this);
            $methods = array();
            foreach ($raw_methods as $methodName) {
                if ('_' != substr($methodName, 0, 1)) {
                    $methods[] = $methodName;
                }
            }
        } else {
            $methods = get_class_methods($this);
        }
        return $methods;
    }
 
    /**
     * Get object properties
     *
     * @param boolean $public Return public properties
     * @return Object properties
     * @access public  
     */         

    function getProperties( $public = true ) 
    {
        $vars = get_object_vars($this);
        if ($public) {
            foreach ($vars as $key => $value) {
                if ('_' == substr($key, 0, 1)) {
                    unset($vars[$key]);
                }
            }
        }
        return $vars;
    }

    /**
     * Save Object
     * 
     * @return array|object Saved data on success, error or warning otherwise     
     * @access public
     */

    public function save() 
    {
        $vars = get_object_vars($this);
  
        foreach ($vars as $key => $value) {
            if ('_nosave_' == substr($key, 0, 8)) {
                unset($vars[$key]);
            }
            if ('nosave_' == substr($key, 0, 7)) {
                return true;
            }
        }  
        return $vars;  
    }
 
    /**
     * Delete Object
     * 
     * @return boolean|object True on success, error or warning otherwise     
     * @access public
     */
                        
    function delete() 
    {
        $vars = get_object_vars($this);
        foreach($vars as $key=>$val) {
            if (isset($this->_nosave_original_state[$key])) {
                $this->$key = $this->_nosave_original_state[$key];
            } else {
                unset($this->$key);
            }
        }
        return true;
    }
 
    /**
     * Get error message
     * 
     * @param integer Index
     * @param boolean Convert error to string
     * @return mixed Error message     
     * @access public     
     */         

    function getError($i = null, $toString = true ) 
    {
        // Find the error
        if ( $i === null) {
            // Default, return the last message
            $error = end($this->_nosave_errors);
        } elseif (!array_key_exists($i, $this->_nosave_errors)) {
            // If $i has been specified but does not exist, return false
            return false;
        } else {
            $error = $this->_nosave_errors[$i];
        }
  
        // Check if only the string is requested
        if ( VWP::isError($error) && $toString ) {
            return $error->toString();
        }
        return $error;
    }

    /**
     * Get errors
     *   
     * @return array Errors
     * @access public  
     */
        	 
    function getErrors() 
    {
        return $this->_errors;
    }

    /**
     * Set a value
     * 
     * @param string $vname Value name
     * @param mixed $value Value
     * @return mixed previous Value
     */
             
    function set( $vname, $value = null ) 
    {
        $previous = isset($this->$vname) ? $this->$vname : null;
        $this->$vname = $value;
        return $previous;
    }

    /**
     * Set property values
     * 
     * @param array $properties Properties
     * @return boolean True on success
     */         

    function setProperties( $properties ) 
    {	 
        $properties = (array) $properties; //cast to an array
        if (is_array($properties)) {
            foreach ($properties as $k => $v) {
                $this->$k = $v;
            }
            return true;
        }
        return false;
    }

    /**
     * Set property value
     * 
     * Stores a copy of the provided value as the requested property
     * 
     * @param string $name Property name
     * @param mixed $value Property value
     * @return mixed new property value
     */
             
    function setProperty($name,$value) 
    {
        $this->$name = $value;
        return $value;
    }

    /**
     * Return object as a string
     * 
     * @return string Human readable object string
     * @access public      
     */
     
    function __toString() 
    {  
        $ret = "VObject ";
        $ret .= get_class($this);
        $properties = self::getProperties();
        $ret .= "[";
        foreach($properties as $key=>$val) {
            $ret .= (string) $key . " = " . (string) $val;
        }
        $ret .= "]";
        return $ret;
    }  

    /**
     * Set error value
     * 
     * @param string|object $error Error
     * @access public  
     */
           
    function setError($error) {
        array_push($this->_nosave_errors, $error);
    }

    /**
     * Convert object to string
     * 
     * @return string String representation of object
     * @access public  
     */
       
    function toString() 
    {	 
        $className = get_class($this);
        $properties = array();
        $p = self::getProperties();
        foreach($p as $k=>$v) {
            array_push($properties,'"' . addcslashes($k) . '"=>' . var_export($v,true));
        }
        return $className . "[" . implode(",",$properties) . "]";
    }
    
    // end class VObject
} 