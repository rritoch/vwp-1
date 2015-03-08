<?php

/**
 * Virtual Web Platform - Telnet Protocol (RFC-854)
 *  
 * This file provides the Telnet protocol API.   
 * 
 * @todo Implement telnet control code processing for Virtual Terminal Emulation
 * @package VWP
 * @subpackage Libraries.Networking.Protocols  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

VWP::RequireLibrary('vwp.net.socket');

/**
 * Virtual Web Platform - Telnet Protocol (RFC-854)
 *  
 * This file provides the Telnet protocol API.   
 * 
 * @package VWP
 * @subpackage Libraries.Networking.Protocols  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */
 
class VTelnet extends VSocket 
{
 
    /**
     * Input buffer
     *    
     * @var string $_in_buffer Input buffer
     * @access private
     */
       
    protected $_in_buffer = '';
 
    /**
     * Timeout
     *    
     * @var integer $_timeout Timeout
     * @access private
     */
   
    protected $_timeout;
 
    /**
     * Read line of input
     * 
     * @return string|object Data line on success, error or warning on failure
     * @access public
     */
         
    function readLine() 
    {
 
        $result = true;
  
        while(!$this->isError($result)) {
            $bparts = explode("\n",$this->_in_buffer);
            if (count($bparts) > 1) {
                $ret = array_shift($bparts) . "\n";
                $this->_in_buffer = implode("\n",$bparts);
                return $ret;
            }
  
            $r = array($this);
            $w = array();
            $e = array();
            $result = $this->doSelect($r,$w,$e,$this->_timeout);
  
            if (!$this->isError($result)) {   
                if ($result > 0) {
                    $result = $this->read(1024);
                    if (!$this->isError($result)) {
                        $this->_in_buffer .= $result;
                    }
                }
            }
        }
        return $result;
    }
 
    /**
     * Test if a value is an error
     * 
     * @param mixed $ob Test object  
     * @return boolean True if test object is an error
     * @access public
     */
         
    function isError($ob) 
    {
        return VWP::isError($ob);
    }
 
    /**
     * Test if a value is a warning or an error
     * 
     * @param mixed $ob Test object  
     * @return boolean True if test object is a warning or error
     * @access public
     */
   
    function isWarning($ob) 
    {
        return VWP::isWarning($ob);
    }
 
    /**
     * Generate an error
     * 
     * @param string $msg Error message
     * @param string $service Service name       
     * @param integer $errno Error code
     * @param boolean $throw Throw error
     * @return object Error
     * @access public
     */
 
    function raiseError($msg = "Unidentified Error",$service = "Telnet",$errno = null, $throw = true) 
    {
        return VWP::raiseError($msg,$service,$errno,$throw);
    }
 
    /**
     * Generate a warning
     * 
     * @param string $msg Error message
     * @param string $service Service name       
     * @param integer $errno Error code
     * @param boolean $throw Throw error
     * @return object Error
     */ 
 
    function raiseWarning($msg = "Unidentified Warning",$service = "Telnet",$errno = null, $throw = true) 
    {
        return VWP::raiseWarning($msg,$service,$errno,$throw);
    }
 
    /**
     * Class constructor
     *   
     * @access public
     */
       
    function __construct() 
    {   
        parent::__construct(AF_INET,SOCK_STREAM,SOL_TCP);
        $this->timeout = 30;   
    }
    
    // end class VTelnet
} 

