<?php

VWP::RequireLibrary('vwp.archive.installer');

/**
 * Base installer 1.0.2
 */

class User_1_0_2_Base extends VInstaller {

 function process($mode) {
  $this->base_mode = $mode;      
  $level = VEnv::getVar("level",false);
   
  switch($mode) {
   case "install":
    $base_path = dirname(dirname(__FILE__));    
    $this->setSourcePath($base_path);
    $this->setSourcePath($base_path.DS.'setup',"setup");
    $this->setSourcePath($base_path.DS.'base',"application");
    $this->setSourcePath($base_path.DS.'library',"library");
     
    //$this->addNotice("Processing Install Request",true);
     
    if ($level === false) {      
     $this->init_install();
     return $this->continue_install();
    } else {
     $this->setInstallLevel($level);
     return $this->continue_install();
    }
    break;
   case "uninstall":       
    //$this->addNotice("Processing Uninstall Request",true);
    
    if ($level === false) {
     $this->uninstall();
     } else {
      $this->setInstallLevel($level);
      $this->continue_uninstall();
     }
     if ($this->is_complete() && $this->is_success()) {
      $this->wipe_setup();
     }
     break; 
    default:
    return VWP::raiseError("Unknown mode sent to installer!",get_class($this).":process",520,false);
  }
  return true; 
 }

 function __construct() {
  
  $this->setAppId("user");
  $this->setBaseVersion(array(1,0,2));   
  $this->setName("User");          
  $this->setAuthor("Ralph Ritoch");
  $this->setWebsite("VNetPublishing.Com","http://www.vnetpublishing.com");
  parent::__construct();
 }   
}

/**
 * Version installer 1.0.0
 */

class User_1_0_2_Sub_1_0_0 extends User_1_0_2_Base 
{
 	
  /**
   * Version install method
   * 
   * @access public      
   */     

 function version_install() {
        
  $tasks = array('initDB',
                 'copyfiles',
                 'installEvents',
                 'installMenuLinks');
  
  $applinks = array();
  
  $applinks[] = array(
    "type"=>"applink",
    "text"=>'User Administration',
    "widget"=>'user.admin.users'  
  );
    
  $this->setMenuLinks('app_admin',$applinks);  
  
  $base = dirname(dirname(__FILE__)).DS.'base'.DS.'events';
  
  $events = array(
   array( // user/admin
    "type"=>"user",
    "id"=>"admin",
    "filename"=>$base.DS.'user'.DS.'admin.php'),
   array( // auth/admin
    "type"=>"auth",
    "id"=>"admin",
    "filename"=>$base.DS.'auth'.DS.'admin.php'),    
   array( // session/admin
    "type"=>"session",
    "id"=>"admin",
    "filename"=>$base.DS.'session'.DS.'admin.php'),
   array(
    "type"=>"user",
    "id"=>"user",
    "filename"=>$base.DS.'user'.DS.'user.php'),
   array(
    "type"=>"auth",
    "id"=>"user",
    "filename"=>$base.DS.'auth'.DS.'user.php'),   
  );
  
  $this->setEvents($events);
  
  $result = $this->runAll($tasks);
     
  $this->finish($result);
  return $result;  
 }

  /**
   * Version uninstall method
   * 
   * @access public      
   */     

 function version_uninstall() {
        
  $tasks = array('uninitDB',
                 'deletefiles',
                 'uninstallEvents',
                 'uninstallMenuLinks');
  
  $applinks = array();
  
  $applinks[] = array(
    "type"=>"applink",
    "text"=>'User Administration',
    "widget"=>'user.admin.users'  
  );
    
  $this->setMenuLinks('app_admin',$applinks);  
  
  $base = dirname(dirname(__FILE__)).DS.'base'.DS.'events';
  
   $evtBase = VWP::getVarPath('vwp').DS.'events';
  
  $events = array(
   array( // user/admin
    "type"=>"user",
    "id"=>"admin",
    "filename"=>$evtBase.DS.'user'.DS.'admin.php'),
   array( // auth/admin
    "type"=>"auth",
    "id"=>"admin",
    "filename"=>$evtBase.DS.'auth'.DS.'admin.php'),    
   array( // session/admin
    "type"=>"session",
    "id"=>"admin",
    "filename"=>$evtBase.DS.'session'.DS.'admin.php'),
   array(
    "type"=>"user",
    "id"=>"user",
    "filename"=>$evtBase.DS.'user'.DS.'user.php'),
   array(
    "type"=>"auth",
    "id"=>"user",
    "filename"=>$evtBase.DS.'auth'.DS.'user.php'),   
  );
   
  $this->setEvents($events);
  
  $result = $this->runAll($tasks);
     
  $this->finish($result);
  return $result;  
 }
 
 
 
     function __construct() 
     {
         $this->setVersion(array(1,0,0));   
         $this->setReleaseDate("June 4, 2010");
         parent::__construct();
     }
     
     // end class User_1_0_1_Base 
} 


/**
 * Version installer 1.0.1
 */

