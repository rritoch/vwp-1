<?php

VWP::RequireLibrary('vwp.archive.installer');

/**
 * Base installer 1.0.0
 */

class Vwp_1_0_1_Base extends VInstaller {

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
  
  $this->setAppId("vwp");
  $this->setBaseVersion(array(1,0,1));   
  $this->setName("Vwp");          
  $this->setAuthor("Ralph Ritoch");
  $this->setWebsite("VNetPublishing.Com","http://www.vnetpublishing.com");
  parent::__construct();
 }   
}

/**
 * Version installer 1.0.1
 */

class Vwp_1_0_1_Sub_1_0_1 extends Vwp_1_0_1_Base {

	/**
	 * Remove Expired Files
	 * 
	 * @access public
	 */
	
    function endClean() 
    {
    	$vpath =& v()->filesystem()->path();
    	$vfolder =& v()->filesystem()->folder();
    	
        $libpath = $vpath->clean($this->getInstallPath('library') .DS.$this->app_id);
        
        if ($vfolder->exists($libpath.DS.'xml'.DS.'vdom')) {
            $vfolder->delete($libpath.DS.'xml'.DS.'vdom');
        }
                
        return true;
    }
  
    /**
     * Version install method
     * 
     * @access public      
     */     

    function version_install() 
    {
   
        $tasks = array('initDB',
                 'copyfiles',
                 'installEvents',
                 'installMenuLinks',
                 'endClean');
  
        $corelinks = array();
  
        $corelinks[] = array(
         "type"=>"applink",
         "text"=>'Site',
         "widget"=>''  
        );
  
       $corelinks[] = array(
            "type"=>"applink",
            "text"=>'System Configuration',
            "widget"=>'vwp.configure'
        );
  
        $this->setMenuLinks('core_admin',$corelinks);
  
        //$base = dirname(dirname(__FILE__)).DS.'base'.DS.'events';
  
        $events = array();
    
        $this->setEvents($events);
  
        $result = $this->runAll($tasks);
     
        $this->finish($result);
        return $result; 
    }

 function __construct() {
   $this->setVersion(array(1,0,1));   
   $this->setReleaseDate("January 9, 2011");
   parent::__construct();
 }
   
} // end class


/**
 * Interface class
 */
  
class Vwp_1_0_1_Installer  extends Vwp_1_0_1_Sub_1_0_1 {
 // interface class
}
