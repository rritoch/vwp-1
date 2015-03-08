<?php

/**
 * Virtual Web Platform - FTP Data Line
 *  
 * This file provides the data connection for FTP Clients.   
 * 
 * @package VWP
 * @subpackage Libraries.Networking.Clients.FTP  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License   
 */

/**
 * Require Socket Support
 */

VWP::RequireLibrary('vwp.net.socket');

/**
 * Virtual Web Platform - FTP Data Line
 *  
 * This class provides the data connection for FTP Clients.   
 * 
 * @package VWP
 * @subpackage Libraries.Networking.Clients.FTP  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License   
 */

class VFTPClientDataLine extends VSocket 
{
 
    /**
     * @var boolean $passive Passive connection
     * @access private
     */
       
    protected $passive = null;
 
    /**
     * @var string $pasv_host Host for passive connection
     * @access private
     */
       
    protected $pasv_host = null;

    /**
     * @var string $pasv_port Host for passive connection
     * @access private
     */

    protected $pasv_port = null;
 
    /**
     * @var integer $timeout Connection timeout
     * @access private
     */
     
    protected $timeout = 30;
 
      
    /**
     * Get socket address
     * 
     * @return string|obect Host address on success, error or warning on failure
     * @access public  
     */
           
    function getHost() 
    {
        $result = $this->getsockname();   
        if (VWP::isError($result)) {
            return $result;
        }
        return $result[0];
    }

    /**
     * Get socket port
     * 
     * @return string|obejct Host port on success, error or warning on failure
     * @access public  
     */

    function getPort() 
    {
        $result = $this->getsockname();
        if (VWP::isError($result)) {
            return $result;
        }
        return $result[1];  
    }

    /**
     * Set active state
     * 
     * @param string $host Host interface to bind to
     * @param integer $port Host port to bind to
     * @return true|object True on success, error or warning on failure
     */
              
    function setActive($host = 0,$port = 0) 
    {
        $this->passive = false;
        $reply = $this->bind($host,$port);   
        if (VWP::isError($reply)) {
            return $reply;
        }
        return $this->listen();   
    }

    /**
     * Set passive state
     * 
     * @param string $host Host interface to bind to
     * @param integer $port Host port to bind to
     * @return true|object True on success, error or warning on failure
     */

    function setPassive($host,$port = 22) 
    {
        if ($this->passive === null) {
            $this->passive = true;
            $this->pasv_host = $host;
            $this->pasv_port = $port;
        } else {
            return VWP::raiseError("Already in active mode!","VFTPClientDataLine");
        }
        return true;
    }  

    /**
     * Set timeout
     * 
     * @param integer $timeout Timeout in seconds
     * @access public
     */
           
    function setTimeout($timeout) {
        $this->timeout = $timeout;
    }
 
    /**
     * Read data from data line
     * 
     * @param integer|false $length Number of bytes to read or false to read until no data is available
     * @param integer $type Read type (see php documentation of socket_read)
     * @return string|object Data on success, error or warning on failure  
     */
             
    function read($length, $type = PHP_BINARY_READ) 
    {
        if ($length === false) {
            $result = '';
            // read until empty
            $done = false;
            while(!$done) {
                $r = array($this);
                $w = array();
                $e = array($this);
         
                $reply = $this->doSelect($r,$w,$e,$this->timeout);
    
                if (VWP::isWarning($reply)) {
                    $done = true;
                } elseif ($reply < 1) {
                    $done = true;
                    $this->close();
                } else if (count($r) > 0) {
                    $msg = parent::read(1024);      
                    if (VWP::isWarning($msg)) {
                        return $msg;
                    }
                    if (strlen($msg) > 0) {
                        $result .= $msg;
                    } else {
                        $done = true;
                    }
                } elseif (count($e) > 0) {
                    $done = true;      
                }     
            }        
        } else {
            $result = parent::read($length,$type);   
        }
  
        return $result; 
    }

    /**
     * Establish connection
     * 
     * @param string $host Host address
     * @param integer $port Host port
     * @access public
     * @return true|object True on success, error or warning on failure      
     */
           
    function connect($host, $port = 0) 
    {
        if ($this->passive === null) {
            return VWP::raiseError("Active/Passive not selected!","VFTPClientDataLine");
        }
  
        if ($this->passive) {
            $result = parent::connect($this->pasv_host,$this->pasv_port);
            if (VWP::isWarning($result)) {
                return $result;
            }        
        } else {    
            $oldsock = $this->_socket;   
            $newsock = parent::accept();
            if (VWP::isWarning($newsock)) {
                return $newsock;
            }
            $this->close();
            $this->setProperties($newsock->getProperties());
            $this->_socket = $newsock->_socket;    
        }
        return true;
    }
    
    // end class VFTPClientDataLine
} 
