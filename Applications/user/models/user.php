<?php

/**
 * User Model 
 *  
 * @package    VWP.User
 * @subpackage Widgets
 * @author Ralph Ritoch <rritoch@gmail.com> 
 * @copyright (c) Ralph Ritoch - All rights reserved
 * @link http://www.vnetpublishing.com 
 */

VWP::RequireLibrary("vwp.model");
VWP::RequireLibrary('vwp.net');

VNet::RequireClient('smtp');

/**
 * User Model 
 *  
 * @package    VWP.User
 * @subpackage Widgets
 * @author Ralph Ritoch <rritoch@gmail.com> 
 * @copyright (c) Ralph Ritoch - All rights reserved
 * @link http://www.vnetpublishing.com 
 */

class User_Model_User extends VModel 
{

	/**
	 * Find users by email
	 *
	 * @param unknown_type $email
	 */
	
	function findUsersByEmail($email) 
	{
		return VUser::getUsernamesByEmail($email);
	}
	
	/**
	 * Send Reminder Email
	 * 
	 * @param unknown_type $email
	 */
	
	function sendReminder($email) 
	{
		
       	$can_email = $this->canEmail();
        	        	        	       	
       	if ($can_email) {
        	
       		$smtpd = new VSMTPClient;
        		
       		$settings = $this->getConfig();
        		        		        		
       		// send welcome email
        		
       		$result = $smtpd->smtp_connect($settings['smtp_host'],$settings['smtp_username'],$settings['smtp_password'],$settings['smtp_port']);
        		
       		if (!VWP::isWarning($result)) {
        			
       		    $smtpd->set_from($email->from[1],$email->from[0]);
       		    $smtpd->set_to($email->to[1],$email->to[0]);        		
       		    $smtpd->set_text_message($email->text);
        		    
       		    $smtpd->set_html_message($email->html);
       		    $smtpd->set_subject($email->subject);
        		    
       		    $result = $smtpd->send_message();        		        
       		    $smtpd->smtp_disconnect();        		        
       		    $smtpd->smtp_flush();        		        
       		}
       	} else {
        	$result = VWP::raiseWarning('EMail client not configured!',__CLASS__,null,false);
       	}		
		return $result;
	}
	
	/**
	 * Get Site Name
	 */
	
	function getSiteName() 
	{		
		$cfg = new VConfig;
		return $cfg->site_name;
	}
	
	/**
	 * Generate Confirmation Code
	 * 
	 * @return string Confirmation code
	 * @access public
	 */
	
	function generateConfirmationCode($userinfo) 
	{
	   $t = ''.time();	   
	   if (function_exists('microtime')) {
	       $u = explode(' ',''.microtime());
	       $u = ''.$u[0];
	   } else {
	   		   	   
	   	   $seed = time() + rand(3,1001);
	   	   	   	   	   	   
	   	   if (function_exists('memory_get_usage')) {
	   	   	   $u = str_repeat('*',rand(3,1001));
	   	       $seed = ($seed/2.0) + (memory_get_usage()/2.0);
	   	   }
	   	   
	   	   srand(abs(floor($seed)));
	   	   	   	   
	       $u = str_repeat('*',rand(2,16));	       
	   }
	   
	   $e = $userinfo['email'];	   
	   $code = md5(implode(':',array($t,$u,$e)));	   
	   return $code;
	}
	
    /**
     * Clean registration variables
     * 
     * @param array $register Registration variables
     * @access public
     */
         
    function cleanRegisterVars(&$register) 
    {
        $required = array("name","username","password","email","confirm_password");  
        foreach($required as $key) {
            if (!isset($register[$key])) {
                $register[$key] = '';
            }
        }  
    }
 
 
 function getConfigKey() {
  return "SOFTWARE\\VNetPublishing\\User\\Config";
 }
 
 /**
  * Get configuration settings
  * 
  * @return array|object Configuration settings on success, error or warning otherwise
  */       
 
