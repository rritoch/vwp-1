<?php

/**
 * User accounts
 *  
 * This file provides user accounts         
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
 * Administrator user accounts
 *  
 * This file provides administrator user accounts         
 * 
 * @package VWP.User
 * @subpackage Events.Auth  
 * @author Ralph Ritoch 
 * @copyright Ralph Ritoch <rritoch@gmail.com>
 * @link http://www.vnetpublishing.com    
 */

class UserEventUser extends VEvent {
 
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
  * Handle Allow Event
  * 
  * @param array $options Arguments
  * @param mixed $result Result
  * @access public
  */
  
 function onAllow(&$options,&$result) {
   
  $usage = $options[0];
  $roles = $options[1];
  $R_Id = $options[2];
  $S_Id = $options[3];
  return false;
 }

 /**
  * Handle Deny Event
  * 
  * @param array $options Arguments
  * @param mixed $result Result
  * @access public
  */
 
 function onDeny(&$options,&$result) {   
  $usage = $options[0];
  $roles = $options[1];
  $R_Id = $options[2];
  $S_Id = $options[3];
  return false; 
 }


 /**
  * Handle GetRoles Event
  * 
  * @param array $credentials Credentials
  * @param mixed $result Result
  * @access public
  */ 

  function onGetRoles(&$credentials,&$result) {
    
   $userinfo = false;
   $cinfo = $credentials;
   
   if ((isset($cinfo["domain"])) && ($cinfo["domain"] !== null)) {
    return false;
   }
   
   $this->onFind($cinfo,$userinfo);
   if ($userinfo === false) {
    $result = array('GUEST');
   } else {
    $result = array('USER');
   } 
         
   return $result;   
  }

 /**
  * Handle Save Event
  * 
  * @param array $credentials User credentials
  * @param mixed $result Result
  * @access public
  */
             
 function onSave($credentials,&$result) {
        
  $config = $this->getConfig();
  if (VWP::isWarning($config)) {
   return VWP::raiseWarning("User accounts not configured!",get_class($this),null,false);
  }
  
  if (empty($config["user_database"])) {
   return VWP::raiseWarning("User accounts disabled!",get_class($this),null,false);
  }

  if (isset($credentials["_domain"])) {
   return false;
  }
    
  if (!isset($credentials["id"])) {
   $r = false;
   $r2 = $this->onFind($credentials,$r);
   if (is_array($r)) {
    $credentials["id"] = $r["id"];
   }      
  }
      
  $dbi =& VDBI::getInstance();
  $table_name = $config["table_prefix"]."users";
  
  $table =& $dbi->getTable($table_name,$config["user_database"]); 
  if (VWP::isWarning($table)) {
   return $table;
  }
  
  if (isset($credentials["id"])) {
   $row =& $table->getRow($credentials["id"]);  
  } else {
   $row =& $table->getRow(null);   
  }
        
  if (in_array("_admin",array_keys($credentials))) {
   if ($credentials["_admin"]) {
    $credentials["_admin"] = 1;
   } else {
    $credentials["_admin"] = 0;
   }   
  }

  if (function_exists('md5')) {
   if (in_array("password",array_keys($credentials))) {
    if (strlen($credentials["password"]) != 32) {
     $credentials["password"] = md5($credentials["password"]);
    }
   }
  }
  
  if (VWP::isWarning($row)) {
   $result = $row;
  } else {
   
   if (isset($credentials['meta'])) {
       $credentials['meta'] = serialize($credentials['meta']);
   } 	
  	
   foreach($credentials as $key=>$val) {
    if ($key != '_domain') {       
     $row->set($key,$val);
    }
   }
   
   $result = $row->save();   
  }
          
  return $result;
 }

 /**
  * Handle Delete Event
  * 
  * @param array $credentials User credentials
  * @param mixed $result Result
  * @access public
  */
             
 function onDelete($credentials,&$result) {
        
  $config = $this->getConfig();
  if (VWP::isWarning($config)) {
      return VWP::raiseWarning("User accounts not configured!",get_class($this),null,false);
  }
  
  if (empty($config["user_database"])) {
      return VWP::raiseWarning("User accounts disabled!",get_class($this),null,false);
  }

  if (isset($credentials["_domain"])) {
      return false;
  }
      
  $r = false;
  $r2 = $this->onFind($credentials,$r);
  if (is_array($r)) {
    $credentials["id"] = $r["id"];
  }      
  
      
  $dbi =& VDBI::getInstance();
  $table_name = $config["table_prefix"]."users";
  
  $table =& $dbi->getTable($table_name,$config["user_database"]); 
  if (VWP::isWarning($table)) {
   return $table;
  }
  
  if (isset($credentials["id"])) {
   $row =& $table->getRow($credentials["id"]);  
  }
        
  if (VWP::isWarning($row)) {
   $result = $row;
  } else {     
   $result = $row->delete();   
  }
          
  return $result;
 }
 
 
 
