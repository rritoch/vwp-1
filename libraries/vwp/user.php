<?php

/**
 * Virtual Web Platform - User Library
 *  
 * This file provides the base user interface        
 * 
 * @package VWP
 * @subpackage Libraries  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License   
 */


// Restricted access
class_exists('VWP') || die();

/**
 * Require System Events
 */
  
VWP::RequireLibrary("vwp.sys.events");

/**
 * Require System Callbacks
 */
 
VWP::RequireLibrary('vwp.sys.callback');

 
/**
 * Require Session Support
 */
  
VWP::RequireLibrary("vwp.session");

/**
 * Virtual Web Platform - User Library
 *  
 * This file provides the base user interface        
 * 
 * @package VWP
 * @subpackage Libraries  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

class VUser extends VObject 
{
   
    /**
     * Current user
     *    
     * @var array $_cur_user Current user
     * @access private
     */
          
    static $_cur_user = null;
   
   
    /**
     * SuID Stack
     *    
     * @var object $_cur_user Current user
     * @access private
     */
          
    static $_suid_stack = array();
   
    /**
     * Temporary nosave data
     *   
     * @var array $_extra Temporary nosave data   
     * @access public    
     */
        
    var $_extra = array();
    
    
    /**
     * Administrator status
     * 
     * @var integer $_admin Administrator status  
     * @access public    
     */
        
    var $_admin = false;
   
    /**
     * Username
     * 
     * @var string $username Username
     * @access public
     */
              
   
    var $username = null;
   
    /**
     * Password
     * 
     * @var string $password Password
     * @access public
     */
      
    var $password = null;
   
    /**
     * Email address
     * 
     * @var string $email
     * @access public
     */ 
    
    var $email = null;
   
    /**
     * Email Verification
     * 
     * @var string $email_verification Email Verification Code
     * @access public
     */
    
    public $email_verification = null;
    
    /**
     * User's Name
     * 
     * @var string $name
     * @access public
     */ 
    
    var $name = 'Guest';

    /**
     * Meta data
     * 
     * @var array $meta Meta data
     * @access public
     */    
    
    var $meta = array();
    
    /**
     * User's roles
     * 
     * @var array $_roles Assigned roles
     * @access public  
     */
         
    var $_roles = array('GUEST');
    
    /**
     * User's domain
     * 
     * @var string $_domain Domain
     * @access public  
     */   
    
    var $_domain = null;
    
    
    /**
     * User's Shell 
     * 
     * @var array $_shell
     * @access private
     */
     
    var $_shell;
    
    /**     
     * Registered callbacks
     * 
     * @var array Callbacks
     * @access private
     */
    
    var $_callbacks = array();
    
    /**
     * User Cache
     * 
     * @var array User Cache
     * @access public
     */
    
    static $_user_cache = array();
    
    /**
     * Test if this user is an administrator
     * 
     * @return boolean True if user is an administrator
     * @access public      
     */
        
    function isAdmin() 
    {
        if ($this->_admin) {
           return true;
        }
        return false;
    }
   
    /**
     * Check if a foreign user
     * 
     * @return boolean True if foreign
     * @access public     
     */
    
    function is_foreign() 
    {
        return empty($this->username) || (!empty($this->_domain)) ? true : false;	
    }
    
    /**
     * Test if user is allowed to access a feature
     * 
     * @param string $usage Usage note
     * @param string $R_Id Resource ID
     * @param array $S_Id Service ID      
     * @return boolean True if user is allowed access
     * @access public      
     */ 
    
    function allow($usage,$R_Id,$S_Id = null) 
    {
     
        // Clean Role List    
     
        $roles = array();
        if (in_array('USER',$this->_roles)) {
            foreach($this->_roles as $role) {
                if ($role != "GUEST") {
                    $roles[] = $role;
                }
            }  
        } else {
            $roles = $this->_roles;
            if (!in_array('GUEST',$roles)) {
                $roles[] = 'GUEST';
            }    
        }
   
        // Send Event
        $e_options = array($usage,$roles,$R_Id,$S_Id);  
        $response = VEvent::dispatch_event("user","Allow",$e_options);
     
        // Process Credentials
     
        if ($this->_admin) {
            return true;
        }
   
        if (in_array('ADMINISTRATOR',$roles)) {
            return true;
        }  
          
        foreach($response["trace"] as $ent) {
            $result = $ent["result"];
            if (is_array($result) && (count($result) > 0)) {
                return true;
            }
        }
           
        return false;
    }
   
    /**
     * Test if user is blocked from accessing a feature
     * 
     * @param string $usage Usage note
     * @param string $R_Id Resource ID
     * @param array $S_Id Service ID      
     * @return boolean True if user is blocked from accessing the feature
     * @access public      
     */ 
   
    function deny($usage,$R_Id,$S_Id = null) 
    {
   
        // Clean Role List
     
        $roles = array();
        if (in_array('USER',$this->_roles)) {
            foreach($this->_roles as $role) {
                if ($role != "GUEST") {
                    $roles[] = $role;
                   }
               }  
        } else {
            $roles = $this->_roles;
            if (!in_array('GUEST',$roles)) {
                $roles[] = 'GUEST';
            }    
        }
     
        // Send Event
   
        $e_options = array($usage,$roles,$R_Id,$S_Id);
     
        $response = VEvent::dispatch_event("user","Deny",$e_options);     
     
        // Process credentials
     
        if ($this->_admin) {
            return false;
        }
   
        if (in_array('ADMINISTRATOR',$roles)) {
            return false;
        }
     
        foreach($response["trace"] as $ent) {
            $result = $ent["result"];
            if (is_array($result) && (count($result) > 0)) {
                return true;
            }
        }    
     
        return false;
    }
    
    /**
     * Authenticate user with given credentials
     * 
     * @param array $credentials user credentials
     * @access public
     */
                
    function authenticate($credentials) 
    {
        $response = VEvent::dispatch_event("auth","Authenticate",$credentials);  
        $result = $response["result"];
     
        if (VWP::isWarning($result)) {
            $result->ethrow();
        } elseif ($result === false) {        	
            if (count($response["trace"]) < 1) {
                VWP::raiseWarning("No authentication system available, defaulting to Administrator login!",get_class($this).":authenticate",null,true);
                $this->_admin = true;
                if (isset($credentials["username"])) {
                    $this->username = $credentials["username"];
                } else {
                    $this->username = "Admin";
                }
                $this->_roles = array('USER','ADMINISTRATOR');    
            } else {
                VWP::raiseWarning("Authentication failed!",get_class($this).":authenticate",null,true);
            }   
        } else {
     
            if (!isset($credentials["C_Id"])) {
                VWP::addNotice("Authentication successful!");
            }
      
            $response = VEvent::dispatch_event("user","Find",$credentials);
                              
            $result = $response["result"];
      
            $this->_domain = null;   
            $this->setProperties($result);
      
            if (isset($credentials["C_Id"])) {
                $this->suid($credentials); // Network Auth!
                $C_Id = $credentials["C_Id"];
            } else {
                $C_Id = array(
                    "username"=>$this->username,
                    "domain"=>$this->_domain,
                );
                if ($this->_admin) {                
                    VWP::addNotice("Administrator access granted!");
                }                
            }
   
            $response = VEvent::dispatch_event("user","GetRoles",$C_Id);
   
            $this->_roles = array();
      
            foreach($response["trace"] as $ent) {
                $result = $ent["result"];
                if (is_array($result)) {
                    foreach($result as $role) {
                        if (!in_array($role,$this->_roles)) {
                            array_push($this->_roles,$role);
                        }
                    }
                }
            }
   
            if (!in_array('USER',$this->_roles)) {
                array_push($this->_roles,'USER');
            }
         
            if (!isset($credentials["C_Id"])) {
                $sess =& VSession::getInstance();
                $uvars = array(
                 "name",
                 "username",
                 "password",
                 "email",
                 "email_verification",
                 "meta",
                 "_admin",
                 "_domain",
                );
           
                foreach($uvars as $key) {   
                    $sess->set($key,$this->$key,"user");        
                }
            }               
        }
    }
   
    /**
     * Exit a SUID session          
     * 
     * @access public    
     */   
   
    function suid_exit() 
    {
        if (count(self::$_suid_stack) > 0) {
            $sess =& VSession::getInstance();
            
            VEvent::dispatch_event("suid","suid_exit");
            $olduser = array_pop(self::$_suid_stack);
            $this->setProperties(get_object_vars($olduser));
            
            self::updateSession();
        }
    }
    
    /**
     * Update Session
     *
     * @access public
     */
    
    public static function updateSession() {
        
    	$sess =& VSession::getInstance();
    	
    	if (!$sess->isLive()) {
    		return;
    	}
    	
    	// Store SUID Stack

        $suid_stack = array();
        foreach(self::$_suid_stack as $u) {
            array_push($suid_stack,$u->getProperties(false));
        }                        
        
        if (isset(self::$_cur_user[0]) && is_object(self::$_cur_user[0])) {
            $sess->set('_suid_stack',$suid_stack,"userstack");
        
            $uvars = array(
              "name",
              "username",
              "password",
              "email",
              "email_verification",
              "meta",
              "_admin",
              "_domain",
             );
             
            $cvars = get_object_vars(self::$_cur_user[0]);
            $ckeys = array_keys($cvars);
            
            foreach($uvars as $key) {
            	$store = null;     	   
                $sess->set($key,in_array($key,$ckeys) ? self::$_cur_user[0]->$key : null,"user");
                self::$_cur_user[0]->$key =& $sess->get($key,null,'user');                         
            }
        }  
    }
    
    /**
     * Start a SUID session
     * 
     * @param array $credentials user credentials
     * @return boolean True if authenticated, false otherwise  
     * @access public
     */
                
    public static function suid($credentials) 
    {
        // Place current user onto SUID stack

    	$oldUser = new VUser;
        $settings = get_object_vars(self::$_cur_user[0]);    	
        foreach($settings as $k=>$v) {
        	$oldUser->$k = $v;
        }    	
        array_push(self::$_suid_stack,$oldUser);
           
        // Proccess user change
        
        $newUser = new VUser;
        $newP = get_object_vars($newUser);
        unset($newP['_shell']); // don't change shell
        
        self::$_cur_user[0]->setProperties($newP);
        
        $newUser->_shell = self::$_cur_user[0]->getShellStack();
        $newP = get_object_vars($newUser);
        $regular_properties = array_keys($newP);
        
        // Remove non-standard settings
        $p = get_object_vars(self::$_cur_user[0]);
        foreach($p as $k=>$v) {
            if (!in_array($k,$regular_properties)) {
                unset(self::$_cur_user[0]->$k);
            }
        }
                
        // Protected credentials
        $cred_copy = $credentials;
     
        // Process SUID event listeners
        VEvent::dispatch_event("suid","suid",$cred_copy);
             
        if (empty($credentials)) {
        	self::updateSession();
            return false; // Fake Logout!
        }
        
        $response = VEvent::dispatch_event("auth","Authenticate",$credentials);  
        $result = $response["result"];
     
        if (VWP::isWarning($result)) {
            self::$_cur_user[0]->setError($result);
            $result->ethrow();            
            self::updateSession();                 
            return false;   
        } elseif ($result === false) {
            if (count($response["trace"]) < 1) {
                $err = VWP::raiseWarning("No authentication system available, defaulting to Administrator login!",'VUser::suid'.":authenticate",null,true);
                self::$_cur_user[0]->setError($err);    
                self::$_cur_user[0]->_admin = true;
                if (isset($credentials["username"])) {
                    self::$_cur_user[0]->username = $credentials["username"];
                } else {
                    self::$_cur_user[0]->username = "Admin";
                }
       
                // Set effective roles
                self::$_cur_user[0]->_roles = array('USER','ADMINISTRATOR');
              
            } else {
                self::$_cur_user[0]->_roles = array('GUEST');
                $err = VWP::raiseWarning("SUID Authentication failed!",'VUser::suid'.":authenticate",null,true);
                self::$_cur_user[0]->setError($err);
                self::updateSession();                
                return false;
            }   
        } else {
            VWP::addNotice("SUID Authentication successful!");
            $response = VEvent::dispatch_event("user","Find",$credentials);
            self::$_cur_user[0]->setError($response);
      
            $result = $response["result"];
            
               
            self::$_cur_user[0]->setProperties($result);
                        
            // Set effective roles
            if (isset($credentials["C_Id"])) {
                $C_Id = $credentials["C_Id"];
            } else {
                $C_Id = array(
                 "username"=>self::$_cur_user[0]->username,
                 "domain"=>self::$_cur_user[0]->_domain,
                );
            }
   
            $response = VEvent::dispatch_event("user","GetRoles",$C_Id);
   
            self::$_cur_user[0]->_roles = array();
      
            foreach($response["trace"] as $ent) {
                $result = $ent["result"];
                if (is_array($result)) {
                    foreach($result as $role) {
                        if (!in_array($role,self::$_cur_user[0]->_roles)) {
                            array_push(self::$_cur_user[0]->_roles,$role);
                        }
                    }
                }
            }
                       
            if (self::$_cur_user[0]->_admin) {       
                VWP::addNotice("Administrator access granted!");
            }
        }
     
        self::updateSession();     
        return true;
    }
   
    /**
     * Perform a callback with this users shell
     *      
     * @param object $callback Callback Object
     * @access public
     */
    
    function doCallback($callback) {
        $oldshell = $this->_shell;
        $this->_shell = array($callback->getShell());
        
        $cb = $callback->getCallback();
                
        if (
             is_array($cb) && 
             (count($cb) > 1) && 
             is_object($cb[0]) &&
             method_exists($cb[0],$cb[1])
             ) {
            // Object call
                    
            $func = $cb[1];        
            $cb[0]->$func();     
        } else {
            if (is_callable($cb)) {
                call_user_func($cb);
            }
        }  
        
        $this->_shell = $oldshell;
    }
    
    /**
     * Create a callback
     * 
     * @param string|array $action Requested callback action
     * @return array Callback action
     * @access public
     */
    
    function createCallback($action) {
        $idx = count($this->_callbacks);
        $this->_callbacks[$idx] = new VCallback($action);    
        return $this->_callbacks[$idx]->getAction();
    }
    
    /**
     * Get current user
     * 
     * @return VUser Current user
     * @todo Make logout more friendly, destroying session is overkill
     * @access public
     */         
     
    public static function &getCurrent() {          
        if (!is_array(self::$_cur_user)) {
           
            self::$_cur_user = array(new VUser);
            
            $auth = VEnv::getVar('auth',false);
            // Get session user
   
            $sess =& VSession::getInstance();
         
            $live_session = $sess->isLive();
            
            if ($live_session) {
                $uvars = array(
                 "name",
                 "username",
                 "password",
                 "email",
                 "email_verification",
                 "meta",
                 "_admin",
                 "_domain",
                );
       
                foreach($uvars as $key) {
                	if (!$sess->exists($key,'user')) {
                	    $sess->set($key,self::$_cur_user[0]->$key,'user');	
                	}   
                    self::$_cur_user[0]->$key =& $sess->get($key,null,"user");
                }
            
                // Refresh SUID Stack
            
                $suid_stack = $sess->get('_suid_stack',null,"userstack");
                
                if (is_array($suid_stack)) {
                    self::$_suid_stack = array();
                
                    foreach($suid_stack as $uinfo) {
                      $u = new VUser;
                      $u->setProperties($uinfo);
                      array_push(self::$_suid_stack,$u);                
                    }
                }
            } else {
                        
                 VWP::raiseWarning('No registered session handlers, defaulting to installer privileges!','VUser');
                 self::$_cur_user[0]->name = "Installer";
                 self::$_cur_user[0]->username = "installer";
                 self::$_cur_user[0]->password = rand(10000,99999);
                 self::$_cur_user[0]->email = '';
                 self::$_cur_user[0]->email_verification = null;
                 self::$_cur_user[0]->meta = array();
                 self::$_cur_user[0]->_admin = true;
                 self::$_cur_user[0]->_domain = null;
            }
            
            // Refresh effective roles
   
            $C_Id = array(
             "username"=>self::$_cur_user[0]->username,
             "domain"=>self::$_cur_user[0]->_domain,
            );
      
   
            $response = VEvent::dispatch_event("user","GetRoles",$C_Id);
      
            self::$_cur_user[0]->_roles = array();
      
            foreach($response["trace"] as $ent) {
                $result = $ent["result"];
                if (is_array($result)) {
                    foreach($result as $role) {
                        if (!in_array($role,self::$_cur_user[0]->_roles)) {
                            array_push(self::$_cur_user[0]->_roles,$role);
                        }
                    }
                }
            }   
      
            // Authenticate
            
            if (is_array($auth)) {
                if (isset($auth["logout"]) && $auth["logout"]) {
                    $uinfo = array();
                    foreach($uvars as $key) {
                        $uinfo[$key] = null;
                    }
                    self::$_cur_user[0]->setProperties($uinfo);
                    self::$_cur_user[0]->_roles = array();

                    // Destroy session????
                    $sess->reset();

                    // Rebuild guest
                    
                    self::$_cur_user[0]->name = '';
                    self::$_cur_user[0]->username = '';
                    self::$_cur_user[0]->password = '';
                    self::$_cur_user[0]->email = '';
                    self::$_cur_user[0]->email_verification = null;
                    self::$_cur_user[0]->meta = array();
                    self::$_cur_user[0]->_admin = false;
                    self::$_cur_user[0]->_domain = null;
                    
                    
                    
                    if (isset($auth["username"])) {
                        self::$_cur_user[0]->authenticate($auth); 
                    }
                } else {
                    self::$_cur_user[0]->authenticate($auth);
                }    
            }    
            self::updateSession();            
        }
        
        return self::$_cur_user[0];
    }
    
    /**
     * Get user by username
     * 
     * @param string $username Username
     * @param string $_domain Domain
     * @return VUser|VWarning User on success, error or warning otherwise
     * @access public
     */
    
    public static function &getUserByUsername($username,$_domain = null) 
    {
        $curuser =& self::getCurrent();

        if (empty($_domain) && 
            empty($curuser->_domain) &&
            ($username == $curuser->username) 
           ){
        	return $curuser;
        }
        
        $ud = empty($_domain) ? ':'.$username : $_domain.':'.$username;
        
        if (!isset(self::$_user_cache[$ud])) {
            $credentials = array("username"=>$username,"_domain"=>$_domain);
            $response = VEvent::dispatch_event("user","Find",$credentials);
            
            $result = $response["result"];
            if ($result === false || VWP::isWarning($result)) {
            	$u = VWP::raiseWarning('User not found!',__CLASS__,null,false);
            	return $u;
            }
                
            self::$_user_cache[$ud] = new VUser;
            self::$_user_cache[$ud]->setProperties($result);
        }
        
        return self::$_user_cache[$ud];
    }
    
    /**
     * Free User Record
     * 
     * @param VUser $user User
     * @access public
     */
    
    public static function freeUserRecord(&$user) 
    {    	
    	$ud = empty($user->_domain) ? ':'.$user->username : $user->_domain . ':' . $user->username;  
        unset(self::$_user_cache[$ud]);	
    }
    
    /**
     * Get SUID mode status
     * 
     * @return boolean True if in SUID mode
     * @access public    
     */
     
    function is_suid() 
    { 
        if (count(self::$_suid_stack) > 0) {
            return true;
        }
        return false;
    }

    /**
     * Confirm Email
     *
     * @param string $confirmation_code
     * @return boolean|object True on success, error or warning otherwise
     * @access public
     */
    
    function confirmEmail($confirmation_code) 
    {
    	if (empty($this->email_verification)) {
    		return true;
    	}
    	
    	if ($confirmation_code == $this->email_verification) {
    		$this->email_verification = null;    		    		
    		return $this->save();    		
    	}    	
    	return VWP::raiseWarning('Invalid confirmation code!',__CLASS__,null,false);
    }
    
    /**
     * Save user
     * 
     * @return boolean|object True on success, error or warning otherwise
     * @access public
     */
            
    function save() 
    {
        if ($this->is_foreign()) {     
            return VWP::raiseWarning("Unable to save foreign user!",get_class($this).":save",null,true);
        }
        
        $userinfo = $this->getProperties();        
        
        $userinfo["_admin"] = $this->_admin;
        $userinfo["_domain"] = $this->_domain;
        $response = VEvent::dispatch_event("user","Save",$userinfo);
        if (count($response["trace"]) < 1) {
            return VWP::raiseWarning("No user database available!",get_class($this).":save",null,true);
        }

        $lastError = VWP::raiseWarning("No user database available!",get_class($this).":save",null,false);

        $ctr = 0;
        foreach($response["trace"] as $r) {   
            if (VWP::isWarning($r["result"])) {
                $lastError = $r["result"];
            } elseif ($r["result"]) {
                $ctr++;
            }
        }
     
        $curUser =& self::getCurrent();
        
        if (empty($this->_domain) && ($this->username == $curUser->username)) {
        	self::updateSession();
        }
        
        if ($ctr > 0) {
            return true;
        }
        return $lastError;
    }
    
    /**
     * Delete User
     * 
     * @return boolean|object True on success, error or warning otherwise     
     * @access public
     */
                        
    function delete() 
    {    

        if ($this->is_foreign()) {     
            return VWP::raiseWarning("Unable to delete foreign user!",get_class($this).":save",null,true);
        }
        
        $userinfo = $this->getProperties();        
        
        $userinfo["_admin"] = $this->_admin;
        $userinfo["_domain"] = $this->_domain;
        
        $curUser =& self::getCurrent();
        
        $logout = false;
        if (empty($this->_domain) && ($this->username == $curUser->username)) {
            // Destroy session - OK because this is a delete            
            $sess =& VSession::getInstance();            
            if (!VWP::isWarning($sess)) {
                $sess->reset();
            }

            $logout = true;
        }        
        
        
        $response = VEvent::dispatch_event("user","Delete",$userinfo);
        if (count($response["trace"]) < 1) {
            return VWP::raiseWarning("No user database available!",get_class($this).":save",null,true);
        }
        
        if ($logout) {
        	$uinfo = array();
            $uvars = array(
                 "name",
                 "username",
                 "password",
                 "email",
                 "email_verification",
                 "meta",
                 "_admin",
                 "_domain",
                );        	
            foreach($uvars as $key) {
                $uinfo[$key] = null;
            }
            $curUser->setProperties($uinfo);
            $curUser->_roles = array();

            // Rebuild guest
                    
            $curUser->name = '';
            $curUser->username = '';
            $curUser->password = '';
            $curUser->email = '';
            $curUser->email_verification = null;
            $curUser->meta = array();
            $curUser->_admin = false;
            $curUser->_domain = null;
        }

        $lastError = VWP::raiseWarning("No user database available!",get_class($this).":save",null,false);

        $ctr = 0;
        foreach($response["trace"] as $r) {   
            if (VWP::isWarning($r["result"])) {
                $lastError = $r["result"];
            } elseif ($r["result"]) {
                $ctr++;
            }
        }
            
        if ($ctr > 0) {
            return true;
        }
        return $lastError;
    }
    
    /**     
     * Get user's current shell
     * 
     * @return VShell User's current shell object
     * @access public
     */
    
    function &getShell() 
    {
        return $this->_shell[0];
    } 
    
    /**
     * Get users shell stack
     * 
     * @return array Shell stack
     * @access public
     */
    
    function &getShellStack() {
        return $this->_shell;
    }
    
    /**
     * Set Meta Data
     * 
     * @param string $vname Variable name
     * @param mixed $value Value
     * @param string $domain Domain
     * @return mixed Set Value     
     */
    
    function setMeta($vname,$value,$domain = "__default") 
    {
    	    	
    	if (!isset($this->meta[$domain])) {
    	    $this->meta[$domain] = array();	
    	}
    	$this->meta[$domain][$vname] = $value;
    	return $value; 
    }
    
    /**
     * Unset Meta Data
     * 
     * @param string $vname Value name
     * @param string $domain Domain
     * @return mixed old value
     */
    
    function unsetMeta($vname,$domain) 
    {
    	$oldValue = null;
        if (isset($this->meta[$domain][$vname])) {
            $oldValue = $this->meta[$domain][$vname];
            unset($this->meta[$domain][$vname]);            
        }
        return $oldValue;	
    }
    
    /**
     * Get Meta
     * 
     * @param string $vname
     * @param mixed $default Default value
     * @param string $domain
     * @return mixed Value
     */
    
    function getMeta($vname,$default = null, $domain = "__default") 
    {        
        return (isset($this->meta[$domain][$vname])) ? $this->meta[$domain][$vname] : $default;
    }
    
    /**     
     * Resume logon session (Do not call directly)
     * 
     * Note: This is the entry point for requests sent to the platform.
     * 
     * @access public
     */
         
    function logon() 
    {        
                                
        VEnv::setVar('path',array(VPATH_BASE.DS.'Applications'),'shell');
        VEnv::setVar('shellName','vwebshell','shell');
        VEnv::setVar('shellClassName','VWebShellApplication','shell');
        
        VNotify::Notify('logon','VUser');
        
        $paths = VEnv::getVar('path',array(VPATH_BASE.DS.'Applications'),'shell');
        $shellName = VEnv::getVar('shellName','vwebshell','shell');
        $shellClassName = VEnv::getVar('shellClassName','VWebShellApplication','shell');
                  
        $filename1 = $shellName.'.php';
        $filename2 = $shellName.DS.$shellName.'.php';            
        
        $vfile =& VFilesystem::local()->file();
                        
        $shellFilename = null;
        $len = count($paths);                                    
        for($i=0;$i < $len; $i++) {
           $path = $paths[$i];
                
            if ($vfile->exists($path.DS.$filename1)) {
                $shellFilename = $path.DS.$filename1;             
                $i = $len;
            } elseif (($vfile->exists($path.DS.$filename2))) {
                $shellFilename = $path.DS.$filename2;
                $i = $len;             
            }            
        }                                    
                        
        if (empty($shellFilename)) {
            $this->_shell = array(VWP::raiseError("Default shell '$shellName' not found!","VUser",ERROR_FILENOTFOUND,false));
            return $this->_shell[0];
        } 
        
        /**
         * Require Shell
         */
           
        require_once($shellFilename);
        
        if (!class_exists($shellClassName)) {
            return VWP::raiseError("Shell class \"$shellClassName\" missing!","VUser",null,false);
        }                  

        VNotify::Notify('logon:before_content','VUser');
        
        $cfg = new VConfig;
        
        // Get Route Settings
        $appId = VEnv::getCmd('app',$cfg->default_application,'route');  
        
        $appId = $appId == 'index' ? $cfg->default_application : $appId; 
        
        $widgetId = VEnv::getCmd('widget',null,'route');
        
        // Get URL Settings
        $appId = VEnv::getCmd('app',$appId,'get');  
        $widgetId = VEnv::getCmd('widget',$widgetId,'get');
        
        $cmd = empty($widgetId) ? $appId : $appId . '.' . $widgetId;
                           
        $stdio = new VStdio;
        
        $this->_shell = array(new $shellClassName());       
        
        $buffercfg = array("alias"=>"content");
        
        $doc =& VWP::getDocument();
        
        $bufferId = $doc->createScreenBuffer($buffercfg);                       
        
        $stdio->setOutBuffer($doc,$bufferId);
        
        $env =& $this->_shell[0]->getEnv();
        
        $env["get"] = VEnv::getChannel('get');
        $env["route"] = VEnv::getChannel('route');        
        $env["any"] = VEnv::getChannel('any');
        $env["shell"] = VEnv::getChannel('shell');
        
        $screenId = VEnv::getVar('screen',null,'post');
                
        if (($screenId === null) || ($screenId == $stdio->getScreenId())) {            
            $env["post"] = VEnv::getChannel('post');                                                           
        } else {
        	$nuke = VEnv::getChannel('post');
        	foreach($nuke as $key=>$val) {
        		if ((!isset($env['get'][$key])) || (!isset($env['route'][$key])) || (!isset($env['get'][$key]))) {
        		    unset($env['any'][$key]);	
        		}
        	}
        }
                
        $env['get']['app'] = $appId;
        $env['any']['app'] = $appId;
        if (!empty($widgetId)) {
            $env['get']['widget'] = $widgetId;
            $env['any']['widget'] = $widgetId;
        }

        $result = $this->_shell[0]->execute($cmd,$env,$stdio);             
                        
        if (VWP::isWarning($result)) {
            $result->ethrow();   
        }
        
        VNotify::Notify('logon:after_content','VUser');
                        
        return true;
    }
    
    // end class VUser    
} 