class User_1_0_2_Sub_1_0_1 extends User_1_0_2_Base 
{

	
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
	
	
	function initDB() 
	{
	   $cfg = $this->getConfig();
		if (!VWP::isWarning($cfg)) 
		{
		      // Check Database
  
              
  
              $db =& v()->dbi()->getDatabase($cfg["user_database"]);
              if (VWP::isWarning($db)) {
                 return null;
              }
  
              // Check Table
  
              $tbl_prefix = $cfg["table_prefix"];
  
              $table_name = $tbl_prefix . 'users';
              $table = & $db->getTable($table_name);

              
              $table->loadColumns();

              $have_meta = false;
              $have_email_verification = false;
              
              foreach($table->columns as $col) {
                  if ($col['name'] == 'meta') {
                      $have_meta = true;
                  }
                  
                  if ($col['name'] == 'email_verification') {
                  	  $have_email_verification = true;
                  }
              }
              
              $type = strtolower($db->getDBType());
              
              
              if (!$have_email_verification) {
                  switch($type) {
              	      case "mysql":
                          $query = 'ALTER TABLE '
                               .$db->nameQuote($table_name)
                               .' ADD COLUMN `email_verification` varbinary(128) AFTER `email`';
                               $db->setQuery($query);
                               $result = $db->query();
                               $db->unloadTables();               		
                  }
              }
              
		      if (!$have_meta) {
                  switch($type) {
              	      case "mysql":
                          $query = 'ALTER TABLE '
                               .$db->nameQuote($table_name)
                               .' ADD COLUMN `meta` BLOB AFTER `name`';
                               $db->setQuery($query);
                               $result = $db->query();
                               $db->unloadTables();               		
                  }
              }  
		                            
		} else {
        print_r($cfg);
      }
		return true;
	}	
	
	function uninitDB() 
	{
	   print_r(array('uninitDB'));
	   
	    $cfg = $this->getConfig();
		if (!VWP::isWarning($cfg)) 
		{
		      // Check Database
  
              $db =& v()->dbi()->getDatabase($cfg["user_database"]);
              if (VWP::isWarning($db)) {
                 print_r($db);
                 return null;
              }
  
              // Check Table
  
              $tbl_prefix = $cfg["table_prefix"];
  
              $table_name = $tbl_prefix . 'users';
              $table = & $db->getTable($table_name);

              
              $table->loadColumns();

              $have_meta = false;
              $have_email_verification = false;
              
              foreach($table->columns as $col) {
                  if ($col['name'] == 'meta') {
                      $have_meta = true;
                  }
                  
                  if ($col['name'] == 'email_verification') {
                  	  $have_email_verification = true;
                  }
              }
              
              $type = strtolower($db->getDBType());
              
              
              if ($have_email_verification) {
                  switch($type) {
              	      case "mysql":
                          $query = 'ALTER TABLE '
                               .$db->nameQuote($table_name)
                               .' DROP COLUMN `email_verification`';
                               $db->setQuery($query);
                               $result = $db->query();
                               print_r(array('result',$result));
                               $db->unloadTables();               		
                  }
              } else {
                  print_r(array('NO EMAIL VERIFIC'));
              }
              
		      if ($have_meta) {
                  switch($type) {
              	      case "mysql":
                          $query = 'ALTER TABLE '
                               .$db->nameQuote($table_name)
                               .' DROP COLUMN `meta`';
                               $db->setQuery($query);
                               $result = $db->query();
                               $db->unloadTables();               		
                  }
              }  
		                            
		} else {
        print_r($cfg);
      }
		return true;
	}
	
  /**
   * Version install method
   * 
   * @access public      
   */     

 function version_install() {
        
  $tasks = array('initDB',
                 'copyfiles',
                 'installEvents',
                 'installMenuLinks');
  
  $applinks = array();
  
  $applinks[] = array(
    "type"=>"applink",
    "text"=>'User Administration',
    "widget"=>'user.admin.users'  
  );
    
  $this->setMenuLinks('app_admin',$applinks);  
  
  $base = dirname(dirname(__FILE__)).DS.'base'.DS.'events';
  
  $events = array(
   array( // user/admin
    "type"=>"user",
    "id"=>"admin",
    "filename"=>$base.DS.'user'.DS.'admin.php'),
   array( // auth/admin
    "type"=>"auth",
    "id"=>"admin",
    "filename"=>$base.DS.'auth'.DS.'admin.php'),    
   array( // session/admin
    "type"=>"session",
    "id"=>"admin",
    "filename"=>$base.DS.'session'.DS.'admin.php'),
   array(
    "type"=>"user",
    "id"=>"user",
    "filename"=>$base.DS.'user'.DS.'user.php'),
   array(
    "type"=>"auth",
    "id"=>"user",
    "filename"=>$base.DS.'auth'.DS.'user.php'),   
  );
  
  $this->setEvents($events);
  
  $result = $this->runAll($tasks);
     
  $this->finish($result);
  return $result;  
 }

  /**
   * Version uninstall method
   * 
   * @access public      
   */     