 function getConfig() {
  $localMachine = & Registry::LocalMachine();
  
  $result = Registry::RegOpenKeyEx($localMachine,
                        self::getConfigKey(),
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
     * Get current user settings
     * 
     * @return array User settings
     * @access public
     */
 
    function getSettings() 
    {
 	    $user =& v()->shell()->user();
 	    $userinfo = array();
 	
        $required = array("name","email"); 	

        foreach($required as $key) {
    	    if (isset($user->$key)) {
    		    $userinfo[$key] = $user->$key;
    	    } else {
    		    $userinfo[$key] = '';
    	    }
        }
    
        $clr = array("password","confirm_password");
    
        foreach($clr as $key) {
   	        $userinfo[$key] = '';    	    
        }
    
        return $userinfo;
    }
 
    /**
     * Update user settings
     * 
     * @param array $settings
     * @access public
     */
    
    function updateSettings($settings) {
    	$user =& v()->shell()->user();
    	
    	if (isset($settings['name'])) {
    		$user->name = $settings['name'];
    	}

      	if (isset($settings['email'])) {
    		$user->email = $settings['email'];
    	}
    	    	
    	if (isset($settings['password']) && (!empty($settings['password']))) {
            $user->password = $settings['password'];    		
    	}
    	
    	return $user->save();
    }
    
    function requireConfirmation() {
    	return true;
    }
    
    /**
     * Can Email Flag
     * 
     * @return boolean True if can email
     * @access public
     */
    
    function canEmail() 
    {
        $settings = $this->getConfig();
        if (VWP::isWarning($settings)) {
        	return false;
        }

        $check = array("smtp_host","smtp_port");
        
        foreach($check as $ck) {
        	if (!isset($settings[$ck])) {
        		return false;
        	}
        	if (empty($settings[$ck])) {
        		return false;
        	} 
        }
        return true;
    }
    
    /**
     * Confirm email address
     * 
     * @param string $username Username
     * @param string $code Code     
     * @access public
     */
    
    function confirmEmail($username,$code) 
    {
    	
    	$user =& VUser::getUserByUsername($username);    	    	
    	if (VWP::isWarning($user)) {
    		return $user;
    	}    	
    	return $user->confirmEmail($code);
    }
    
    
    /**
     * Reset password
     */
    
    function resetPassword($username,$password) 
    {

    	$u =& VUser::getUserByUsername($username);
    	
    	$userinfo = $u->getProperties();
    	$userinfo['password'] = $password;
    	$response = VEvent::dispatch_event("user","Validate",$userinfo); 
  
        foreach($response["trace"] as $r) {
            if (VWP::isWarning($r["result"])) {
                return $r["result"];
            }
        }

        $u->password = $password;
        $u->unsetMeta('auth_code','user:reset');
        return $u->save();        
    }
    
    /**
     * Register a new user
     * 
     * @return true|object True on success, error or warning on failure
     * @access public  
     */
         
   function register($userinfo,$welcome_email,$confirm_email) {
    
   	    
        $response = VEvent::dispatch_event("user","Find",$userinfo);
        $result = $response["result"];

        if (is_array($result)) {
            return VWP::raiseWarning("Username in use!",get_class($this).":register",null,false);  
        }

        $response = VEvent::dispatch_event("user","Validate",$userinfo); 
  
        foreach($response["trace"] as $r) {
            if (VWP::isWarning($r["result"])) {
                return $r["result"];
            }
        } 
 
        $response = VEvent::dispatch_event("user","Register",$userinfo);

        $ctr = 0;
        foreach($response["trace"] as $r) {
            if (!VWP::isWarning($r["result"])) {
                $ctr++;
            }  
        }
           	   	       	    
        if ($ctr > 0) {
        	$can_email = $this->canEmail();
        	        	        	       	
        	if ($can_email) {
        	
        		$smtpd = new VSMTPClient;
        		
        		$settings = $this->getConfig();
        		        		        		
        		// send welcome email
        		
        		$result = $smtpd->smtp_connect($settings['smtp_host'],$settings['smtp_username'],$settings['smtp_password'],$settings['smtp_port']);
        		
        		if (!VWP::isWarning($result)) {
        			
        		    $smtpd->set_from($welcome_email->from[1],$welcome_email->from[0]);
        		    $smtpd->set_to($welcome_email->to[1],$welcome_email->to[0]);        		
        		    $smtpd->set_text_message($welcome_email->text);
        		    
        		    $smtpd->set_html_message($welcome_email->html);
        		    $smtpd->set_subject($welcome_email->subject);
        		    
        		    $result = $smtpd->send_message();        		        
        		    $smtpd->smtp_disconnect();        		        
        		    $smtpd->smtp_flush();        		        
        		}
        		
        		
        		if (VWP::isWarning($result)) {
        			$result->ethrow();
        		}
        		
        		if ($this->requireConfirmation()) {
        			// send confirmation email

        		    $result = $smtpd->smtp_connect($settings['smtp_host'],$settings['smtp_username'],$settings['smtp_password'],$settings['smtp_port']);
        		    if (!VWP::isWarning($result)) {
        		        $smtpd->set_from($confirm_email->from[1],$confirm_email->from[0]);
        		        $smtpd->set_to($confirm_email->to[1],$confirm_email->to[0]);        		
        		        $smtpd->set_text_message($confirm_email->text);
        		        $smtpd->set_html_message($confirm_email->html);
        		        $smtpd->set_subject($confirm_email->subject);
        		        $result = $smtpd->send_message();
        		        $smtpd->smtp_disconnect();
        		        $smtpd->smtp_flush();        			
        		    }
        		    
        		    if (VWP::isWarning($result)) {
        			    $result->ethrow();
        		    }        		    
        		    
        			// if failed NUKE cofirmation in user
        		}
        	}
        	
            return true;
        }

        $lastError = VWP::raiseError("Registration failed due to no response from registration system!",get_class($this).":register",500,false);
        foreach($response["trace"] as $r) {
            if (VWP::isWarning($r["result"])) {
                $lastError = $r["result"];
            }
        }
  
        return $lastError;
    }
 
    function resendConfirmationEmail($confirm_email) 
    {

   		if ($this->requireConfirmation()) {
   			// send confirmation email
   			
      		$smtpd = new VSMTPClient;        		
       		$settings = $this->getConfig();
   			
   		    $result = $smtpd->smtp_connect($settings['smtp_host'],$settings['smtp_username'],$settings['smtp_password'],$settings['smtp_port']);
   		    if (!VWP::isWarning($result)) {
   		        $smtpd->set_from($confirm_email->from[1],$confirm_email->from[0]);
   		        $smtpd->set_to($confirm_email->to[1],$confirm_email->to[0]);        		
   		        $smtpd->set_text_message($confirm_email->text);
   		        $smtpd->set_html_message($confirm_email->html);
   		        $smtpd->set_subject($confirm_email->subject);
   		        $result = $smtpd->send_message();
   		        $smtpd->smtp_disconnect();
   		        $smtpd->smtp_flush();        			
   		    }
        		            		       		    
   		} else {
   			$result = VWP::raiseWarning('Email confirmation is not required!',__CLASS__,null,false);
   		}    	   	
   		return $result;    	
    }
    
 function getCurrent() {
  $u = VUser::getCurrent();
  if (empty($u->username)) {
   return false;
  } else {
   $result = $u->getProperties();
   $result["_domain"] = $u->_domain;
   return $result;
  }
 }
} // end class