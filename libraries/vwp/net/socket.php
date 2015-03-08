<?php

/**
 * Virtual Web Platform - Socket support
 *  
 * This file provides the default API for
 * Socket communication.   
 * 
 * @package VWP
 * @subpackage Libraries.Networking  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */


/**
 * Require PHP sockets extension
 */

VWP::RequireExtension('sockets');

/**
 * Virtual Web Platform - Socket support
 *  
 * This file provides the default API for
 * Socket communication.   
 * 
 * @package VWP
 * @subpackage Libraries.Networking  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License 
 */

class VSocket extends VObject 
{

    /**
     * @var array $_arguments Arguments
     * @access private   
     */
       
    protected $_arguments = null;
 
    /**
     * @var mixed $_socket System socket
     * @access private   
     */
          
    protected $_socket = null;
 
    /**
     * @var integer $_domain Socket domain
     * @access private   
     */
       
    protected $_domain = null;
 
    /**
     * @var integer $_type Socket type
     * @access private   
     */
       
    protected $_type = null;
 
    /**
     * @var integer $_protocol Socket protocol
     * @access private   
     */
       
    protected $_protocol = null;
 
    /**
     * @var boolean $_busy Socket busy   
     * @access private   
     */
         
    protected $_busy = false;
  
    /**
     * @var integer $errno Last err
     * @access public   
     */
       
    public $errno = null;

    /**
     * @var integer $errno Last error message
     * @access public   
     */

    public $errmsg = null;  

    /**
     * @var array $_proto_list Protocol list
     * @access private   
     */
       
    static $_proto_list; 
 
    /**
     * Get protocol of socket
     *    
     * @return integer Protocol
     * @access public   
     */
       
    function getProtocol($name) 
    {   
        return $this->_protocol;
    }
 
    /**
     * Get protocol name
     *    
     * @return string protocol name
     * @access public   
     */
       
    function getProtocolName() 
    {
        return getprotobynumber($this->_protocol);
    }
 
    /**
     * Get socket domain
     * 
     * @return integer Domain
     * @access public
     */
             
    function getDomain() 
    {
        return $this->_domain;
    }

    /**
     * Get socket type
     *    
     * @return integer Socket type
     * @access public
     */        

    function getType() 
    {
        return $this->_type;
    }
 
    /**
     * Get peer connection info
     *
     * <pre>
     *  Returns an array where item 1 is the address. 
     *  If there is a port the second item in the array will hold the port number
     * </pre>
     *                   
     * @return array Peer connection info
     * @access public
     */
             
    function getPeerName() 
    {
        $address = $null;
        $port = null;   
        if (!socket_getpeername($this->_socket,$address,$port)) {
            return self::raiseError(null,null,null,false);
        }
        if ($port === null) {
            return array($address);
        }
        return array($address,$port);
    }
 
    /**
     * Get Socket connection info
     * 
     * <pre>
     *  Returns an array where item 1 is the address. 
     *  If there is a port the second item in the array will hold the port number
     * </pre>
     * 
     * @return array local socket connection info            
     */
        
    function getSockName() 
    {
        $addr = null;
        $port = null;
        if (!socket_getsockname($this->_socket,$addr,$port)) {
            return self::raiseError(null,null,null,false); 
        }
        if ($port === null) {
            return array($addr);
        }
        return array($addr,$port);   
    }

    /**
     * Get list of supported protocols
     * 
     * @return array Protocol names indexed by protocol number
     * @access public   
     */
             
    public static function getSupportedProtocols() 
    {
        if (!isset(self::$_proto_list)) {
            self::$_proto_list = array();
            for ($p = 0; $p < 255; $p++) {
                $name = getprotobynumber($p);
                if ($name !== false) {
                    self::$_proto_list[$name] = $p;
                }
            }
        }
        return self::$_proto_list; 
    }  
 
    /**
     * Set the socket domain
     * 
     * @param integer $domain Socket Domain
     * @param boolean $create Build socket
     * @return true|object True on success, error or warning on failure.
     */
                      
