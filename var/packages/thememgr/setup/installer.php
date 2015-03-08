<?php

VWP::RequireLibrary('vwp.archive.installer');
VWP::RequireLibrary('vwp.sys.registry');

/**
 * Base installer 1.0.0
 */

class Thememgr_1_0_1_Base extends VInstaller 
{

    function process($mode) 
    {
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

    function __construct() 
    {
  
        $this->setAppId("thememgr");
        $this->setBaseVersion(array(1,0,1));   
        $this->setName("ThemeMGR");          
        $this->setAuthor("Ralph Ritoch");
        $this->setWebsite("VNetPublishing.Com","http://www.vnetpublishing.com");
        parent::__construct();
    }

    // end class Thememgr_1_0_1_Base
}

/**
 * Version installer 1.0.0
 */

class Thememgr_1_0_1_Sub_1_0_0 extends Thememgr_1_0_1_Base 
{
 
     
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
                       'installMenuLinks');
     
        $corelinks = array();
  
        $corelinks[] = array(
             "type"=>"applink",
             "text"=>'Theme Manager',
             "widget"=>'thememgr'  
         );

 
        $this->setMenuLinks('core_admin',$corelinks);
  
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

    function version_uninstall() 
    {
        
        $tasks = array('uninitDB',
                       'deletefiles',
                       'uninstallEvents',
                       'uninstallMenuLinks');
  
        $corelinks = array();
  
        $corelinks[] = array(
          "type"=>"applink",
          "text"=>'Theme Manager',
          "widget"=>'thememgr'  
        );
 
        $this->setMenuLinks('core_admin',$corelinks);
  
        //$base = dirname(dirname(__FILE__)).DS.'base'.DS.'events';
  
        $events = array();
  
        $this->setEvents($events);
  
        $result = $this->runAll($tasks);
     
        $this->finish($result);
        return $result;  
    }
 
    function __construct() 
    {
        $this->setVersion(array(1,0,0));   
        $this->setReleaseDate("September 22, 2010");
        parent::__construct();
    }
    
    // end class  Thememgr_1_0_1_Sub_1_0_0
} 

/**
 * Version installer 1.0.1
 */

class Thememgr_1_0_1_Sub_1_0_1 extends Thememgr_1_0_1_Base 
{
 
     
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
                       'installMenuLinks');
     
        $corelinks = array();
  
        $corelinks[] = array(
             "type"=>"applink",
             "text"=>'Theme Manager',
             "widget"=>'thememgr'  
         );

 
        $this->setMenuLinks('core_admin',$corelinks);
  
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

    function version_uninstall() 
    {
        
        $tasks = array('uninitDB',
                       'deletefiles',
                       'uninstallEvents',
                       'uninstallMenuLinks');
  
        $corelinks = array();
  
        $corelinks[] = array(
          "type"=>"applink",
          "text"=>'Theme Manager',
          "widget"=>'thememgr'  
        );
 
        $this->setMenuLinks('core_admin',$corelinks);
  
        //$base = dirname(dirname(__FILE__)).DS.'base'.DS.'events';
  
        $events = array();
  
        $this->setEvents($events);
  
        $result = $this->runAll($tasks);
     
        $this->finish($result);
        return $result;  
    }
 
    function __construct() 
    {
        // register previous version
        $o1 = new Thememgr_1_0_1_Sub_1_0_0;
            	
        $this->setVersion(array(1,0,1));   
        $this->setReleaseDate("December 31, 2010");
        parent::__construct();
    }
    
    // end class  Thememgr_1_0_1_Sub_1_0_0
} 

/**
 * Interface class
 */
  
class Thememgr_1_0_1_Installer  extends Thememgr_1_0_1_Sub_1_0_1 
{
    // interface class
}