 /**
  * Handle Find User Event
  * 
  * @param array $credentials User credentials
  * @param mixed $result Result
  * @access public
  */
 
 function onFind($credentials,&$result) {

  if (!isset($credentials["username"])) {
   return false;
  }

  if ((isset($credentials["domain"])) && ($credentials["domain"] !== null)) {
   return false;
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
      
  if (count($m) > 0) {
   $id = $m[0];
   $row =& $table->getRow($id);
   if (VWP::isWarning($row)) {
    $result = $row;
   } else {
    $p = $row->getProperties();
    $result = array();
    foreach($p["fields"] as $name=>$info) {
     $result[$name] = $info["value"];
    } 
   }
  } else {
   // generic not found
   return false;
  }  
  
  if (isset($result['meta']) && is_string($result['meta'])) {
      $result['meta'] = unserialize($result['meta']);
  }
  
  return $result;  
 }

 /**
  * Handle Validate user settings event
  * 
  * @param array $credentials User credentials
  * @param mixed $result Result
  * @access public
  */

 function onValidate($credentials,&$result) {

  if ((isset($credentials["_domain"])) && ($credentials["_domain"] !== null)) {
   return false;
  }

  $result = true;
  
  $required = array("username"=>"username","password"=>"password","email"=>"email address");
  $min_length = array(
   "name"=>5,
   "username"=>3,
   "password"=>5
  );

  $max_length = array(   
   "password"=>32
  );
  
  foreach($required as $key=>$name) {
   if (!isset($credentials[$key])) {
    $result = VWP::raiseWarning("Missing " . $name . ".",get_class($this),null,false);
   } elseif (empty($credentials[$key])) {
    $result = VWP::raiseWarning("Missing " . $name . ".",get_class($this),null,false);   
   }
  }

  foreach($min_length as $key=>$val) {
   if (strlen($credentials[$key]) < $val) {
    $result = VWP::raiseWarning(ucfirst($key) . " must be at least $val characters!",get_class($this),null,false);
   }
  }

  foreach($max_length as $key=>$val) {
   if (strlen($credentials[$key]) > $val) {
    $result = VWP::raiseWarning(ucfirst($key) . " must be less than $val characters!",get_class($this),null,false);
   }
  }

  $initial = substr($credentials["username"],0,1);
  
  if (!preg_match('/[a-zA-Z]/',$initial)) {
   $result = VWP::raiseWarning("Username must start with a letter!",get_class($this),false);   
  }

  if (!preg_match('/^[a-zA-Z0-9_]+$/',$credentials["username"])) {
   $result = VWP::raiseWarning("Invalid characters in username, Only [a-z, 0-9, and _ (underscore)] allowed!",get_class($this),false);   
  }
    
  return $result;
 }

 /**
  * Handle user registration event
  * 
  * @param array $credentials User credentials
  * @param mixed $result Result
  * @access public
  */
 
 function onRegister($credentials,&$result) {

  if ((isset($credentials["_domain"])) && ($credentials["_domain"] !== null)) {
   return VWP::raiseWarning("Unable to register remote user!",get_class($this),null,false);
  }
 
  $test = false;
  $r = $this->onFind($credentials,$test);
  if ($test !== false) {
   return VWP::raiseWarning("Username in use!",get_class($this),null,false);
  }
  
  $test = false;
  $r = $this->onValidate($credentials,$test);
  if ($test !== true) {
   if (VWP::isWarning($test)) {
   	 return $test;
   }
   return VWP::raiseWarning("Invalid account settings!",get_class($this),null,false);
  }
  
  $curUser =& VUser::getCurrent();
  
  if (isset($credentials["_admin"])) {   
   if (!$curUser->isAdmin()) {
    unset($credentials["_admin"]);
   }
  }
  
  $nuser = new VUser;
  $nuser->setProperties($credentials);
  return $nuser->save();  
 }


} // end class
