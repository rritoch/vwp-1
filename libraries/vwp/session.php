<?php

/**
 * Virtual Web Platform - Session Manager
 *  
 * This file provides the primary session manager interface        
 * 
 * @package VWP
 * @subpackage Libraries  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

// restricted access

class_exists("VWP") || die();

/**
 * Require System Events Support
 */
  
VWP::RequireLibrary('vwp.sys.events');

/**
 * Require Server Headers Support
 */
  
VWP::RequireLibrary('vwp.server.headers');

/**
 * Require Server Client Info Support
 */
  
VWP::RequireLibrary('vwp.server.client');
 
/**
 * Virtual Web Platform - Session Manager
 *  
 * This file provides the primary session manager interface        
 * 
 * @package VWP
 * @subpackage Libraries  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */
  
class VSession extends VObject 
{
    
    /**
     * Force SSL Mode
     * 
     * @var boolean $_force_ssl Force SSL Cookies
     * @access private  
     */
        
    protected $_force_ssl = false;
   
    /**
     * Session state
     * 
     * @var string $_state Session state
     * @access private
     */
          
    protected $_state = 'active';
   
    /**
     * Namespace session variable prefix
     * 
     * @var string $_ns_prefix Namespace session variable prefix
     * @access private
     */
          
    static $_ns_prefix = '_vwp_';
    
    /**
     * Session instance
     * 
     * @var string $_instance Instance
     * @access private
     */
        
    static $_instance = null;
    
    
    /**
     * Session timeout
     * 
     * @var integer $_expire Session expire timeout
     * @access public
     */
        
    public $_expire = 3600;
    
    /**
     * Security flags
     *    
     * A = Address
     * B = Browser
     *      
     * @var string $_security Security settings
     * @access public
     */
        
    public $_security = "A";
    
    /**
     * Session object counter
     * 
     * @var integer $_sctr Session object counter
     * @access private
     */
                 
    static $_sctr = 0;
   
    /**
     * Current session
     * 
     * @var VSession $_current Current session
     * @access private
     */
       
    static $_current = null;
    
    /**
     * Session Handler Count
     * 
     * @var integer $_handler_count Session handler count
     * @access private
     */
     
    static $_handler_count = null;
             
    /**
     * Get current session
     * 
     * @return VSession Current session
     * @access public
     */       
    
    public static function &getInstance() 
    {
        if (empty(self::$_current)) {
            self::$_current = new VSession; 
        }    
        return self::$_current;
    }
   
    /**
     * Is Session Live
     * 
     * @return boolean True if there are any session handlers
     * @access public
     */
     
    public function isLive() {            
        return isset(self::$_handler_count) ? self::$_handler_count > 0 : null; 
    }
                       
    /**
     * Register the functions of this class with PHP's session handler
     *
     * @access public
     */
     
    function register() 
    {
        // use this object as the session handler
        return session_set_save_handler(
         array($this, '_open'),
         array($this, '_close'),
         array($this, '_read'),
         array($this, '_write'),
         array($this, '_destroy'),
         array($this, '_gc')
        );
    }
   
    /**
     * Delete a variable from the session
     *     
     * @param string $name Name of variable
     * @param string $namespace Namespace to use, default to 'default'
     * @return mixed Value from session or warning if not set
     * @access public     
     */
     
    function deleteVar( $name, $namespace = 'default' ) 
    {
        $namespace = self::$_ns_prefix.$namespace;
        if( $this->_state !== 'active' ) {
            return VWP::raiseWarning("Session is not active!",get_class($this),null,false);
        }
    
        if ($this->exists($name,$namespace)) {
            $value = $_SESSION[$namespace][$name];
            unset( $_SESSION[$namespace][$name] );
        } else {
            $value = VWP::raiseWarning("Session key not found!",get_class($this),5,false);
        }
        return $value;
    }
   
    /**
     * Check if data exists in the session
     *     
     * @param string $name Name of variable
     * @param string $namespace Namespace to use, default to 'default'
     * @return boolean $result true if the variable exists
     * @access public
     */
     
    function exists( $name, $namespace = 'default' ) 
    {
        $namespace = self::$_ns_prefix.$namespace;
   
        if( $this->_state !== 'active' ) {
            return false;
        }
   
        if (!isset($_SESSION[$namespace])) {
        	return false;
        }
        
        $vlist = array_keys($_SESSION[$namespace]);
        return in_array($name,$vlist);
    }
   
   
    /**
     * Get data value from the session
     *          
     * @param  string $name			Name of a variable
     * @param  mixed  $default 		Default value of a variable if not set
     * @param  string $namespace 	Namespace to use, default to 'default'
     * @return mixed  Value of requested session variable
     * @access public     
     */
     
    function &get($name, $default = null, $namespace = 'default') 
    {
        $namespace = self::$_ns_prefix.$namespace;
        if ($this->_state !== 'active' && $this->_state !== 'expired') {
            $e = VWP::raiseError("Unusable session!",get_class($this),null,false);
            $this->setError($e);            
            return $default;
        }
        if (isset($_SESSION[$namespace][$name])) {
            return $_SESSION[$namespace][$name];
        }
        return $default;
    }
   
