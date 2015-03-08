<?php

/**
 * SMTP Server Support library
 *     
 * @package VWP
 * @subpackage Libraries.Networking.Protocols  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

VWP::RequireLibrary('vwp.net.socket');

/**
 * SMTP Server Support library
 * 
 * This class is used for accessing SMTP Servers.
 *     
 * @package VWP
 * @subpackage Libraries.Networking.Protocols  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

class VSMTP extends VSocket 
{

    /**
     * TCP/IP Socket Buffer
     * 
     * @var string $_sock_buff TCP/IP Socket buffer
     * @access private   
     */

    protected $_sock_buff;

    /**
     * SMTP Message Buffer
     *   
     * @var string $_smtp_msg SMTP Message buffer
     * @access private   
     */


    protected $_smtp_msg;
 
    /**
     * SMTP Response code
     *   
     * @var string $_smtp_code SMTP Response code
     * @access private   
     */

    protected $_smtp_code;

    /**
     * Connection
     */
    
    protected $_connection_state = false;
    
    /**
     * Class constructor
     * 
     * @access public  
     */
         
    function __construct() 
    { 	
 	    parent::__construct(AF_INET,SOCK_STREAM,SOL_TCP,true);
 	    $this->_sock_buff = '';      
    }

    /**
     * Send message data to SMTP server
     * 
     * @return boolean|object True on success, error or warning otherwise
     * @access public      
     */   

    function send_data($data) 
    {
 
 	    if (354 != ($errno = $this->smtp_command("DATA"))) {
            return $this->report_error($this->_smtp_msg,$errno);
	    }
	  
 	    socket_write($this->_socket, $data,strlen($data));
   
        if ($this->smtp_command("\r\n.") != 250) {
            return $this->report_error("Could not finish data input.");
        }
    
        return true;
    }

    /**
     * Get a line of data from SMTP server
     * 
     * @return string Line of data
     * @access public    
     */     

    public function smtp_getline() 
    {
        $ret = "";   
   
        if (count(explode("\n",$this->_sock_buff)) < 2) {    
            $ret = null;     
            while ($line = @ socket_read($this->_socket,512,PHP_BINARY_READ)) {
                $this->_sock_buff .= $line;
                if (count(explode("\n",$this->_sock_buff)) > 1) {
                    $ret = "";
                    break;
                }         
            }
        }
   
        if ($ret === null) {    
            return null;
        }
   
        $tmpa = explode("\n",$this->_sock_buff);
        $ret = array_shift($tmpa);
        if (count($tmpa) > 0) {
            $this->_sock_buff = implode("\n",$tmpa);
        } else {
            $this->_sock_buff = "";
        }
        
        $tmp = strlen($ret);
        if ($tmp > 0) {
            if ($ret[$tmp - 1] == "\r") {
                $ret = substr($ret,0,$tmp - 1);
            }
        }
        return $ret;    
    }

    /**
     * Send a command to the SMTP server
     * 
     * @param string $cmd SMTP Command
     * @return integer SMTP Response code
     * @access public
     */     
  
    function smtp_command($cmd) 
    {
    	VWP::nowarn();
        $sock_data = @ socket_write($this->_socket, $cmd . "\r\n", strlen($cmd) + 2);
        VWP::noWarn(false);
        
        if ($sock_data === false) {
        	$this->_smtp_msg = "Unable to write to socket";
        	return $this->report_error($this->_smtp_msg);
        }
        
        $this->_smtp_msg = "";
        $this->_smtp_code = false;
        $i = 0;
   
        while ($line = $this->smtp_getline()) {
            $i++;
            
            if ((strlen($line) > 3) && ($line[3] == " ")) {     
                $tmp = substr($line,0,3) + 0;
                $this->_smtp_msg .= substr($line,4);     
                if ($tmp != 220) {      
                    $this->_smtp_code = $tmp;
                    break;
                }  
            } else {
                $this->_smtp_msg .= $line;
            }         
        }   
        return $this->_smtp_code;   
    }

    /**
     * Report an error
     *   
     * @param string $msg Error message
     * @param integer $err Error code
     * @return VWarning Error to report    
     * @access public   
     */
        
    function report_error($msg, $err = null) 
    {
        $this->smtp_disconnect();   
        return VWP::raiseWarning($msg,__CLASS__,$err,false);
    }

    /**
     * Connect to SMTP server
     * 
     * @param string $host Hostname
     * @param string $user username
     * @param string $pass Password
     * @param integer $port TCP/IP Port
     * @return boolean|object True on success, error or warning otherwise
     * @access public      
     */
                           
    function smtp_connect($host = "localhost", $user = FALSE, $pass = FALSE, $port = 25) 
    {

        $result = $this->connect($host,$port);
        if (VWP::isWarning($result)) {
        	return $result;
        }    
   
       if ($user && $pass) {
   	       if (250 != ($err = $this->smtp_command("EHLO ". $host))) {
   	           return $this->report_error($this->_smtp_msg,$err);   	 
           }
    
           if ($this->smtp_command("AUTH LOGIN") == 334) {
               if (334 != ($err = $this->smtp_command(base64_encode($user)))) {
			       return $this->report_error($this->smtp_msg,$err);			
               }
		       if (235 != ($err = $this->smtp_command(base64_encode($pass)) )) {
                   return $this->report_error($this->smtp_msg,$err);
 	           }      
           }
       } else {
           if (250 != ($err = $this->smtp_command("HELO ". $host))) {
               return $this->report_error($this->smtp_msg,$err);
           }
       }
       $this->_connection_state = true;
       return true; 
    }
  
    /**
     * Disconnect from SMTP server
     * 
     * @access public   
     */
          
    function smtp_disconnect() 
    {	
    	if ($this->_connection_state) {
    		$this->_connection_state = false;    
            $this->smtp_command("QUIT");
    	}        
        $this->disconnect();               
        $this->_socket = socket_create($this->_domain  ,$this->_type  ,$this->_protocol);     
        $this->_sock_buff = "";
	}
	
    // end class VSMTP 
} 
 
