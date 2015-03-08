<?php

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

VWP::RequireLibrary('vwp.sys.events');

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

class AdminEventUser extends VEvent {
 
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
    return false;
   } 
   
   if (!$userinfo["_admin"]) {
    return false;
   }
   
   $result = array('USER','ADMINISTRATOR');
   return $result;   
  }
    
 /**
  * Handle Save Event
  * 
  * @param array $credentials User credentials
  * @param mixed $result Result
  * @access public
  */
             
 function onSave(&$credentials,&$result) {
  global $users;
    
  if ((isset($credentials["domain"])) && ($credentials["domain"] !== null)) {
   return false;
  }
  
  if (isset($users)) {
   $old_users = $users;
  }
  
  $passwdfile = VPATH_BASE.DS.'etc'.DS.'passwd.php';
  
  $vfile =& v()->filesystem()->file();
  
  if ($vfile->exists($passwdfile)) {
   require($passwdfile);
  } else {
   $users = array();
  }
  
  $userinfo = $credentials;
  if (function_exists('md5')) {
   if (isset($userinfo["password"])) {
    if (strlen($userinfo["password"]) != 32) {
     $userinfo["password"] = md5($userinfo["password"]);
    }
   }
  }
  $out = array();
  if (isset($users[$credentials["username"]])) {
   $out = $users[$credentials["username"]];
  }
  
  foreach($userinfo as $key=>$val) {
      if ($key == 'meta') {
          $out[$key] = serialize($val);   	
      } else {
          $out[$key] = $val;
      }
  }
  
  foreach($out as $key=>$val) {
  	  if ($key == 'meta') {
  	      $credentials[$key] = unserialize($val);
  	  } else {
          $credentials[$key] = $val;
  	  }
  }
  
  if ($credentials["_admin"]) {
   $users[$credentials["username"]] = $credentials;  
  } else {
   unset($users[$credentials["username"]]);
  }
    
  // save
  $nl = "\r\n";
  
  $out =  '<' . '?php global $users;' . $nl
         .' $users = array(' . $nl;
  foreach($users as $uid=>$uinfo) {
   $out .= '  "' . addslashes($uid) . '"=> array('.$nl;
   foreach($uinfo as $key=>$val) {
    if ($key == 'meta' && is_array($val)) {
    	$val = serialize($val);
    }
    if (is_string($val)) {
     $out .= '   "' . addslashes($key) . '"=>"' . addslashes($val) . '",'.$nl;
    } elseif ($val === true) {
     $out .= '   "' . addslashes($key) . '"=> true ,'.$nl;
    } elseif ($val === false) {
      $out .= '   "' . addslashes($key) . '"=> false ,'.$nl;
    } elseif (is_numeric($val)) {
     $out .= '   "' . addslashes($key) . '"=> ' . $val . ','.$nl;
    } else {
     $out .= '   "' . addslashes($key) . '"=> null ,'.$nl;
    }
   }   
   $out .= '  ),'.$nl;
  }       
  $out .= ' );' . $nl;
  
  $result = $vfile->write($passwdfile,$out);
  if (!VWP::isWarning($result)) {
   $result = true;
  }
  
  if (isset($old_users)) {
   $users = $old_users;
  } else {
   unset($users);
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
             
 function onDelete(&$credentials,&$result) {
  global $users;
    
  if ((isset($credentials["domain"])) && ($credentials["domain"] !== null)) {
   return false;
  }
  
  if (isset($users)) {
   $old_users = $users;
  }
  
  $passwdfile = VPATH_BASE.DS.'etc'.DS.'passwd.php';
  
  $vfile =& v()->filesystem()->file();
  
  if ($vfile->exists($passwdfile)) {
   require($passwdfile);
  } else {
   $users = array();
  }
    
  if (isset($users[$credentials["username"]])) {
      unset($users[$credentials["username"]]);
  }
      
  // save
  $nl = "\r\n";
  
  $out =  '<' . '?php global $users;' . $nl
         .' $users = array(' . $nl;
  foreach($users as $uid=>$uinfo) {
   $out .= '  "' . addslashes($uid) . '"=> array('.$nl;
   foreach($uinfo as $key=>$val) {
    if ($key == 'meta' && is_array($val)) {
    	$val = serialize($val);
    }
    if (is_string($val)) {
     $out .= '   "' . addslashes($key) . '"=>"' . addslashes($val) . '",'.$nl;
    } elseif ($val === true) {
     $out .= '   "' . addslashes($key) . '"=> true ,'.$nl;
    } elseif ($val === false) {
      $out .= '   "' . addslashes($key) . '"=> false ,'.$nl;
    } elseif (is_numeric($val)) {
     $out .= '   "' . addslashes($key) . '"=> ' . $val . ','.$nl;
    } else {
     $out .= '   "' . addslashes($key) . '"=> null ,'.$nl;
    }
   }   
   $out .= '  ),'.$nl;
  }       
  $out .= ' );' . $nl;
  
  $result = $vfile->write($passwdfile,$out);
  if (!VWP::isWarning($result)) {
   $result = true;
  }
  
  if (isset($old_users)) {
   $users = $old_users;
  } else {
   unset($users);
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
  global $users;
  

  
  if ((isset($credentials["domain"])) && ($credentials["domain"] !== null)) {
   return false;
  }
    
  if (isset($users)) {
   $old_users = $users;
  }
  
  $passwdfile = VPATH_BASE.DS.'etc'.DS.'passwd.php';
  
  $vfile =& v()->filesystem()->file();
  
  if ($vfile->exists($passwdfile)) {
   require($passwdfile);
  } else {
   $users = array();
  }
  
  if (!isset($credentials["username"])) {
      if (!isset($credentials["email"])) {
          return false;
      }
       $ulist = array();
       foreach($users as $uname=>$data) {
       	   if ($data['email'] == $credentials["email"]) {
               $ulist[] = $uname;	
       	   }
       }
       if (count($ulist) < 1) {
           return false;
       }
       if (!is_array($result)) {
           $result = array();
       }
       $result = array_merge($ulist);
       return $result;
  }
  
  if (isset($users[$credentials["username"]])) {
       $result = $users[$credentials["username"]];
       if (isset($result['meta'])) {       	         	         	 
          $result['meta'] = unserialize($result['meta']);
          if (!is_array($result['meta'])) {
          	 $result['meta'] = array();
          }
       }             
  }
  
  if (isset($old_users)) {
      $users = $old_users;
  } else {
      unset($users);
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

  $admin = 0;
  $curUser =& VUser::getCurrent();
  
  if (isset($credentials["_admin"])) {   
   if ($curUser->isAdmin()) {
    $admin = $credentials["_admin"];
   }
  }
  
  $vfile =& v()->filesystem()->file();
  if (!$vfile->exists(VPATH_BASE.DS.'etc'.DS.'passwd.php')) {
   $admin = 1;
  }
  
  if ($admin != 1) {
   return VWP::raiseWarning("Access denied!",null,null,false);  
  }
  $credentials["_admin"] = $admin;
  
  $newUser = new VUser;  
  $newUser->setProperties($credentials);
  return $newUser->save();  
 }


} // end class