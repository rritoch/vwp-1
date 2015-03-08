<?php

VWP::RequireLibrary('vwp.archive.installer');
VWP::RequireLibrary('vwp.sys.registry');

/**
 * Base installer 1.0.1
 */

class Tinymce_1_0_1_Base extends VInstaller {

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
  
  $this->setAppId("tinymce");
  $this->setBaseVersion(array(1,0,1));   
  $this->setName("TinyMCE");          
  $this->setAuthor("Ralph Ritoch");
  $this->setWebsite("VNetPublishing.Com","http://www.vnetpublishing.com");
  parent::__construct();
 }   
}

/**
 * Version installer 1.0.1
 */

class Tinymce_1_0_1_Sub_1_0_1 extends Tinymce_1_0_1_Base {
 
 /**
  * Register Editor
  */
  
 function registerEditor() {

  $settings = array(
   "app"=>"tinymce",
   "name"=>"TinyMCE",
   "widget"=>"editor",  
  );
  
  $key = "TOOLS\\Editors\\TinyMCE";
  
  $localMachine = & Registry::LocalMachine();  
  
  $result = Registry::RegCreateKeyEx($localMachine,
                              $key,
                              0,
                              '',
                              0,
                              0,
                              0,
                              $registryKey,
                              $result); 
                              
  if (!VWP::isWarning($result)) {
   $result = true;
   foreach($settings as $key=>$val) {
    $sresult= Registry::RegSetValueEx($registryKey,
                           $key,
                           0, // reserved 
                           REG_SZ, // string
                           $val,
                           strlen($val)); 
    if (VWP::isWarning($sresult)) {
     $result = $sresult;                            
    }  
   }
   
   Registry::RegCloseKey($registryKey);
   Registry::RegCloseKey($localMachine);

   return $result;
  }
  
  Registry::RegCloseKey($localMachine);
  return $result; 
 
 
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
                 'registerEditor');
  
  //$base = dirname(dirname(__FILE__)).DS.'base'.DS.'events';
  
  $events = array();
  
  $this->setEvents($events);
  
  $result = $this->runAll($tasks);
     
  $this->finish($result);
  return $result;  
 }

 function __construct() {
   $this->setVersion(array(1,0,1));   
   $this->setReleaseDate("January 17, 2011");
   parent::__construct();
 }
   
} // end class


/**
 * Interface class
 */
  
class Tinymce_1_0_1_Installer  extends Tinymce_1_0_1_Sub_1_0_1 {
 // interface class
}