 function version_uninstall() {
        
  $tasks = array('uninitDB',
                 'deletefiles',
                 'uninstallEvents',
                 'uninstallMenuLinks');
  
  $applinks = array();
  
  $applinks[] = array(
    "type"=>"applink",
    "text"=>'User Administration',
    "widget"=>'user.admin.users'  
  );
  
  $this->setMenuLinks('app_admin',$applinks);  
  
  $base = dirname(dirname(__FILE__)).DS.'base'.DS.'events';
  
  $evtBase = VWP::getVarPath('vwp').DS.'events';
  
  $events = array(
   array( // user/admin
    "type"=>"user",
    "id"=>"admin",
    "filename"=>$evtBase.DS.'user'.DS.'admin.php'),
   array( // auth/admin
    "type"=>"auth",
    "id"=>"admin",
    "filename"=>$evtBase.DS.'auth'.DS.'admin.php'),    
   array( // session/admin
    "type"=>"session",
    "id"=>"admin",
    "filename"=>$evtBase.DS.'session'.DS.'admin.php'),
   array(
    "type"=>"user",
    "id"=>"user",
    "filename"=>$evtBase.DS.'user'.DS.'user.php'),
   array(
    "type"=>"auth",
    "id"=>"user",
    "filename"=>$evtBase.DS.'auth'.DS.'user.php'),   
  );
  
  $this->setEvents($events);
  
  $result = $this->runAll($tasks);
     
  $this->finish($result);
  return $result;  
 }

 
     function __construct() 
     {
         $this->setVersion(array(1,0,1));   
         $this->setReleaseDate("December 3, 2010");
         parent::__construct();
     }
     
     // end class User_1_0_1_Base 
} 
 
/**
 * Version installer 1.0.2
 */

class User_1_0_2_Sub_1_0_2 extends User_1_0_2_Base 
{

	
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
	
	
	function initDB() 
	{
		return true;
	}	
	
	function uninitDB() 
	{
		return true;
	}
	
  /**
   * Version install method
   * 
   * @access public      
   */     

 function version_install() {
        
  $tasks = array('initDB',
                 'copyfiles',
                 'installEvents',
                 'installMenuLinks');
  
  $applinks = array();
  
  $applinks[] = array(
    "type"=>"applink",
    "text"=>'User Administration',
    "widget"=>'user.admin.users'  
  );
    
  $this->setMenuLinks('app_admin',$applinks);  
  
  $base = dirname(dirname(__FILE__)).DS.'base'.DS.'events';
  
  $events = array(
   array( // user/admin
    "type"=>"user",
    "id"=>"admin",
    "filename"=>$base.DS.'user'.DS.'admin.php'),
   array( // auth/admin
    "type"=>"auth",
    "id"=>"admin",
    "filename"=>$base.DS.'auth'.DS.'admin.php'),    
   array( // session/admin
    "type"=>"session",
    "id"=>"admin",
    "filename"=>$base.DS.'session'.DS.'admin.php'),
   array(
    "type"=>"user",
    "id"=>"user",
    "filename"=>$base.DS.'user'.DS.'user.php'),
   array(
    "type"=>"auth",
    "id"=>"user",
    "filename"=>$base.DS.'auth'.DS.'user.php'),   
  );
  
  $this->setEvents($events);
  
  $result = $this->runAll($tasks);
     
  $this->finish($result);
  return $result;  
 }

  /**
   * Version uninstall method
   * 
   * @access public      
   */     

 function version_uninstall() {
        
  $tasks = array('uninitDB',
                 'deletefiles',
                 'uninstallEvents',
                 'uninstallMenuLinks');
  
  $applinks = array();
  
  $applinks[] = array(
    "type"=>"applink",
    "text"=>'User Administration',
    "widget"=>'user.admin.users'  
  );
  
  $this->setMenuLinks('app_admin',$applinks);  
  
  $base = dirname(dirname(__FILE__)).DS.'base'.DS.'events';
  
  $evtBase = VWP::getVarPath('vwp').DS.'events';
  
  $events = array(
   array( // user/admin
    "type"=>"user",
    "id"=>"admin",
    "filename"=>$evtBase.DS.'user'.DS.'admin.php'),
   array( // auth/admin
    "type"=>"auth",
    "id"=>"admin",
    "filename"=>$evtBase.DS.'auth'.DS.'admin.php'),    
   array( // session/admin
    "type"=>"session",
    "id"=>"admin",
    "filename"=>$evtBase.DS.'session'.DS.'admin.php'),
   array(
    "type"=>"user",
    "id"=>"user",
    "filename"=>$evtBase.DS.'user'.DS.'user.php'),
   array(
    "type"=>"auth",
    "id"=>"user",
    "filename"=>$evtBase.DS.'auth'.DS.'user.php'),   
  );
  
  $this->setEvents($events);
  
  $result = $this->runAll($tasks);
     
  $this->finish($result);
  return $result;  
 }
 
 
 
 function __construct() {
   // register previous version
   $o1 = new User_1_0_2_Sub_1_0_0;
   
   $this->setVersion(array(1,0,2));   
   $this->setReleaseDate("February 3, 2010");
   parent::__construct();
 }
   
} // end class



/**
 * Interface class
 */
  
class User_1_0_2_Installer  extends User_1_0_2_Sub_1_0_2 {
 // interface class
}