    function setDomain($domain,$create = true) 
    {
        if ($this->_busy) {
            return self::raiseError("Socket busy!","SOCKET",null,false);
        }
        $this->_domain = $domain;
        if ($create) {
            $this->_socket = socket_create($this->_domain  ,$this->_type  ,$this->_protocol);
            if (!$this->_socket) {
                return self::raiseError(null,null,null,false);
            }
        }
        return true;   
    }

    /**
     * Set protocol
     * 
     * @param integer $protocol Socket Protocol
     * @param boolean $create Create socket
     * @return True on success, error or warning on failure
     * @access public
     */
                   
    function setProtocol($protocol,$create = true) 
    {
        if ($this->_busy) {
            return self::raiseError("SOCKET","Socket busy!",null,false);
        }
        $this->_protocol = $protocol;
        if ($create) {
            $this->_socket = socket_create($this->_domain  ,$this->_type  ,$this->_protocol);
            if (!$this->_socket) {
                return self::raiseError(null,null,null,false);
            }
        }
        return true;   
    }
 
    /**
     * Set socket type
     *       
     * @param integer $type
     * @param boolean $create Create socket
     * @return True on success, error or warning on failure
     * @access public      
     */
             
    function setType($type,$create = true) 
    {
        if ($this->_busy) {
            return self::raiseError("SOCKET","Socket busy!",null,false);
        }
        $this->_type = $type;
        if ($create) {
            $this->_socket = socket_create($this->_domain  ,$this->_type  ,$this->_protocol);
            if (!$this->_socket) {
                return self::raiseError(null,null,null,false);
            }
        }
        return true;   
    }
 
    /**
     * Accept an incomming connection
     *
     * @return object Socket on success, error or warning on failure.
     * @access public
     */
              
    function &accept() 
    {
        $result = socket_accept($this->_socket);
        if (($result === false) || ($result === null)) {
            return self::raiseError(null,null,null,false);
        }
        $sock = new VSocket(0,0,0,false);
        $sock->_socket = $result;
        $sock->_busy = true;
        return $sock;
    }
 
    /**
     * Connect to remote socket
     * 
     * @param string $address Network address
     * @param integer $port Network port
     * @return True on success, Error or warning on failure   
     * @access public
     */
                   
    function connect($address, $port = 0) 
    {
        VWP::noWarn();   
        $result = @socket_connect($this->_socket,$address,$port);
        VWP::noWarn(false);
  
        if (($result === false) || ($result === null)) 
        {
            $this->_arguments= func_get_args();
            $err = self::raiseError(null,null,null,false); 
            $this->_arguments = null;
            return $err;
        }
        return true;
    }
 
    /**
     * Shutdown socket
     * 
     * <pre>
     *  possible values for how    
     *   0 Shutdown socket reading
     *   1 Shutdown socket writing
     *   2 Shutdown socket reading and writing
     * </pre>
     * 
     * @param integer $how How
     * @return true|object True on success, error or warning on failure
     * @access public            
     */                  
 
    function shutdown($how = 2) 
    {
        VWP::noWarn();
        $result = @socket_shutdown($this->_socket,$how);
        VWP::noWarn(false);
        if ($result === false) {
            return self::raiseError(null,null,null,false);
        }
        return true;  
    }
 
    /**
     * Close socket
     *
     * <pre>
     *  possible values for how    
     *   0 Shutdown socket reading
     *   1 Shutdown socket writing
     *   2 Shutdown socket reading and writing
     * </pre>    
     * @param boolean Shutdown first
     * @param integer How to shutdown
     * @access public
     */
                
    function close($shutdown = true,$how = 2) 
    {
        $result = $this->shutdown($how);
        socket_close($this->_socket);
        return $result;
    }
 
    /**
     * Disconnect
     * 
     * @return boolean|object True on success, error or warning otherwise.
     * @access public
     */
 
    function disconnect() 
    {
        return $this->close();
    }
 
    /**
     * Set socket blocking
     * 
     * @param boolean $block Blocking
     * @return true|object True on success, error or warning on failure
     * @access public
     */
                
    function setBlock($block) 
    {
        if ($block) {
            $result = socket_set_block();
        } else {
            $result = socket_set_nonblock();
        }
        if ($result === false) {
            return self::raiseError(null,null,null,false);
        }
        return true;      
    }
 
