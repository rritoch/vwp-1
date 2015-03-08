<?php

/**
 * Virtual Web Platform - Warnings
 *  
 * This file provides Warnings for the Error Handling system   
 * 
 * @package VWP
 * @subpackage Libraries  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License   
 */

// Restrict access
class_exists('VWP') || die(); // restrict access

/**
 * Virtual Web Platform - Warnings
 *  
 * This class provides Warnings for the Error Handling system   
 * 
 * @package VWP
 * @subpackage Libraries  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */
 
class VWarning extends VObject 
{
    
    /**
     * Message Thrown
     * 
     * @var boolean $thrown True if this object has been thrown
     * @access public
     */
                
    var $thrown = false;
    
    /**
     * Message Code
     * 
     * @var integer $errno Error code
     * @access public
     */
        
    var $errno = 0;
    
    /**
     * Message
     * 
     * @var string $errmsg Error message
     * @access public
     */
    
    var $errmsg = null;
    
    /**
     * System Name
     * 
     * @var string $errsystem Error system name
     * @access public
     */
    
    var $errsystem = null;
    
    /**
     * Backtrace
     * 
     * @var array $backtrace Backtrace
     * @access public
     */
    
    var $backtrace = array();
    
    /**
     * Distribute the message
     * 
     * @return object The current error or warning object
     * @access public  
     */           
     
    function ethrow() 
    {
        
        if (!$this->thrown) {    
            $is_error = self::isError($this);
            $this->thrown = true;
            $errstr = '';
            if (!empty($this->errsystem)) {
                $errstr .= "[" . $this->errsystem . "] ";
            }   
            if ($is_error) {
                $errstr .= "User Error";
            } else {
                $errstr .= "User Warning";
            }
            if (!empty($this->errno)) {
                $errstr .= " #" . $this->errno;
            }    
            $msg = $this->errmsg;
            if (empty($msg)) {
                $msg = "[no status]";
            }  
            $errstr .= ": $msg";    
            if ($is_error) {
                VWP::dispatchError(E_USER_ERROR, $errstr);
            } else {
                VWP::dispatchError(E_USER_WARNING, $errstr);
            }
        }
        return $this;
    }
    
    /**
     * Test if a value is an Error
     * 
     * @access public
     * @param mixed $ob Value to test
     * @return boolean True if value is an Error   
     */
     
    public static function isError($ob = null) 
    {
        if (is_object($ob)) {
            $className = strtolower(get_class($ob));
            if (substr($className,strlen($className) - 5,5) == "error") {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Test if a value is a Warning
     * 
     * @access public
     * @param mixed $ob Value to test
     * @return boolean True if value is an error or warning
     */
                       
    public static function isWarning($ob = null) 
    {
        if (is_object($ob)) {
            if (self::isError($ob)) {
                return true;
            }  
            $className = strtolower(get_class($ob));
            if (substr($className,strlen($className) - 7,7) == "warning") {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Generate a Warning
     * 
     * @param string $msg Warning Message
     * @param string $system System Name
     * @param integer $errno Warning code
     * @param boolean $throw Throw error
     * @return object Error object
     * @access public   
     */
     
    function raiseWarning($msg,$system = null,$errno = null,$throw = true) 
    {  
        return new VWarning($msg,$system,$errno,$throw);  
    }
    
    /**
     * Class Constructor
     * 
     * @param string $msg Message
     * @param string $system System Name
     * @param integer $errno Warning code
     * @param boolean $throw Throw error
     * @access public
     */
         
    function __construct($msg,$system = null, $errno = null, $throw = true) 
    {
        parent::__construct();   
        $this->thrown = false;
        $this->errmsg = $msg;
        $this->errno = $errno;     
        $this->errsystem = $system;     
        if ($throw) {
            $this->ethrow(); 
        }
    }
 
    // end class VWarning     
}
