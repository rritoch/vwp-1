<?php

VWP::RequireLibrary('vwp.archive.installer');

/**
 * Base installer 1.0.1
 */

class VWPUpgrade_1_0_1_Base extends VInstaller 
{

    /**     
     * Install module from pacakge
     * 
     * @param string $package_file Package file
     * @return boolean|object True on success, error or warning otherwise
     * @access public
     */
    
    function helper_doInstallPackage($package_file) 
    {
    	$vfolder =& v()->filesystem()->folder();
    	
        VWP::RequireLibrary('vwp.archive.archive');
        $tempFolder = $vfolder->mktemp("install");
        if (VWP::isWarning($tempFolder)) {
            return $tempFolder;
        }
        $arch = VArchive::getInstance();
        $result = $arch->extract($package_file,$tempFolder);
        if (VWP::isWarning($result)) {
            return $result;
        }
        $tmpv = parent::$_installer_versions;
        parent::$_installer_versions = array(array(),array());
        $result = $this->helper_doInstall($tempFolder);
        parent::$_installer_versions = $tmpv;
        $vfolder->delete($tempFolder);
        return $result;
    }
	
	
    /**     
     * Install module from folder
     * 
     * @param string $source_folder
     * @return boolean|object True on success, error or warning otherwise
     * @access public
     */
 
    function helper_doInstall($source_folder) 
    {
  
    	$vfile =& v()->filesystem()->file();
    	
        // verify installer exists  
        $installerFilename = v()->filesystem()->path()->clean($source_folder.DS.'setup'.DS.'installer.php');
        if (!$vfile->exists($installerFilename)) {
            return VWP::raiseError("Installer $installerFilename not found!",get_class($this).":doInstall",500,false);
        }
  
        // load manifest to get installer class name
        $manifestFilename = $source_folder.DS.'base'.DS.'manifest.xml';
        if (!$vfile->exists($manifestFilename)) {
            return VWP::raiseError("Manifest not found!",get_class($this).":doInstall",500,false);
        }

        $manifest = VManifest::getInstance();  
        $result = $manifest->load($manifestFilename);
        if (VWP::isWarning($result)) {
            return $result;
        }  
  
        $verstr = str_replace(".","_",$manifest->version);
  
        if (empty($manifest->_app)) {
        	$className = ucfirst(strtolower($manifest->_theme_type)) . '_'. ucfirst(strtolower($manifest->getThemeId())) . '_'. $verstr.'_Installer';        
        } else {
            $className = ucfirst(strtolower($manifest->_app)) . '_'. $verstr.'_Installer';
        }
  
        require_once($installerFilename);
        if (!class_exists($className)) {
            return VWP::raiseError("Installer $className not found!",get_class($this).":doInstall",510,false);   
        }  
  
        $this->_installer = new $className; 
        $result = $this->_installer->process("install");
        if (VWP::isWarning($result)) {
            return $result;
        }  
        $result = array(
            "complete"=>$this->_installer->is_complete(),
            "success"=>$this->_installer->is_success()
        );
        return $result;    
    }
	
	
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
  
  $this->setAppId("vwpupgrade");
  $this->setBaseVersion(array(1,0,1));   
  $this->setName("VWPUpgrade");          
  $this->setAuthor("Ralph Ritoch");
  $this->setWebsite("VNetPublishing.Com","http://www.vnetpublishing.com");
  parent::__construct();
 }   
}

/**
 * Version installer 1.0.1
 */

class VWPUpgrade_1_0_1_Sub_1_0_1 extends VWPUpgrade_1_0_1_Base 
{
	
	function initDB() 
	{
		return true;
	}	
	
	function uninitDB() 
	{
		return true;
	}

  /**
   * Install SubPackages
   */
	
  function installSubPackages() 
  {
       $subPackages = array(
           "vwp-1.0.1-1295304190.zip",
           "vwebshell-1.0.1-1295296676.zip",
           "user-1.0.1-1295291734.zip",
           "tinymce-1.0.1-1295291417.zip",
           "thememgr-1.0.1-1295290865.zip",           
           "menumgr-1.0.1-1295297016.zip",
           "menu-1.0.1-1295290178.zip",
           "content-1.0.1-1295289955.zip",
           "admin_theme_default-1.0.1-1295292388.zip",
           "site_theme_default-1.0.1-1295292667.zip",           
       );
       
       $src_path = $this->getSourcePath().DS.'base'.DS.'packages';
       
       foreach($subPackages as $pkg) {
           $filename = $src_path.DS.$pkg;
           $result = $this->helper_doInstallPackage($filename);
           if (VWP::isWarning($result)) {
               $result->ethrow();
           }	
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
                 'installMenuLinks',
                 'installSubPackages');
  
  $applinks = array();
      
  $this->setMenuLinks('app_admin',$applinks);  
  
  $base = dirname(dirname(__FILE__)).DS.'base'.DS.'events';
  
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
  
  $applinks[] = array();
  
  $this->setMenuLinks('app_admin',$applinks);  
  
  $base = dirname(dirname(__FILE__)).DS.'base'.DS.'events';
  
  $evtBase = VWP::getVarPath('vwp').DS.'events';
  
  $events = array();
  
  $this->setEvents($events);
  
  $result = $this->runAll($tasks);
     
  $this->finish($result);
  return $result;  
 }
   
 function __construct() {
   // register previous version   
   
   $this->setVersion(array(1,0,1));   
   $this->setReleaseDate("January 17, 2011");
   parent::__construct();
 }
   
} // end class



/**
 * Interface class
 */
  
class VWPUpgrade_1_0_1_Installer  extends VWPUpgrade_1_0_1_Sub_1_0_1 {
 // interface class
}

