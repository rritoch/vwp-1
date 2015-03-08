<?php

/**
 * Administrator authentication
 *  
 * This file provides administrator authentication         
 * 
 * @package VWP.User
 * @subpackage Events.Auth  
 * @author Ralph Ritoch 
 * @copyright Ralph Ritoch <rritoch@gmail.com>
 * @link http://www.vnetpublishing.com    
 */

VWP::RequireLibrary('vwp.sys.events');

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

class AdminEventAuth extends VEvent 
{

    /**
     * Handle Authenticate event
     * 
     * @param array $credentials User credenitals
     * @param mixed $result Authentication result
     * @return mixed Authentication result
     */
             
    function onAuthenticate($credentials,&$result) {
        global $users;

        $passwdfile = VPATH_BASE.DS.'etc'.DS.'passwd.php';
  
        $vfile =& v()->filesystem()->file();
        if (!$vfile->exists($passwdfile)) {
            $result = VWP::raiseWarning("No users defined!");
        }

        if ((!isset($credentials["username"])) ||
          (!isset($credentials["username"]))) {
            return VWP::raiseWarning("Invalid username or password!",null,null,false);
        }
        
        if (isset($users)) {
            $old_users = $users;
        }
    
        $vfile =& v()->filesystem()->file();
  
        if ($vfile->exists($passwdfile)) {
            require($passwdfile);
        } else {
            $users = array();
        }
        $username = $credentials["username"];
  
        $pass = $credentials["password"];
        if (isset($users[$username])) {
            $userinfo = $users[$username];
            if (isset($userinfo['password'])) {
               $match = $userinfo["password"];
               if ($match == $pass) {
                   $result = true;
               } elseif (function_exists('md5') && (md5($pass) == $match)) {
                   $result = true;
               } else {
                   $result = VWP::raiseWarning("Invalid username or password!",null,null,false);   
               }
            } else {
                $result = VWP::raiseWarning("Incomplete administrator account, missing password! If you are able to login please update the password in your user profile, otherwise another administrator can set a new password for you.",null,null,false);   	
            }   
        } else {
            $result = VWP::raiseWarning("Invalid username or password!",null,null,false);
        }  
        return $result; 
    }

} // end class