    /**
     * Set data value into the session
     *     
     * @param  string $name Name of a variable
     * @param  mixed  $value Value of a variable
     * @param  string $namespace Namespace to use, default to 'default'
     * @return mixed Old data value
     * @access public     
     */
   
    function set($name, $value = null, $namespace = 'default') 
    {
   
        $namespace = self::$_ns_prefix.$namespace;
        
        if($this->_state !== 'active') {
            $e = VWP::raiseWarning("Unable to write to inactive session",get_class($this),null,false);
            $this->setError($e);
            return $e;
        }
   
        $old = isset($_SESSION[$namespace][$name]) ?  $_SESSION[$namespace][$name] : null;
   
        $_SESSION[$namespace][$name] = $value;
             
        return $old;
    }
   
    /**
     * Set the session timers
     *     
     * @return boolean $result true on success
     * @access public     
     */
     
    function setTimers() 
    {
        $now = time();
        if ($this->exists( 'session.timer.start' )) {
            $this->set( 'session.timer.last', $this->get( 'session.timer.now' ) );
            $this->set( 'session.timer.now', $now);  
        } else {
            $this->set('session.timer.start',$now);
            $this->set('session.timer.last', $now );
            $this->set('session.timer.now', $now);
        }
        return true;
    }
   
    /**     
     * Open the Session
     *       
     * @param string $save_path The path to the session object.
     * @param string $session_name The name of the session.
     * @return boolean True on success, false otherwise.
     * @access public     
     */
       
     function _open($save_path,$session_name) 
     {     	 
     	    
         $opt = array("save_path"=>$save_path,"session_name"=>$session_name);
         $response = VEvent::dispatch_event("session","Open",$opt);   
         $ctr = 0;
         foreach($response["trace"] as $r) {
             if (!VWP::isWarning($r["result"])) {
                 $ctr++;
             }
         }
         return $ctr > 0;
     }
   
    /**
     * Close the session.
     *          
     * @return boolean  True on success, false otherwise.
     * @access public     
     */
       
    function _close() 
    {   
        $opt = array();
        $response = VEvent::dispatch_event("session","Close",$opt);
        return count($response["trace"]) > 0;  
    }
   
    /**
     * Read the data for a particular session identifier from the
     * SessionHandler backend.
     *          
     * @param string $id The session identifier.
     * @return string The session data.
     * @access public     
     */
               
    function _read($id) 
    {
    	
    	
        $opt = array("id"=>$id);
        $response = VEvent::dispatch_event("session","Read",$opt);
        
        self::$_handler_count = count($response["trace"]);
        
        $ctr = array();
        $max = 0;
        $ret = null;
        foreach($response["trace"] as $r) {
            if (is_string($r["result"])) {
                $id = md5($r["result"]);
                if (!isset($ctr[$id])) {
                    $ctr[$id] = 0;
                }
                $ctr[$id] = $ctr[$id] + 1;
                if ($ctr[$id] > $max) {
                    $max = $ctr[$id];
                    $ret = $r["result"];
                }       
            }
        }    
        return $ret;
    }
    
    /**
     * Write session data
     * 
     * @param string $id The session identifier.
     * @param string $data Session data
     * @return boolean True on success, false otherwise
     * @access public     
     */
                  
    function _write($id,$data) 
    {
        $opt = array("id"=>$id,"data"=>$data);
        $response = VEvent::dispatch_event("session","Write",$opt);
        return count($response["trace"]) > 0;
    }
   
    /**
     * Destroy the data for a particular session identifier 
     *     
     * @param string $id  The session identifier.
     * @return boolean  True on success, false otherwise.
     * @access public     
     */
     
     function _destroy($id) 
     {
         $opt = array("id"=>$id);
         $response = VEvent::dispatch_event("session","Destroy",$opt);
         return count($response["trace"]) > 0;  
     }
   
    /**
     * Garbage collect stale sessions
     *     
     * @param integer $maxlifetime  The maximum age of a session.
     * @return boolean  True on success, false otherwise.
     * @access public     
     */
     
    function _gc($maxlifetime) 
    {    	
        $opt = array("maxlifetime"=>$maxlifetime);
        $response = VEvent::dispatch_event("session","Gc",$opt);
        return count($response["trace"]) > 0;  
    }
   
    /**
     * Set session cookie parameters
     *
     * @access private
     */
   
    function _initCookieParams() 
    {
        $cookie = session_get_cookie_params();
        if ($this->_force_ssl) {
            $cookie['secure'] = true;
        }
        session_set_cookie_params( $cookie['lifetime'], $cookie['path'], $cookie['domain'], $cookie['secure'] );
    }
   
    /**
     * Set session hits
     *     
     * @return boolean True on success
     * @access public     
     */
     
