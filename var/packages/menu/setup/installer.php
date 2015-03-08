<?php

VWP::RequireLibrary('vwp.archive.installer');

/**
 * Base installer 1.0.2
 */

class Menu_1_0_2_Base extends VInstaller {

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
  
  $this->setAppId("menu");
  $this->setBaseVersion(array(1,0,2));   
  $this->setName("Menu");          
  $this->setAuthor("Ralph Ritoch");
  $this->setWebsite("VNetPublishing.Com","http://www.vnetpublishing.com");
  parent::__construct();
 }   
}

/**
 * Version installer 1.0.1
 */

class Menu_1_0_2_Sub_1_0_2 extends Menu_1_0_2_Base {


 function initDB() {
  // no database entries!
  return true;
 }

 function copyfiles() {
   
  $install_path = $this->getSourcePath().DS.'base';  
  
  $manifestFilename = $install_path.DS.'manifest.xml';  
  $manifest = VManifest::getInstance();  
  $result = $manifest->load($manifestFilename);
  if (VWP::isWarning($result)) {
   return $result;
  }     
  
  $ver = implode(".",$this->version()); 
      
  $files = array();

  $folders = $manifest->getFolders($ver);

  $fmapping = array(
   "base"=>"application",
   "library"=>"library",
   "setup"=>"setup",
  );
  foreach($folders as $type=>$flist) {
   $cur_folder_id = $fmapping[$type];
   if (!isset($files[$cur_folder_id])) {
    $files[$cur_folder_id] = array();
   }
   
   $cur_offset_path = false; // used for app modules
   foreach($flist as $fname) {   
    array_push($files[$cur_folder_id],array($cur_offset_path,$fname));
   }   
  }
  return $this->install_files($files);
 }



  /**
   * Version install method
   * 
   * @access public      
   */     

 function version_install() {
   
  $ver = implode(".",$this->version());   

  $result = $this->initDB();
  if (VWP::isWarning($result)) {
   $this->finish($result);
   return $result;
  }
  
  $result = $this->copyfiles();
  
  if ($result) {
   $this->addNotice("[" . implode(".",$this->version()) . "] Installed!",true);
  } else {      
   $this->addNotice("[" . implode(".",$this->version()) . "] Install Failed!",false);
  }    
  $this->finish($result);
  return $result;  
 }

 function __construct() {
   $this->setVersion(array(1,0,2));   
   $this->setReleaseDate("February 3, 2011");
   parent::__construct();
 }
   
} // end class


/**
 * Interface class
 */
  
class Menu_1_0_2_Installer  extends Menu_1_0_2_Sub_1_0_2 {
 // interface class
}