    /**
     * Bind socket 
     * 
     * @param string $address Address to bind to
     * @param integer $port Port to bind to
     * @return true|object True on success, error or warning on failure
     * @access public
     */
                    
    function bind($address,$port = 0) 
    {
        $result = socket_bind($this->_socket,$address,$port);
        if ($result === false) {
            return self::raiseError(null,null,null,false);
        }
        $this->_busy = true;
        return $result;
    }
 
    /**
     * Listen for connection
     * 
     * @param integer $backlog Backlog size
     * @return true|object True on success, error or warning on failure
     * @access public
     */
                
    function listen($backlog = 0) 
    {  
        $result = socket_listen  ( $this->_socket,$backlog);
        if ($result === false) {
            return self::raiseError(null,null,null,false);
        }
        $this->_busy = true;
        return $result;
    }

    /**
     * Monitor sockets for selected change
     * 
     * @param array $read Sockets to monitor for data
     * @param array $write Sockets to monitor for writeability
     * @param array $except Sockets to montior for exceptions
     * @param integer $tv_sec Timeout in seconds
     * @param integer $tv_usec Timeout microseconds      
     * @return integer|object Number of changed sockets on success, error or warning on failure      
     */               
      
    public static function doSelect(&$read,&$write,&$except, $tv_sec, $tv_usec = 0) 
    {
        $r = array();
        $w = array();
        $e = array();
  
        foreach($read as $reader) {
            array_push($r,$reader->_socket);
        }

        foreach($write as $writer) {
            array_push($w,$writer->_socket);
        }   
  
        foreach($except as $excepter) {
            array_push($e,$excepter->_socket);
        }
        $l = VWP::setTimeLimit(0);
        $result = socket_select($r,$w,$e,$tv_sec, $tv_usec);
        VWP::setTimeLimit($l);
  
        if (($result === false) || ($result === null)) {
            return self::raiseError(null,null,null,false);
        }
  
        $ro = array();
        $wo = array();
        $eo = array();
  
        foreach($read as $reader) {
            if (in_array($reader->_socket,$r)) {
                array_push($ro,$reader);
            }
        }

        foreach($write as $writer) {
            if (in_array($writer->_socket,$w)) {
                array_push($wo,$writer);
            }
        }   
  
        foreach($except as $excepter) {
            if (in_array($excepter->_socket,$e)) {
                array_push($eo,$excepter);
            }
        }
  
        $read = $ro;
        $write = $wo;
        $except = $eo;
  
        return $result;
    }

    /**
     * Get a socket option
     * 
     * @param integer $level Protocol level
     * @param integer $optname Option name
     * @return true|object True on success, error or warning on failure
     * @access public
     */      
                  
    function getOption($level, $optname) 
    {
        $result = socket_get_option($this->_socket,$level,$optname);
        if ($result === false) {
            return self::raiseError(null,null,null,false);
        }
        return true;   
    }
 
    /**
     * Set a socket option
     * 
     * @param integer $level
     * @param string $optname Option name
     * @param mixed $optval Option value
     * @return true|object True on success, error or warning on failure
     * @access public
     */
                      
    function setOption($level, $optname, $optval) 
    {
        $result = socket_set_option( $this->_socket,$level,$optname,$optval);
        if ($result === false) {
            return self::raiseError(null,null,null,false);
        }
        return true;
    }
 
    /**
     * Write to socket
     *      
     * @param string $buffer Buffer
     * @param integer $length Number of bytes to write
     * @return integer|object Number of bytes written on success, error or warning on failure
     * @access public   
     */
             
    function write($buffer,$length = 0) 
    {
        if ($length == 0) {
            $length = strlen($buffer);
        }
        $result = socket_write($this->_socket,$buffer,$length);
        if ($result === false) {
            return self::raiseError(null,null,null,false);
        }
        return $result;
    }

    /**
     * Read from socket
     * 
     * @param integer $length Number of bytes to read
     * @param integer $type Read type
     * @return string|object Data read on success, error or warning on failure
     * @access public
     */
                      
