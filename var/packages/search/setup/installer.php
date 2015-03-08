<?php

VWP::RequireLibrary('vwp.archive.installer');
VWP::RequireLibrary('vwp.sys.registry');

/**
 * Base installer 1.0.2
 */

class Search_1_0_2_Base extends VInstaller {

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
  
  $this->setAppId("search");
  $this->setBaseVersion(array(1,0,2));   
  $this->setName("Search");          
  $this->setAuthor("Ralph Ritoch");
  $this->setWebsite("VNetPublishing.Com","http://www.vnetpublishing.com");
  parent::__construct();
 }   
}

/**
 * Version installer 1.0.1
 */

class Search_1_0_2_Sub_1_0_2 extends Search_1_0_2_Base {
 
     
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
      
  $this->setMenuLinks('app_admin',$applinks);
  
  //$base = dirname(dirname(__FILE__)).DS.'base'.DS.'events';
  
  $events = array();
  
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
  
  
  $this->setMenuLinks('app_admin',$applinks);
  
  //$base = dirname(dirname(__FILE__)).DS.'base'.DS.'events';
  
  $events = array();
  
  $this->setEvents($events);
  
  $result = $this->runAll($tasks);
     
  $this->finish($result);
  return $result;  
 }
 
 function __construct() {
   $this->setVersion(array(1,0,2));   
   $this->setReleaseDate("March 6, 2011");
   parent::__construct();
 }
    // end class Search_1_0_2_Sub_1_0_2 
} 


/**
 * Interface class
 */
  
class Search_1_0_2_Installer  extends Search_1_0_2_Sub_1_0_2 {
 // interface class
}

