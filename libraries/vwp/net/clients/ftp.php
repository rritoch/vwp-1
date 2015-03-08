<?php

/**
 * Virtual Web Platform - FTP Client
 *  
 * This file provides the default FTP client.   
 * 
 * @package VWP
 * @subpackage Libraries.Networking.Clients
 * @todo Implement FTP Relay support
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License    
 */

/**
 * Require compatible FTP Protocol
 */

VWP::RequireLibrary("vwp.net.protocols.ftp");

/**
 * Require FTP Data Line Support
 */

VWP::RequireLibrary('vwp.net.clients.ftp.dataline');

/**
 * Virtual Web Platform - FTP Client
 *  
 * This class provides the default FTP Client.   
 * 
 * @package VWP
 * @subpackage Libraries.Networking.Clients  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

class VFTPClient extends VFtp 
{

    /**
     * @var object $banner FTP Banner reply
     * @access public
     */
         
    public $banner = null; 

    /**
     * @var string $username Username
     * @access private
     */
       
    protected $username = null;

    /**
     * @var string $password Password
     * @access private
     */
  
    protected $password = null;
 
    /**
     * @var boolean $connected Connected
     * @access private
     */
       
    protected $connected = false;
 
    /**
     * @var boolean $passive Passive mode
     * @access private
     */
       
    protected $passive = false;
 
    /**
     * Reserved for future use (ftp to ftp)
     *
     * @var boolean $relay Relay mode
     * @access private
     */
       
    protected $relay = false;

    /**
     * @var string $base Remote base path
     * @access private
     */
       
    protected $base = null;

    /**
     * Create a data line
     *   
     * <pre>
     *  Supported data line types:
     *  
     *  A : Ascii
     *  E : EBCDIC    
     *  I : Binary (Image)
     *    
     * </pre>
     *            
     * @param string $type Data line type
     * @return object Data line on success, error or warning otherwise
     * @access private  
     */
           
    protected function &_getDataLine($type) 
    {

        switch(strtoupper($type)) {
            case "AN":
            case "AT":
            case "AC":   
            case "A":
            case "ASCII":
                $type = "A";
                break;
            case "E":
            case "EN":
            case "ET":
            case "EC":    
            case "EBCDIC":
                $type = "E";
                break;
            default:
                $type = strtoupper($type);
                if (in_array(substr($type,strlen($type) - 1,1),array("N","T","C"))) {
                    $type = substr($type,strlen($type) - 1);     
                }
                if (preg_match('/^L[0-9]+$/',$type)) {
                    $type = strtoupper($type);
                } else {
                    $type = "I";
                }
        }
    
        $result = $this->doType($type);
        if (VWP::isWarning($result)) {
            return $result;
        }
  
        $line = new VFTPClientDataLine;
  
        if ($this->passive) {
  
            // pasv mode
   
            $reply = $this->pasv();
            if ($this->isWarning($reply)) {
                return $reply;
            }
            $reply = $line->setPassive($reply->getHost(),$reply->getPort());
            if (VWP::isError($reply)) {
                return $reply;
            }    
        } else {
   
            // non-pasv mode
   
            $reply = $line->setActive();
   
            if (VWP::isError($reply)) {
                return $reply;
            }
   
            $host = $line->getHost();
            if ($host == "0.0.0.0") {
   
                if (isset($_SERVER["SERVER_ADDR"])) {
                    $host = $_SERVER["SERVER_ADDR"];
                } else {
                    $host = $_SERVER["LOCAL_ADDR"];
                }     
            }
            
            $port = $line->getPort();
            $reply = $this->port($host,$port);    
            if ($this->isWarning($reply)) {
                return $reply;
            }           
        }

        return $line;
    }
        
 
    /**
     * FTP (MKD) Create directory
     * 
     * @param string $path Directory to create
     * @return object FTP Reply on success, error or warning on failure
     */
           
    function mkd($path) 
    {   
        $path = $this->getFullPath($path);   
        $result = parent::mkd($path);   
        return $result;  
    }

    /**
     * FTP (CWD) Change current directory
     * 
     * @param string $path Directory to change to
     * @return object FTP Reply on success, error or warning on failure
     */

    function cwd($path) 
    {   
        $path = $this->getFullPath($path);   
        $result = parent::cwd($path);
        return $result;  
    }

    /**
     * FTP (RNFR) Rename from
     * 
     * @param string $path Source directory or filename
     * @return object FTP Reply on success, error or warning on failure
     */

    function rnfr($path) 
    {   
        $path = $this->getFullPath($path);   
        $result = parent::rnfr($path);
        return $result;  
    } 
 
    /**
     * FTP (RNTO) Rename to
     * 
     * @param string $path Destination directory or filename
     * @return object FTP Reply on success, error or warning on failure
     */

    function rnto($path) 
    {   
        $path = $this->getFullPath($path);   
        $result = parent::rnto($path);
        return $result;  
    } 
 
    /**
     * FTP (RMD) Delete directory
     * 
     * @param string $path Directory to delete
     * @return object FTP Reply on success, error or warning on failure
     */

    function rmd($path) 
    {
        $path = $this->getFullPath($path);
        $result = parent::rmd($path);
        return $result;  
    }

    /**
     * FTP (DELE) Delete file
     * 
     * @param string $file File to delete
     * @return object FTP Reply on success, error or warning on failure
     */

    function dele($file) 
    {
        $file = $this->getFullPath($file);
        $result = parent::dele($file);
        return $result;  
    }
 
    /**
     * FTP (CHMOD) Change file or directory permissions
     * 
     * @param string $file File to change permissions on
     * @param integer $permission Permissions  
     * @return object FTP Reply on success, error or warning on failure
     * @access public
     */
   
    function chmod($file,$permission) 
    {  
        $file = $this->getFullPath($file);
        $result = parent::chmod($file,$permission);
        return $result;        
    }
 
    /**
     * FTP (LIST) List files in a directory
     * 
     * @param string $path Directory path
     * @return object FTP Reply on success, error or warning on failure      
     */
     
    function doList($path = null) 
    {
 
        if ($path !== null) {
            $path = $this->getFullPath($path);
        }
  
        $line = & $this->_getDataLine("ASCII");
  
        if (VWP::isWarning($line)) {
            return $line;
        }
  
        $reply = parent::doList($path);
        if ($this->isWarning($reply)) {
            return $reply;
        }
        $line->connect(null,null);   
        $data = $line->read(false);
        if (VWP::isWarning($data)) {
            return $data;
        }
  
        $final_reply = $this->getReply();
        if (VWP::isWarning($final_reply)) {
            return $final_reply;
        }
  
        $final_reply->setData($data);
        return $final_reply;
    }

 
    /**
     * FTP (RETR) Get remote file
     * 
     * @param string $filename Filename
     * @return object FTP Reply on success, error or warning on failure
     * @access public      
     */
     
    function retr($filename) 
    {
 
  
        $filename = $this->getFullPath($filename);
    
        $line = & $this->_getDataLine("I");
  
        if (VWP::isWarning($line)) {
            return $line;
        }
  
        $reply = parent::retr($filename);
        if ($this->isWarning($reply)) {
            return $reply;
        }
        $line->connect(null,null);   
        $data = $line->read(false);
        if (VWP::isWarning($data)) {
            return $data;
        }
  
        $final_reply = $this->getReply();
        if (VWP::isWarning($final_reply)) {
            return $final_reply;
        }
  
        $final_reply->setData($data);
        return $final_reply;
    } 
 
    /**
     * FTP (STOR) Upload file to remote FTP server
     *   
     * <pre>
     *  Supported transfer types:
     *   
     *   "A" : Ascii
     *   "E" : EBCDIC
     *   "I" : Binary (Image)
     *   
     * </pre>
     *                 
     * @param string $file Remote filename
     * @param string $buffer File data
     * @param string $type Transfer type    
     * @return object FTP Reply on success, error or warning on failure      
     */

    function stor($file,$buffer = '',$type = "I") 
    {
      
        $file = $this->getFullPath($file);
     
        $line = & $this->_getDataLine($type);
  
        if (VWP::isWarning($line)) {
            return $line;
        }
  
        $reply = parent::stor($file);
  
        if ($this->isWarning($reply)) {
            return $reply;
        }
        $line->connect(null,null);
    
        $l = VWP::setTimeLimit(0);  
        $data = $line->write($buffer);
        VWP::setTimeLimit($l);
  
        if (VWP::isWarning($data)) {
            return $data;
        }
        
        $line->close(true,1);
        $final_reply = $this->getReply();

        if (VWP::isWarning($final_reply)) {
            return $final_reply;
        }
     
        return $final_reply;
    }
 

    /**
     * Connect to FTP Server
     * 
     * @param string $host Server network address
     * @param integer $port Server network port
     * @return FTP login Reply on success, error or warning on failure  
     * @access public  
     */
            
    function connect($host,$port = 21) 
    {
        if (empty($port)) {
            $port = 21;
        }  
        $result = parent::connect($host,$port);
        if (!VWP::isError($result)) {
            $this->connected = true;
            $this->banner = $this->getReply();    
   
            if (!VWP::isError($this->banner)) {
                $credentials = array("username"=>$this->username,"password"=>$this->password);
                $result = $this->login($credentials);
            }
        }  
        return $result;   
    }
 
    /**
     * Disconnect from FTP server
     * 
     * @return true|object True or FTP reply on success, error or warning on failure
     * @access public
     */
         
    function disconnect() 
    {
        $result = true;
        if ($this->connected) {
            $result = $this->quit(); 
        }
        return $result;
    }
        
    /**
     * Get a FTP Client object
     *   
     * <pre>
     *  Object types:
     *    
     *   vfolder : FTP Folder    
     *   default : FTP Client
     * </pre>
     *             
     * @param string $class Object type
     * @return object Requested FTP Object
     * @access public  
     */         
 
    function &getInstance($class = null) 
    {
    	
        static $instance = array();
    
        if ($class === null) {
            $class = "new";
        }
  
        switch($class) {
            case "vfolder":     
                if (!isset($this->_helpers[$class])) {      
                    VWP::RequireLibrary("vwp.filesystem.ftp.folder");      
                    if (!class_exists('VFTPFolder')) {
                        $this->_helpers[$class] = VWP::raiseError("FTPClient","Filesystem FTP is missing folder support!");       
                    } else {           
                         $ftpfs =& VFilesystem::getInstance($this,'ftp');
                         $this->_helpers[$class] =& v()->filesystem($ftpfs)->folder();      
                    }
                }
               break;
            default:    
                break;
        }
 
        if (isset($this->_helpers[$class])) {
            return $this->_helpers[$class];
        }
        $ptr = count($instance);
        $instance[] = new VFTPClient;
        return $instance[$ptr];
    }
 
    /**
     * Set username
     * 
     * @param string $username username
     * @access public  
     */
           
    function setUsername($username) 
    {
        $this->username = $username;
    }

    /**
     * Set password
     * 
     * @param string $password Password
     * @access public  
     */
 
    function setPassword($password) 
    {
        $this->password = $password;
    }
    
    
    /**
     * Set remote base path
     *   
     * @param string $basePath Remote base path
     * @access public
     */
          
    function setBase($basePath) 
    {
        $this->base = $basePath;
    }

    /**
     * Set passive mode
     * 
     * @param boolean $passive Passive mode
     * @access public  
     */
         
    function setPassive($passive = true) 
    {
        $this->passive = $passive;
    }

    /**
     * Set relay mode
     *   
     * Reserved for future use
     *   
     * @param boolean $relay Relay mode
     * @access public  
     */

    function setRelay($relay = true) 
    {
        $this->relay = $relay;
    }

    /**
     * Convert remote path into a virtual path
     * 
     * @param string $path Remote path
     * @return string Virtual path
     */
           
    function cleanFullPath($path) 
    {
        if ($this->base === null) {
            return v()->filesystem()->path()->clean($path);
        }
        if (substr($path,0,strlen($this->base)) == $this->base) {
            $ret = v()->filesystem()->path()->clean(substr($path,strlen($base)),"/");
            if (substr($ret,0,1) != "/") {
                $ret = "/" . $ret;
            }
        } else {
            $ret = "/";
        }   
        return v()->filesystem()->path()->clean($ret);   
    }

    /**
     * Convert virtual path into a remote path
     * 
     * @param string $path Virtual path
     * @return string Remote path
     * @access public
     */
   
    function getFullPath($path) 
    {   
  
        $vpath =& v()->filesystem()->path();
  
        if ($this->base === null) {    
            return $vpath->clean($path,'/');
        }
  
        $nicepath = $vpath->clean($path,'/');
  
        if (substr($nicepath,0,1) == '/') {
             $nicepath = substr($nicepath,1);
        }
  
        $nicebase = $vpath->clean($this->base,'/');
        if (substr($nicebase,strlen($nicebase) - 1,1) == "/") {
            $nicebase = substr($nicebase,0,strlen($nicebase) - 1);
        }
  
        return $nicebase.'/'.$nicepath;   
    }
 

 
    /**
     * Login to FTP server
     * 
     * <pre>  
     * Credentials support two keys
     *  username : Login username
     *  password : Login password
     * </pre>
     *            
     * @param array $credentials Credentials
     * @return object FTP Reply on success, error or warning otherwise  
     * @access public
     */
           
    function login($credentials) 
    {
        if (isset($credentials["username"])) {
            $this->username = $credentials["username"];
        }

        if (isset($credentials["password"])) {
            $this->password = $credentials["password"];
        }   
     
        $response = $this->user($this->username);
  
        if ($this->isWarning($response)) {    
            return $response;
        }
  
        if ($response->getReplyCode() == 331) {   
            $response = $this->pass($this->password);
        }
     
        return $response;  
    }
      
    // end class VFTPClient
} 