    function read( $length, $type = PHP_BINARY_READ) 
    {
        $result = socket_read($this->_socket,$length,$type);
        if ($result === false) {
            return self::raiseError(null,null,null,false);
        }
        return $result;   
    }

    /**
     * Send data from socket
     * 
     * @param string $buffer Buffer
     * @param integer $len Number of bytes to send
     * @param integer $flags Flags
     * @return integer|object Number of bytes sent on success, error or warning on failure
     * @access public
     */
                         
    function send($buff,$len,$flags) 
    {
        $result = socket_send($this->socket,$buf,$len,$flags);
        if ($result === false) {
            return self::raiseError(null,null,null,false);
        }
        return $result;
    }

    /**
     * Receive data from socket
     * 
     * @param string $buffer Buffer
     * @param integer $len Number of bytes to get
     * @param integer $flags Flags
     * @return integer|object Number of bytes received on success, error or warning on failure
     * @access public
     */
 
    function recv(&$buff, $len,$flags) 
    {
        $result = socket_recv($this->_socket,$buf,$len,$flags);
        if ($result === false) {
            return self::raiseError(null,null,null,false);
        }
        return $result;
    }

    /**
     * Send data from socket even if not connection oriented
     * 
     * @param string $buffer Buffer
     * @param integer $len Number of bytes to send
     * @param integer $flags Flags
     * @param string $addr Network address
     * @param integer $port Network port      
     * @return integer|object Number of bytes sent on success, error or warning on failure
     * @access public
     */
 
    function sendTo($buff,$len,$flags,$addr,$port = 0) 
    {
        $result = socket_sendto($this->socket,$buf,$len,$flags,$addr,$port);
        if ($result === false) {
            return self::raiseError(null,null,null,false);
        }
        return $result;
    }    

    /**
     * Receive data from socket even if not connection oriented
     * 
     * @param string $buffer Buffer
     * @param integer $len Number of bytes to send
     * @param integer $flags Flags
     * @param string $addr Network address
     * @param integer $port Network port      
     * @return integer|object Number of bytes received on success, error or warning on failure
     * @access public
     */
 
    function recvFrom(&$buf,$len,$flags,&$name, &$port) 
    {  
        $result = socket_recvfrom($this->_socket,$buf,$len,$flags,$name);
        if ($result === false) {
            return self::raiseError(null,null,null,false);
        }
        return $result;
    }

    /**
     * Generate a socket error
     *    
     * @param string $errmsg Error message
     * @param string $server Service name
     * @param integer $errno Error code
     * @param boolean Throw error
     * @return object Error
     * @access public   
     */

    function raiseError($errmsg = null,$server = null, $errno = null, $throw = true) 
    {
 
        if ((!empty($errno)) || (!empty($errmsg))) {
            $this->setError($errmsg);
            return VWP::raiseError($errmsg,$server,$errno,$throw);
        }
        $args = '';
        if (is_array($this->_arguments)) {
            $args = ' (' . implode(',',$this->_arguments) . ')';
        }   
        $this->_errno = socket_last_error($this->_socket);
        $this->_errmsg = socket_strerror($this->_errno) . $args;
        $this->setError($this->_errmsg);
        return VWP::raiseError($this->_errmsg,"SOCKET",$this->_errno,false);
    }

    /**
     * Class constructor
     * 
     * @param integer $domain Socket domain
     * @param integer $type Socket type
     * @param integer $protocol Socket protocol
     * @param boolean $create Create socket
     * @access public   
     */
                          
    function __construct($domain = AF_INET, $type = SOCK_STREAM, $protocol = SOL_TCP,$create = true) 
    {

        $this->_domain = $domain;
        $this->_type = $type;
        $this->_protocol = $protocol;
  
        if (is_string($this->_protocol)) 
        {
            $this->_protocol = getprotobyname($name);
        }
  
        if ($create) {     
            $this->_socket = socket_create($this->_domain  ,$this->_type  ,$this->_protocol);   
            if (!$this->_socket) {
                self::raiseError();
            }  
        }
    }
    
    // end class VSocket
} 
