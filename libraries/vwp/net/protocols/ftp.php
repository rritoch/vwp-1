<?php

/**
 * Virtual Web Platform - FTP Protocol (RFC-959)
 *  
 * This file provides the FTP protocol API.   
 * 
 * @package VWP
 * @subpackage Libraries.Networking.Protocols  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

/**
 * Require Telnet Protocol Support
 */

VWP::RequireLibrary('vwp.net.protocols.telnet');

/**
 * Require FTP protocol support
 */
VWP::RequireLibrary('vwp.net.protocols.ftp.reply');

/**
 * Virtual Web Platform - FTP Protocol (RFC-959)
 *  
 * This class provides the FTP protocol API.   
 * 
 * @package VWP
 * @subpackage Libraries.Networking.Protocols  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License 
 */

 
 class VFtp extends VTelnet 
 {

     /**
      * Get reply
      *    
      * @return object FTP Reply on success, error or warning on failure
      * @access public
      */    
           
     function getReply() 
     {
         $response = new VFtpReply();   
         $message = array('','','');
   
         $done = false;
         while(!$done) {
             $line = $this->readLine();
             if ($this->isError($line)) {
                 return $line;
             }
             $message = $response->write($line);    
             $reply_code = $response->getReplyCode();        
             if (($message[0] == $reply_code) && ($message[1] == ' ')) {    
                 $done = true;
             }    
         }
   
         $level = $response->getReplyLevel();
         if ($level > 3) {
             if ($level > 4) {
                 $response = $this->raiseError($response->toString(),get_class($this),$response->getReplyCode(),false);
             } else {
                 $response = $this->raiseWarning($response->toString(),get_class($this),$response->getReplyCode(),false);
             }     
         }
      
         return $response;
     }
  
     /**
      * Send raw FTP command
      * 
      * @param string $cmd FTP Command
      * @access public
      */              
  
     function sendCommand($cmd) 
     {   
   
         $result = $this->write($cmd . "\r\n",strlen($cmd) + 2);
         if ($this->isError($result)) {
             return $result;
         }
         $response = $this->getReply();
   
         return $response;
     }
 
     /**
      * FTP (PASS)
      * 
      * @param string $password Password
      * @return object FTP Reply on success, error or warning on failure
      * @access public
      */
                 
     function pass($password = "ftp@") 
     {
         if (empty($password)) {
             $password = "ftp@";
         } 
         $cmd = "PASS $password";
         return $this->sendCommand($cmd);
     }

     /**
      * FTP (PWD)
      * 
      * @return object FTP Reply on success, error or warning on failure
      * @access public
      */
  
     function pwd() 
     {
         $cmd = "PWD";
         return $this->sendCommand($cmd);  
     }

     /**
      * FTP (PASV)
      * 
      * @return object FTP Reply on success, error or warning on failure
      * @access public
      */
     
     function pasv() 
     {
         $cmd = "PASV";
         return $this->sendCommand($cmd);  
     }

     /**
      * FTP (PORT)
      * 
      * @param string $host Network host
      * @param integer $port Network port   
      * @return object FTP Reply on success, error or warning on failure
      * @access public
      */
     
     function port($host,$port) 
     {
         $port = $port + 0;
         if ($port < 1) {
             return $this->raiseError("Invalid port!","VFTP",null,false);
         }
  
         $ip = explode(".",$host);
         if (count($ip) != 4) {
             return $this->raiseError("Invalid IP Address!","VFTP",null,false);   
         }

         foreach($ip as $p) {
             if (strlen($p) < 1) {
                 return $this->raiseError("Invalid IP Address!","VFTP",null,false);    
             }
             $np = preg_replace("#[0-9]#","",$p);
             if (strlen($np) > 0) {
                 return $this->raiseError("Invalid IP Address!","VFTP",null,false);    
             }
         }
   
         $p2 = $port % 256;
         $p1 = ($port - $p2)/256;
   
         if ($p1 > 255) {   
             return $this->raiseError("Invalid port!","VFTP",null,false);
         }
   
         array_push($ip,$p1);
         array_push($ip,$p2);
   
         $cmd = "PORT " . implode(",",$ip);
         return $this->sendCommand($cmd);   
     }

     /**
      * FTP (USER)
      * 
      * @param string $username Username   
      * @return object FTP Reply on success, error or warning on failure
      */
  
     function user($username = "ftp") 
     {
         if (empty($username)) {
             $username = "ftp";
         }
         $cmd = "USER $username";
         return $this->sendCommand($cmd);
     }

     /**
      * FTP (RETR)
      * 
      * @param string $path Remote filename
      * @return object FTP Reply on success, error or warning on failure
      */
                 
     function retr($file) 
     {
         $cmd = "RETR $file";   
         return $this->sendCommand($cmd);
     }  
  
     /**
      * FTP (STOR)
      * 
      * @param string $file Remote filename   
      * @return object FTP Reply on success, error or warning on failure
      * @access public
      */
 
     function stor($file) 
     {
         $cmd = "STOR $file";   
         $result = $this->sendCommand($cmd);
         return $result;    
     }

     /**
      * FTP (CWD)
      * 
      * @param string $path Remote directory   
      * @return object FTP Reply on success, error or warning on failure
      * @access public
      */
  
     function cwd($path) {   
         $cmd = "CWD $path";   
         $result = $this->sendCommand($cmd);
         return $result;
     }

     /**
      * FTP (RNFR)
      * 
      * @param string $path Remote file or directory   
      * @return object FTP Reply on success, error or warning on failure
      * @access public
      */
  
     function rnfr($path) 
     {   
         $cmd = "RNFR $path";   
         $result = $this->sendCommand($cmd);
         return $result;
     }
  
     /**
      * FTP (RNTO)
      * 
      * @param string $path Remote file or directory   
      * @return object FTP Reply on success, error or warning on failure
      */
  
     function rnto($path) 
     {   
         $cmd = "RNTO $path";   
         $result = $this->sendCommand($cmd);
         return $result;
     }    

     /**
      * FTP (MKD)
      * 
      * @param string $path Remote directory   
      * @return object FTP Reply on success, error or warning on failure
      */

     function mkd($path) 
     {
         $cmd = "MKD " . $path;
         $result = $this->sendCommand($cmd);
         return $result;
     }
 
     /**
      * FTP (RMD)
      * 
      * @param string $path Remote directory   
      * @return object FTP Reply on success, error or warning on failure
      */
 
     function rmd($path) 
     {
         $cmd = "RMD $path";   
         $result = $this->sendCommand($cmd);
         return $result;  
     }

     /**
      * FTP (DELE)
      * 
      * @param string $file Remote filename   
      * @return object FTP Reply on success, error or warning on failure
      */

     function dele($file) 
     {
         $cmd = "DELE $file";   
         $result = $this->sendCommand($cmd);
         return $result;  
     }
  
     /**
      * FTP (LIST)
      * 
      * @param string $path Remote filename
      * @return object FTP Reply on success, error or warning on failure
      */
                 
     function doList($path = null) 
     {
         if (empty($path)) {
             $cmd = "LIST";
         } else {
             $cmd = "LIST $path";
         }
         return $this->sendCommand($cmd);
     }

     /**
      * FTP (TYPE)
      * 
      * @param string $type Transfer type
      * @return object FTP Reply on success, error or warning on failure
      */
    
     function doType($type) 
     {
         $cmd = "TYPE $type";
         return $this->sendCommand($cmd);  
     }

     /**
      * FTP (QUIT)
      * 
      * @return object FTP Reply on success, error or warning on failure
      * @access public
      */
  
     function quit() 
     {
         $cmd = "QUIT";
         return $this->sendCommand($cmd);
     } 

     /**
      * FTP (CHMOD)
      * 
      * Note: Provided by UNIX platforms, not part of the RFC
      * 
      * @param string $file File or directory
      * @param integer $permission Permissions
      * @access public 
      */
  
     function chmod($file,$permission) 
     {
  	     $cmd = "CHMOD " . decoct($permission) . " " . $file;
         return $this->sendCommand($cmd);  
     }
     
     // end class VFtp
 } 