    function _setHits() 
    {
        $last = $this->get('session.hits.last', 0 );
        $hits = $this->get('session.hits.count', 0 );
        if ($last != self::$_instance) {  
            $this->set( 'session.hits.count', $hits + 1);
            $this->set( 'session.hits.last', self::$_instance);
        }
        return true;
    }
   
    /**
     * Create a session id
     *          
     * @return string Session ID
     * @access public     
     */
      
    function _createId() 
    {
        $id = 0;
        while (strlen($id) < 32)  {
            $id .= mt_rand(0, mt_getrandmax());
        }
        $id = md5( uniqid($id, true));
        return $id;
    }
   
    /**
     * Start a session
     *
     * Creates a session (or resumes the current one based on the state of the session)
     *     
     * @return boolean $result true on success
     * @access public     
     */
     
     function _start() 
     {
         //  start session if not started
         if( $this->_state == 'restart' ) {
             session_id( $this->_createId() );
         }
   
         session_cache_limiter('none');
         session_start();
   
         $doc =& VWP::getDocument();
         if (is_object($doc) && method_exists($doc,'header')) {
             // Send modified header for IE 6.0 Security Policy
             $doc->header('P3P: CP="NOI ADM DEV PSAi COM NAV OUR OTRo STP IND DEM"');
         }
   
         return true;
     }
   
    /**
     * Do some checks for security reason
     *
     * - timeout check (expire)
     * - ip-fixiation
     * - browser-fixiation
     *
     * If one check failed, session data has to be cleaned.
     *     
     * @param boolean $restart reactivate session
     * @return boolean|object True on success, warning or error on failure
     * @access public       
     */
     
    function _validate( $restart = false ) 
    {
        // allow to restart a session
        if( $restart ) {
            $this->_state = 'active';
            $this->set( 'session.client.address', null );
            $this->set( 'session.client.forwarded', null );
            $this->set( 'session.client.browser',null );
            $this->set( 'session.token',null);
        }
   
        // check if session has expired
        if( $this->_expire ) {
            $curTime = $this->get( 'session.timer.now' , 0  );
            $maxTime = $this->get( 'session.timer.last', 0 ) +  $this->_expire;
   
            // empty session variables
            if( $maxTime < $curTime ) {
                $this->_state = 'expired';
                return false;
            }
        }
   
        $httpheaders =& VRequestHeaders::getInstance();
        // record proxy forwarded for in the session in case we need it later
        if ($httpheaders->exists('X_FORWARDED_FOR')) {
            $this->set( 'session.client.forwarded', $httpheaders->get('X_FORWARDED_FOR'));
        }
   
        // check for client adress
     
        $cip = VClientInfo::getAddr();
        if (false !== strpos($this->_security,'A')) {
            $ip = $this->get( 'session.client.address' );
            if( $ip === null ) {
                $this->set( 'session.client.address', $cip);
            } else if( $cip !== $ip ) {
                $this->_state = 'error';
                return VWP::raiseWarning('IP Address missmatch!',get_class($this),null,false);
            }
        }
   
        // check for clients browser
    
        if (false !== strpos($this->_security,'B')) {  
            $cbrowser = $httpheaders->get('USER_AGENT',null);
            $browser = $this->get( 'session.client.browser' );
            if( $browser === null ) {
                $this->set( 'session.client.browser', $cbrowser);
            } else if( $cbrowser !== $browser ) {
                $this->_state	=	'error';    
                return VWP::raiseWarning('Browser missmatch!',get_class($this),null,false);
            }
        }
     
        return true;
    }
   
    /**
     * Reset session
     * 
     * Deletes all session data and creates a new session
     * 
     * @access public          
     */
            
    function reset() 
    {
        session_unset();
        session_destroy();
   
        $this->register();
        $this->_state = 'restart';
        
        //load the session
        $this->_start();
   
        //initialise the session
        $this->_setHits();
        $this->setTimers();
   
        $this->_state = 'active';
      
    }
    
    /**
     * Class constructor
     * 
     * @access public
     */
            
    function __construct() 
    {
           	    	
        self::$_sctr++;
     
        if (empty(self::$_instance)) {   
            $r1 = rand(10,99);
            $r2 = rand(10,99);
            $r3 = self::$_sctr;
            $r4 = time();   
            self::$_instance = $r1.".".$r2.".".$r3.".".$r4;
        }
     
        
        // Need to destroy any existing sessions started with session.auto_start
        if (session_id()) {
            session_unset();
            session_destroy();
        }
   
                
        self::$_current =& $this;
        
        $r = $this->register();
     
                
        // set default session save handler
        ini_set('session.save_handler', 'user');
   
        // disable transparent sid support
        ini_set('session.use_trans_sid', '0');
     
        $this->_initCookieParams();
   
        //load the session
        $this->_start();
   
        //initialise the session
        $this->_setHits();
     
        $this->setTimers();
   
        $this->_state = 'active';
   
        // perform security checks
     
        $r = $this->_validate();
     
        if (VWP::isWarning($r)) {
            $this->setError($r);
            $this->reset();
        }
        
    }
    
    // end VSession class 
}  
