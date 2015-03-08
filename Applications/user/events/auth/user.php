<?php

/**
 * User authentication
 *  
 * This file provides user authentication         
 * 
 * @package VWP.User
 * @subpackage Events.Auth  
 * @author Ralph Ritoch 
 * @copyright Ralph Ritoch <rritoch@gmail.com>
 * @link http://www.vnetpublishing.com    
 */

VWP::RequireLibrary('vwp.sys.events');
VWP::RequireLibrary('vwp.sys.registry');


/**
 * Administrator authentication
 *  
 * This class provides administrator authentication         
 * 
 * @package VWP.User
 * @subpackage Events.Auth  
 * @author Ralph Ritoch 
 * @copyright Ralph Ritoch <rritoch@gmail.com>
 * @link http://www.vnetpublishing.com    
 */

class UserEventAuth extends VEvent {


 /**
  * Get configuration settings
  * 
  * @return array|object Configuration settings on success, error or warning otherwise
  */       
 
 function getConfig() {
  $localMachine = & Registry::LocalMachine();
  
  $result = Registry::RegOpenKeyEx($localMachine,
                         "SOFTWARE\\VNetPublishing\\User\\Config",
                        0,
                        0, //samDesired
                        $registryKey);
                         
  if (!VWP::isWarning($result)) {     
    $data = array();
    $idx = 0;
    $keylen = 255;
    $vallen = 255;
    $lptype = REG_SZ; 
    while (!VWP::isError($result = Registry::RegEnumValue(
                                     $registryKey,
                                     $idx++,
                                     $key,
                                     $keylen,
                                     0, // reserved
                                     $lpType,
                                     $val,
                                     $vallen)))  {
   if (!VWP::isWarning($result) || $result->errno = ERROR_MORE_DATA) {                                       
     $data[$key] = $val;
     $keylen = 255;
     $vallen = 255;  
    }
   }  
   Registry::RegCloseKey($registryKey);
   Registry::RegCloseKey($localMachine);
   return $data;
  }
  
  Registry::RegCloseKey($localMachine);
  return $result;
 
 }


 /**
  * Handle Authenticate event
  * 
  * @param array $credentials User credenitals
  * @param mixed $result Authentication result
  * @return mixed Authentication result
  */
             
 function onAuthenticate($credentials,&$result) {

  if ((!isset($credentials["username"])) ||
    (!isset($credentials["username"]))) {
   return VWP::raiseWarning("Invalid username or password!",null,null,false);
  }
          
  $config = $this->getConfig();
  if (VWP::isWarning($config)) {
   return VWP::raiseWarning("User accounts not configured!",get_class($this),null,false);
  }
  
  if (empty($config["user_database"])) {
   return VWP::raiseWarning("User accounts disabled!",get_class($this),null,false);
  }
  
  $dbi =& VDBI::getInstance();
  $table_name = $config["table_prefix"]."users";
  
  $table =& $dbi->getTable($table_name,$config["user_database"]); 
   
  if (VWP::isWarning($table)) {
   return $table;
  }
  
  $filter = $table->createFilter();
  $filter->addCondition("username","=",$credentials["username"]);
  
  $m = $table->getMatches($filter);
  
  $username = $credentials["username"];
  $pass = $credentials["password"];
  
  
  if (count($m) > 0) {
   $id = $m[0];
   $row = $table->getRow($id);
   $match = $row->fields["password"]["value"];
   
   if ($match == $pass) {
    $result = true;
   } elseif (function_exists('md5') && (md5($pass) == $match)) {
   	   if (isset($config['require_email_verification']) && ($config['require_email_verification'] > 0)) {
   	   	   $vc = $row->get('email_verification');
           $result = empty($vc) ? true : VWP::raiseWarning('Email address not confirmed. You must confirm your email address to access your account!',__CLASS__,null,false);
   	   } else {
   	   	   $result = true;
   	   }
   } else {
    $result = VWP::raiseWarning("Invalid username or password!",null,null,false);   
   }   
  } else {
   return VWP::raiseWarning("Invalid username or password!",null,null,false);  
  }  
    return $result; 
 }

} // end class