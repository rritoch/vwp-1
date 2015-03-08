<?php

/**
 * User Login Service 
 *  
 * @package    VWP.User
 * @subpackage Services
 * @author Ralph Ritoch <rritoch@gmail.com> 
 * @copyright (c) Ralph Ritoch - All rights reserved
 * @link http://www.vnetpublishing.com 
 */

VWP::RequireLibrary('vwp.net.dre.service');
VWP::RequireLibrary("vwp.sys.events");

VWP::RequireLibrary('vwp.uri');
VWP::RequireLibrary('vwp.session');
VWP::RequireLibrary('vwp.server.client');
VWP::RequireLibrary('vwp.server.headers');

/**
 * User Login Service 
 *  
 * @package    VWP.User
 * @subpackage Services
 * @author Ralph Ritoch <rritoch@gmail.com> 
 * @copyright (c) Ralph Ritoch - All rights reserved
 * @link http://www.vnetpublishing.com 
 */

class UserServiceLogin extends VService {

 function Login($Credentials) {
 
  // Authenticate Credentials
  
  $response = VEvent::dispatch_event("auth","Authenticate",$Credentials);  
  $result = $response["result"];
  if (VWP::isWarning($result)) {
   return $result;
  }
  
  if (($result === false) && (count($response["trace"]) > 0)) {
   return VWP::raiseWarning('Login failed due to invalid credentials!',null,false);
  }
  
  // Create Session
  
  if (isset($Credentials["username"])) {
   $username = $Credentials["username"];
  } else {
   $username = "Guest";
  }

  $UR_Id_List = array("DOMAIN_MASTER");
  
  $ip = VClientInfo::getAddr();
  $browser = VRequestHeaders::get('User-Agent','');
  
  $IP_Addr = array(
   "ip"=>$ip,
   "browser"=>$browser,
  );

  $sess =& VSession::getInstance();
  
  $sess_id = $sess->_createId();


  $lr = VWP::RequireLibrary('security.rbac');  
  if (VWP::isWarning($lr)) {
   return $lr;
  }    
  
  $sec =& VRBAC_Security::getInstance();
  $cfg = $sec->getConfig();
  if (VWP::isWarning($cfg)) {
   return $cfg;
  }
  
  $domain = $cfg["local_domain"];
  
  // Register Session
  

  
  
  $rr = $sec->add_session($sess_id,$username,$domain,$IP_Addr, $UR_Id_List);
  
  if (VWP::isWarning($rr)) {
   return $rr;
  }
  
  $C_Id = array(
   "session_id"=>$sess_id,
   "username"=>$username,
   "domain"=>$domain,
  );
  
  
  $this->setClient($C_Id);
  
  // Return positive response
         
  return true; 
 }

 /**
  * Service Constructor
  * 
  * @param array $cfg Configuration settings    
  */
     
 function __construct($cfg = array()) {
  parent::__construct($cfg);
  $this->disableCache();
 }
 
} // end class
