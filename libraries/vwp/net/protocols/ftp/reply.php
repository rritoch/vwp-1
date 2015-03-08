<?php

/**
 * Virtual Web Platform - FTP Protocol (RFC-959)
 *  
 * This file provides the FTP reply interface.   
 * 
 * @package VWP
 * @subpackage Libraries.Networking.Protocols.FTP  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License 
 */

/**
 * Virtual Web Platform - FTP Protocol (RFC-959)
 *  
 * This class provides the FTP reply interface.   
 * 
 * @package VWP
 * @subpackage Libraries.Networking.Protocols.FTP  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License   
 */
 
class VFtpReply extends VObject 
{

    /**
     * @var array $reply Reply data
     * @access private
     */
  
    protected $reply = array();
 
    /**
     * @var integer $reply_code Reply code
     * @access private
     */
          
    protected $reply_code = null;
 
    /**
     * @var mixed $data Data buffer
     * @access private   
     */     
  
    protected $data = null;

    /**
     * Get host
     * 
     * Reserved for future use
     * @access private
     */
                
    function getHost() 
    {
        return null;
    }

    /**
     * Get port
     * 
     * Reserved for future use
     * @access private
     */
 
    function getPort() 
    {
        return null;
    }
 
    /**
     * Set data
     * 
     * @param mixed $data Reply data
     * @access public   
     */
             
    function setData($data) {
        $this->data = $data;
    }

    /**
     * Get data
     * 
     * @return mixed $data Reply data
     * @access public   
     */
 
    function getData() 
    {
        return $this->data;
    }
 
    /**
     * Write one FTP reply line to message buffer
     * 
     * <pre>
     *  A message is an array consists of 3 elements
     *  
     *  message[0] : Reply code
     *  message[1] : Reply type
     *  message[2] : Reply text
     *                 
     * </pre>
     * 
     * @param string $msg One line of the FTP servers reply               
     * @return array Message
     * @access public
     */
             
    function write($msg) 
    {
        $code = substr($msg,0,3);
        $type = substr($msg,3,1);
        $txt = substr($msg,4);
  
        if ($this->reply_code === null) {
            $this->reply_code = $code; 
        }
        $message = array($code,$type,$txt);
        array_push($this->reply,$message);
        return $message;
    }
 
    /**
     * Get reply code
     * 
     * @return integer Reply code
     * @access public
     */
             
    function getReplyCode() 
    {
        return $this->reply_code;
    }
 
    /**
     * Get reply level
     * 
     * <pre>
     *  Reply codes consist of a 3 digit number.
     *  The first digit is used to determine the reply level.
     *  If the first digit is less than 3 the operation was successful.                         
     * </pre>
     * 
     * @return integer Returns -1 on success, 3 to 5 on warning or error
     * @access public               
     */
       
    function getReplyLevel() 
    {
        $code = $this->reply_code;
        if (strlen($code) < 3) {
            return -1;
        }
        $level = substr($code,0,1);
        return $level; 
    }
 
    /**
     * Reserved for future use
     *
     * @param string $txt Path info
     * @access private
     */
             
    function _parsePathInfo($txt) 
    {    	
        // check format
    }
 
 
    /**
     * Get path info
     * 
     * <pre>  
     * Path info will be an empty array if no path response was received.
     * Otherwise the path info array has 2 elements:
     *   
     *  pathinfo[0] : Path
     *  pathinfo[1] : Comments
     *      
     * </pre>
     *            
     * @return array Path info   
     * @access public      
     */
       
    function getPathInfo() 
    {
        $info = array();
        if ($this->getReplyCode() == 257) {
            $txt = $this->reply[0][2];
    
            $quote = '';
            $quote = substr($txt,0,1);
    
            if (        
                ($quote == "\"") || ($quote == "'")
               ) {
                              
                $msg = substr($txt,1);            
                $msg = str_replace("%","%26",$msg);   
                $msg = str_replace($quote.$quote,"%22%22",$msg);      
                $parts = explode($quote,$msg);
                $path = array_shift($parts);
                $comments = implode($quote,$parts);   
                $path = str_replace("%22%22",$quote,$path);
                $comments = str_replace("%22%22",$quote.$quote,$comments);
                $path = str_replace("%26","%",$path);
                $comments = str_replace("%26","%",$comments);
                $info = array($path,$comments);              
            }
        }
        return $info;
    }
 
    /**
     * Get reply messages
     * 
     * @return array Reply messages
     * @access public  
     */
           
    function value() 
    {
        return $this->reply;
    }
 
    /**
     * Get text reply
     * 
     * @return string Reply text
     * @access public
     */
           
    function toString() 
    {
        $txt = '';
        foreach($this->reply as $msg) {
            $txt .= $msg[2];
        }
        return $txt;
    }
    
    // end class VFtpReply
} 
