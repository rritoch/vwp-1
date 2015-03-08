<?php

/**
 * VWP Installer 
 * 
 * This file contains the VInstaller class 
 * which is used as the base class for VWP Module installers.
 * 
 * This system is used for installing Applications, Plugins, and Themes.
 * 
 * Note: This system includes modal features
 *       such as prompting a user. Modal features should not be used
 *       as not all installer applications will implement
 *       modal capability. Modal installers
 *       also make automated application deployment nearly impossible.
 *       Until these issues are resolved modal features may not
 *       be fully implemented.
 *     
 * @package    VWP
 * @subpackage Libraries.Archive
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */


VWP::RequireLibrary('vwp.archive.manifest');

/**
 * VWP Installer 
 * 
 * This is the base class for VWP Module installers.
 * 
 * This system is used for installing Applications, Plugins, and Themes.
 * 
 * Note: This system includes modal features
 *       such as prompting a user. Modal features should not be used
 *       as not all installer applications will implement
 *       modal capability. Modal installers
 *       also make automated application deployment nearly impossible.
 *       Until these issues are resolved modal features may not
 *       be fully implemented.
 *            
 * @package    VWP
 * @subpackage Libraries.Archive
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License 
 */

class VInstaller extends VObject 
{
   
    /**
     * Default fail document
     *  
     * @var string $fail_doc Fail document
     * @access public
     */             
  
    public $fail_doc;
 
    /**
     * Base processing mode
     * 
     * Valid modes are "install", "uninstall", and "downgrade"   
     * 
     * @var string $base_mode Processing mode
     * @access public         
     */        
  
    public $base_mode;
    
    /**
     * Processing complete flag
     *
     * @var boolean $complete Processing complete
     * @access public
     */              
  
    public $complete;

    /**
     *  Processing successful flag
     *
     * @var boolean $success Processing successful
     * @access public
     */
  
    public $success;

  
    /**
     * Application ID
     * 
     * @var string $app_id Application ID
     * @access public
     */
                 
    public $app_id;

    /**
     * Theme Type
     * 
     * @var string $theme_type Theme Type
     * @access public
     */
                 
    public $theme_type;
 
    /**
     * Theme ID
     * 
     * @var string $theme_id Theme ID
     * @access public
     */
                 
    public $theme_id; 
 
    /**
     * Version
     *   
     * @var array $version Version
     * @access public  
     */
   
    public $version;
  
    /**
     * Base Version
     *   
     * @var array $base_version Base version
     * @access public  
     */
  
    public $base_version;
  
    /**
     * Release Date
     * 
     * @var string $release_date Release date
     * @access public      
     */
  
    public $release_date;
  
    /**
     * Module Name
     * 
     * @var string $name Module name
     * @access public    
     */
   
    public $name;
  
    /**
     * Module Author
     * 
     * @var string $author Author
     * @access public    
     */
        
    public $author;

    /**
     * Event list
     *      
     * @var array $events Event list
     * @access public
     */        
 
    public $events = array();    
    
    /**
     * Menu Links
     *      
     * @var array $menulinks Menu Links
     * @access public
     */        
 
    public $menulinks = array();   
    
    /**
     * Module Website
     * 
     * @var string $website Website
     * @access public    
     */
   
    public $website;
 
    /**
     * @var object $_vfile File interface
     * @access public  
     */
     
    protected $_vfile;

    /**
     * @var object $_vfolder Folder interface
     * @access public
     */
        
    protected $_vfolder;

    /**
     *  Current processing level
     * 
     * The processing level is an index into the array of 
     * available versions.
     *          
     * @var integer $_level Processing level
     * @access private       
     */
          
    protected $_level;
  
    /**
     * Current processing mode
     * 
     * Valid modes are "install", "uninstall", or "downgrade"   
     * 
     * @var string $_mode Processing mode
     * @access private         
     */        
  
    protected $_mode;    
    
    /**
     * Paths where files are installed into
     * 
     * Paths are indexed by type.
     * 
     * Valid path types are "root", "setup", "application", "theme" and "library".
     *
     * @var array $_install_path
     * @access private   
     */
          
    protected $_install_path = array();

    /**
     * Paths where files are installed from
     * 
     * Paths are indexed by type.
     * 
     * Valid path types are "root", "setup", "application", "theme" and "library".
     * 
     * @var array $_source_path
     * @access private   
     */
  
    protected $_source_path = array();
 
    /**
     * Stop processing
     * 
     * @var boolean $stop Stop processing
     * @access private         
     */
        
    protected $_stop;    
        
    /**
     * Install notices
     * 
     * @static
     * @var array $_install_notices Install notices
     * @access private
     */
         
    static $_install_notices;

    /**
     * Installer versions
     * 
     * @static
     * @var array $_installer_versions Installer Versions
     * @access private
     */
  
    static $_installer_versions = array(array(),array());

    /**
     * Initialize database tables
     * 
     * Themes, applications and plugins can override this
     * function to setup databases on install.
     * 
     * @access public
     */ 

    function initDB() 
    {
        // no database entries!
        return true;
    } 


    /**
     * Un Initialize database tables
     * 
     * Themes, applications and plugins can override this
     * function to clean-up databases on uninstall.
     * 
     * @access public
     */ 

    function uninitDB() 
    {
        // no database entries!
        return true;
    }

    /**
     * Copy files from package
     * 
     * @return True on success, error or warning otherwise
     * @access public    
     */    

    function copyfiles() 
    {

        // Identify install path
            	
        $install_path = $this->getSourcePath().DS.'base';  
  
        // Load manifest from install path
        
        $manifestFilename = $install_path.DS.'manifest.xml';  
        $manifest = VManifest::getInstance();  
        $result = $manifest->load($manifestFilename);
        if (VWP::isWarning($result)) {
            return $result;
        }     
  
        // Setup path mapping
        
        if (empty($this->app_id)) {
            $fmapping = array(
               "base"=>"theme",   
               "setup"=>"setup",
               );  	
        } else {
           $fmapping = array(
               "base"=>"application",
               "library"=>"library",
               "setup"=>"setup",
           );
        }

        // Build file list
        
        $files = array();
        $ver = implode(".",$this->version());             
        $folders = $manifest->getFolders($ver);        
                
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
        
        // Install files
        
        return $this->install_files($files);
    }

    /**
     * Delete files listed in package
     * 
     * @return True on success, error or warning otherwise
     * @access public    
     */    

    function deletefiles() 
    {
  
    	// Import module identifiers
    	
        $app_id = $this->getAppId();
        $theme_id = $this->getThemeId();
        $theme_type = $this->getThemeType();
  
        // Identify install path
        
        if (empty($app_id)) {
            $install_path = $this->getInstallPath('theme').DS.$theme_type.DS.$theme_id;
        } else {
            $install_path = $this->getInstallPath('application').DS.$app_id;	  	
        }
    
        // Load manifest from install path
        
        $manifestFilename = $install_path.DS.'manifest.xml';  
        $manifest = VManifest::getInstance();  
        $result = $manifest->load($manifestFilename);  
        if (VWP::isWarning($result)) {
            $result->ethrow();
            return $result;
        }     

        // Setup path mapping
        
        if (empty($manifest->_app)) {
            $fmapping = array(
                "base"=>"theme",    
                "setup"=>"setup",
               );
            $prefix = $theme_type.DS.$theme_id;  	
        } else {
            $fmapping = array(
                "base"=>"application",
                "library"=>"library",
                "setup"=>"setup",
               );
            $prefix = $app_id;
        }
        
        // Build file list        
                 
        $files = array();

        $ver = implode(".",$this->version());
        $folders = $manifest->getFolders($ver);
            
        foreach($folders as $type=>$flist) {
            $cur_folder_id = $fmapping[$type];
            if (!isset($files[$cur_folder_id])) {
                $files[$cur_folder_id] = array();
            }
   
            $cur_offset_path = false; // used for app modules
            foreach($flist as $fname) {   
                array_push($files[$cur_folder_id],array($cur_offset_path,$prefix.DS.$fname));
            }   
        }
      
        // Uninstall files
        
        return $this->uninstall_files($files);  
    }

 
    /**
     * Get list of events
     * 
     * @return array Events
     * @access public
     */         
 
    function getEvents() 
    {
        return $this->events;
    }
 
    /**
     * Set list of events
     * 
     * @param array $events Event list  
     * @return boolean True on success or false otherwise
     * @access public  
     */
         
    function setEvents($events) 
    {
        if (is_array($events)) {
            $this->events = $events;
            return true;
        }
        return false;
    }
 
    /**
     * Install events
     * 
     * @return boolean True on success
     * @access public
     */
         
    function installEvents() 
    {
        VWP::RequireLibrary('vwp.sys.events');
        $result = true;  
        $events = $this->getEvents();  
        foreach($events as $evt) {
            $e = VEvent::loadEvent($evt["type"],$evt["id"],$evt["filename"]);
            if (VWP::isWarning($e)) {
                $e->ethrow();
                $result = false;
            } else {
                $e->install();
            }   
        }
  
        return $result; 
    }

    /**
     * Get System Menu Map
     * 
     * @return array Map of menu systems to menu names
     * @access public
     */
    
    function getSystemMenuMap() {
        return array(
            "core_admin"=>"admin_topmenu",
            "app_admin"=>"admin_leftmenu"
        );	
    }
    
    /**
     * Get Menu Links
     * 
     * Valid Systems: "core_admin", "app_admin"
     * 
     * @return array Menu link lists indexed by system
     * @access public
     */
    
    function getMenuLinks() {
    	return $this->menulinks;
    }
    
    /**
     * Install Links
     * 
     * @return boolean True on success
     * @access public
     */
    
    function installMenuLinks() 
    {
       VWP::RequireLibrary('vwp.ui.menu');
       
       // Initialize Menu Link Install
       
       $menumap = $this->getSystemMenuMap();	
       $links = $this->getMenuLinks();
       $result = true;
       
       // Process Links
       
       foreach($links as $sys=>$items) {
           if (isset($menumap[$sys])) {
           	
           	   // Get Requested Menu
           	   
               $menu =& VMenu::getInstance($menumap[$sys]);
               if (VWP::isWarning($menu)) {
                   // create Menu
                   $menu =& VMenu::createMenu();
                   $menu->_id = $menumap[$sys];                   
               }
               
               // Add Items
               
               foreach($items as $itemInfo) {
                   
                   // Locate Item
                   $make = true;
                   $idx = 0;
                   $item =& $menu->getItem($idx);
               	   while(!VWP::isWarning($item)) {
               	   	   
               	   	   switch($item->_type) {
               	   	   	   case "applink":
               	   	   	   	   if ($itemInfo["type"] == 'applink') {
                                   if ($item->widget == $itemInfo["widget"]) {
                                       $make = false;
                                   }
               	   	   	   	   }
                               break;
               	   	   	   default:
               	   	   	   	   if ($itemInfo["type"] !== 'applink') {
               	   	   	   	       $make = false;
               	   	   	   	   }
               	   	   	       break;
               	   	   }
               	   	   
               	   	   unset($item);
               	   	   
               	   	   if ($make) {
               	   	       $idx++;
               	           $item =& $menu->getItem($idx);
               	   	   } else {
               	   	       $item = VWP::raiseWarning('Item found!','',null,false);	
               	   	   }
               	   }
               	   // Create Item
                   if ($make) {
                       $menu->insertItem($itemInfo);
                   }
               	
               }
               
               // Save Menu
               
               $sresult = $menu->save();
               if (VWP::isWarning($sresult)) {
                   $this->addNotice($sresult->errmsg,false);
                   $result = false;
               }
           } else {
           	   // Report Menu System Not Found
               $this->addNotice("Menu system '$sys' not found!",false);
           }         	           
       }
       
       return $result;	
    }

    /**
     * Install Links
     *
     * @return boolean True on success
     * @access public
     */
    
    function uninstallMenuLinks() 
    {
       VWP::RequireLibrary('vwp.ui.menu');
       
       // Initialize Menu Link Install
       
       $menumap = $this->getSystemMenuMap();	
       $links = $this->getMenuLinks();
       $result = true;
       
       // Process Links
       
       foreach($links as $sys=>$items) {
           if (isset($menumap[$sys])) {
           	
           	   // Get Requested Menu
           	   
               $menu =& VMenu::getInstance($menumap[$sys]);
               if (!VWP::isWarning($menu)) {
               
                   // Add Items
               
                   foreach($items as $itemInfo) {
                   
                       // Locate Item
                       
                       $idx = 0;
                       $item =& $menu->getItem($idx);
                   	   while(!VWP::isWarning($item)) {
                   	   	   $idx++;
                   	       $item =& $menu->getItem($idx);	
                   	   }
                   	   
                   	   for($idx = $idx - 1; $idx > -1; $idx--) {
               	   	       $item =& $menu->getItem($idx);
               	   	       $remove = false;
               	   	       
                   	   	   switch($item->_type) {
                   	   	   	   case "applink":
                   	   	   	   	   if ($itemInfo["type"] == 'applink') {
                                       if ($item->widget == $itemInfo["widget"]) {
                                           $remove = true;
                                       }
                   	   	   	   	   }
                                   break;
                   	   	   	   default:
                   	   	   	       break;
                   	   	   }
               	   	   
                   	   	   if ($remove) {                   	   	       
                   	           $menu->deleteItem($idx);
                   	   	   }
                   	   }               	
                   }
               }
               
               // Save Menu
               
               $sresult = $menu->save();
               if (VWP::isWarning($sresult)) {
                   $this->addNotice($sresult->errmsg,false);
                   $result = false;
               }
           } else {
           	   // Report Menu System Not Found
               $this->addNotice("Menu system '$sys' not found!",false);
           }         	           
       }
       
       return $result;	
    }
    
    
    /**
     * Set Menu Links
     * 
     * Valid Systems: "core_admin", "app_admin"
     * 
     * @param string $sys System
     * @param array $items Link list
     * @access public
     */
    
    function setMenuLinks($sys,$items) 
    {
        if (!isset($this->_links[$sys])) {
        	$this->menulinks[$sys] = $items;
        } else {
        	$this->menulinks[$sys] = array_merge($this->menulinks[$sys],$items);
        }	
    }
    
    /**
     * UnInstall events
     * 
     * @return boolean True on success
     * @access public
     */
         
    function uninstallEvents() 
    {
        VWP::RequireLibrary('vwp.sys.events');
        $result = true;  
        $events = $this->getEvents();  
        foreach($events as $evt) {        	
            $e = VEvent::loadEvent($evt["type"],$evt["id"],$evt["filename"]);
            if (VWP::isWarning($e)) {
                $e->ethrow();
                $result = false;
            } else {
                $e->uninstall();
            }   
        }
  
        return $result; 
    }
                            
    /**
     * Version Info
     *   
     * @return array Returns an array of Major, Minor, Sub version
     * @access public   
     */           

    function version() 
    {
        if (isset($this->version)) {
            return $this->version;
        }
        return array(0,0,0); 
    }

    /**
     * Base Version Info
     *   
     * @return array Returns an array of Major, Minor, Sub version
     * @access public   
     */           

    function base_version() 
    {
        if (isset($this->base_version)) {
            return $this->base_version;
        }
        return array(0,0,0); 
    }

    /**
     * Get Module Release Date
     *
     * @return string release date
     * @access public
     */
   
    function getReleaseDate() 
    {
        if (isset($this->release_date)) { 
            return $this->release_date;   
        }
        return "<Unknown>";
    }  

    /**
     * Get Module Name
     * 
     * @access public
     */
    
    function getName() 
    {
        if (isset($this->name)) {
            return $this->name;
        }
        return "<unnamed>";
    }    

    /**
     * Get Module Author
     * 
     * @return string Author
     * @access public
     */
    
    function getAuthor() 
    {
        if (isset($this->author)) {
            return $this->author;
        }  
        return "<unknown>";
    }

    /**
     * Get Author Website
     *
     * @return string Website
     * @access public
     */
    
    function getWebsite() 
    {
        if (isset($this->website)) {
            return $this->website;
        }
        return array("<unknown>",false);
    }  

    /**
     * Set Version
     * 
     * @param array $version Version ((int)Major, (int)Minor, (int)Sub)
     * @access public
     */   

    function setVersion($version) 
    {
        $this->version = $version;
    }
  
    /**
     * Set base version
     * 
     * @param array $version Version ((int)Major, (int)Minor, (int)Sub)
     * @access public
     */  
        
    function setBaseVersion($version) 
    {
        $this->base_version = $version;
    }
  
    /**
     * Set release date
     * 
     * @param string $release_date Release date
     * @access public         
     */
        
    function setReleaseDate($release_date) 
    {
        $this->release_date = $release_date;
    } 

    /**
     * Set Module Name
     *
     * @param string $name Module Name
     * @access public
     */
    
    function setName($name) 
    {
        $this->name = $name;   
    }    

    /**
     * Set Module Author
     * 
     * @param string $author Author
     * @access public
     */
    
    function setAuthor($author) 
    {
        $this->author = $author;   
    }

    /**
     * Set Author Website
     *
     * @param string $website_name Website Name
     * @param string $website_address Website address
     * @access public     
     */
    
    function setWebsite($website_name,$website_address = false) 
    {
        $this->website = array($website_name,$website_address);
    }  
  
    /**
     * Get list of versions 
     * 
     * @return array Returns an array of installer objects
     * @access public      
     */

    function getVersions() 
    {   
   
        $bname = implode(".",$this->base_version());   
 
        $ret = array();
  
        if (empty($this->app_id)) {
            foreach(self::$_installer_versions[1][$this->theme_type][$this->theme_id][$bname] as $key=>$val) {       
                array_push($ret,$val);
            }
        } else {
          foreach(self::$_installer_versions[0][$this->app_id][$bname] as $key=>$val) {       
              array_push($ret,$val);
          }  	  	
        }
        return $ret;
    }
  
    /**
     * Compare versions
     * 
     * Returns   
     * -1 If old version is higher,
     * 0 If versions are the same,
     * or 1 if versions are the same
     * 
     * @param array $old_version Old version
     * @param array $new_version New version      
     * @return integer Version compairison
     * @access public   
     */                    
  
    function compareVersions($old_version,$new_version) 
    {
        $v2 = $old_version;
        $v1 = $new_version;   
        for ($ptr = 0; $ptr < 3; $ptr++) {
            if ($v1[$ptr] < $v2[$ptr]) {
                return -1;
            }
            if ($v1[$ptr] > $v2[$ptr]) {
                return 1;
            }
        }
        return 0;
    }
         
    /**
     * Get processing status
     * 
     * @return true|false True on completion or false otherwise
     * @access public   
     */           

    function is_complete() 
    {        
        return isset($this->complete) && $this->complete ? true : false;
    }

    /**
     * Get processing result
     * 
     * @return true|false True on success or false otherwise
     * @access public   
     */           

    function is_success() 
    {
        return isset($this->success) && $this->success ? true : false;
    }

    /**
     * Set install path
     *
     * Valid path types are "root", "setup", "application", "theme" and "library".
     * 
     * @param string $path Path to install into
     * @param string $type Path type   
     * @access public
     */
                   
    function setInstallPath($path,$type="root") 
    {
        $this->_install_path[$type] = $path;
    }

    /**
     * Get install path
     * 
     * Valid path types are "root", "setup", "application", "theme" and "library".
     * 
     * @param string $type Path type     
     * @return string|object Install path or warning if not found
     * @access public
     */
                 
    function getInstallPath($type = "root") 
    {
        if (!isset($this->_install_path[$type])) {
            return VWP::raiseWarning("Path not found",get_class($this).":getInstallPath",null,false);
        }
        return $this->_install_path[$type];
    }

    /**
     * Set source path
     *
     *  Valid path types are "root", "setup", "application", "theme" and "library".
     *  
     * @param string $path Source path
     * @param string $type Path type   
     * @access public
     */
                 
    function setSourcePath($path,$type = "root") 
    {
        $this->_source_path[$type] = $path;
    }

    /**
     * Get source path
     *
     * Valid path types are "root", "setup", "application", "theme" and "library".
     * 
     * @param string $type Path type 
     * @return string|object Source path or warning if not found
     * @access public
     */  
  
    function getSourcePath($type = "root") 
    {

        if (!isset($this->_source_path[$type])) {
            return VWP::raiseWarning("Path not found",get_class($this).":getSourcePath",null,false);
        }   
        return $this->_source_path[$type];
    }

    /**
     * Get setup path
     * 
     * @return Setup path
     * @access public    
     */
  
    function getSetupPath() 
    {
        switch($this->base_mode) {
            case "install":
                return $this->getSourcePath("setup");
                break;
            case "uninstall":
            case "downgrade":
                return $this->getInstallPath("setup");
                break;
            default:
                return false;
                break;
        }
        return false;
    }

    /**
     * Set application ID
     * 
     * @param string $id Application ID
     * @access public
     */
              
    function setAppId($id) 
    {
        $this->app_id = $id;
    }
  
    /**
     * Get application ID
     * 
     * @return string Application ID
     * @access public
     */
                 
    function getAppId() 
    {
        return $this->app_id;
    }

    /**
     * Set Theme Type
     * 
     * @param string $themeType Theme Type
     * @access public
     */
              
    function setThemeType($themeType) 
    {
        $this->theme_type = $themeType;
    }
  
    /**
     * Get theme type
     * 
     * @return string Theme type
     * @access public
     */
                 
    function getThemeType() 
    {
        return $this->theme_type;
    }
 
 
    /**
     * Set Theme ID
     * 
     * @param string $id Theme ID
     */
              
    function setThemeId($id) 
    {
        $this->theme_id = $id;
    }
  
    /**
     * Get Theme ID
     * 
     * @return string Theme ID
     * @access public
     */
                 
    function getThemeId() 
    {
        return $this->theme_id;
    }
  
    /**
     * Set install level
     * 
     * Note: Use with caution. This feature is tied in with modal install features
     * 
     * @param integer $level Install level
     * @access public
     */
                 
    function setInstallLevel($level) 
    {
        $this->_level = $level;     
    }

    /**
     * Get install level
     * 
     * @return integer Install level
     * @access public
     */              

    function getInstallLevel() 
    {
        return $this->_level;  
    }

    /**
     * Get processing mode
     * 
     * Valid modes are "install", "uninstall", or "downgrade"
     * 
     * @return string Processing mode
     * @access public
     */
              
    function getMode() 
    {
        return $this->_mode;
    }     

    /**
     * Get User Notices
     * 
     * @return array User messages
     * @access public
     */
                
    function getNotices() 
    {   
        $ret = array();
        if (!isset(self::$_install_notices)) {
            return array();
        }
        foreach(self::$_install_notices as $msg) {
            array_push($ret,array($msg[1],$msg[2]));
        }
        return $ret;
    }

    /**
     * Get User Notices with processing mode
     * 
     * @return array User messages
     * @access public
     */

    function getExtendedNotices() 
    {   
        if (!isset(self::$_install_notices)) {
            return array();
        }
        return self::$_install_notices;
    }

    /**
     * Add a user message
     * 
     * @param string $msg The user message
     * @param true|false $result False for error or true otherwise
     * @access public   
     */              
 
    function addNotice($msg,$result = true) 
    {
   
        if (!isset(self::$_install_notices)) {
            self::$_install_notices = array();
        }
        array_push(self::$_install_notices,array($this->_mode,$msg,$result));
        return $result;
    }
    
    /**
     * Set Fail Document
     *
     * Note: This is a modal install feature. Use with caution
     *       as many install applications do not support modal installs.
     *       
     * @param string $doc Document filename
     * @access public
     */
    
    function setFailDoc($doc) 
    {
        if (file_exists($doc)) {
            $this->fail_doc = $doc;
            return true;
        } else {
            return VWP::raiseWarning("Fail Document $doc is missing!",get_class($this) . ":setFailDoc",500,false);
        }
    }
  
    /**
     * Display a document
     * 
     * Loads a display document
     * 
     * @param string $doc Document filename
     * @return true|object True on success or error otherwise
     * @access public   
     */                  
  
    function load_doc($doc) 
    {
        if (file_exists($doc)) {
            require($doc);
            return true;
        }
        $docname = basename($doc);
        return VWP::raiseError("Document $docname missing!",get_class($this).":load_doc",null,false);
    }

    /**
     * Prompt the user
     * 
     * This function will pause processing and display
     * the document to the user.
     * 
     * Note: This is a modal install feature. Use with caution
     *       as many installer applications do not support
     *       modal installs.
     * 
     * @param string $doc Document filename
     * @access public
     */
                        
    function prompt($doc) 
    {   
        $result = $this->load_doc($doc);     
        $this->_stop = true;
        return $result;
    }

    /**
     * Fail processing
     * 
     * If in install mode uninstall mode will start.
     * If not in install mode the installer will return
     * as not successful. 
     * 
     * Note: If this is a modal installer
     * than the fail document will be displayed.
     * 
     * @access public   
     */
              
    function fail() 
    {
        if ($this->_mode == "install") {
            $this->continue_uninstall();
        } else {
   
            if (empty($this->fail_doc)) {
                $this->complete = true;
                $this->success = false;
            } else {
                $result = $this->prompt($this->fail_doc);
                if (VWP::isWarning($result)) {
                    $result->ethrow();
                }   
            }   
        }
    }

    /**
     * Version Install handler
     * 
     * This function should be overriden by child classes
     * 
     * @access public   
     */
  
    function version_install() {
        $this->fail();
        return VWP::raiseError('Version Installer Missing!',get_class($this).":version_install",500,false);
    }
  
    /**
     * Setup an install process
     * 
     * This function is called by an installers process() function
     * to setup the initial install state.
     * 
     * Note: This function resets a modal install to its starting point. While
     *       modal installs should be avoided a modal installer
     *       should call this function on the initial user request
     *       and use VInstaller::setInstallLevel() after user prompts.
     *        
     * @access public      
     */
        
    function init_install() {
        $this->setInstallLevel(1);
    }
  
    /**
     * Trigger end of install process
     * 
     * @access public
     */
                
    function complete_install() {
        $this->complete = true;  
    }
    
    /**
     * Continue processing of an install process
     * 
     * @access public   
     */
                
    function continue_install() 
    {
  
        // initialize install environment
    
        $this->_mode = "install";    
        $base_version = $this->base_version();  
        $current =& $this->getCurrentVersion();
    
        $mode_upgrade = false;
        $mode_downgrade = false;
    
        if ($current === false) {
            $current_version = false;     
        } else {     
            $current_version = $current->version();
            $current_versions = $current->getVersions();
            
            $vdir = $this->compareVersions($current_version,$base_version);
            if ($vdir > 0) {
                $mode_upgrade = true;                
            } elseif ($vdir < 0) {
                $mode_downgrade = true;                
            } else {            	
            }
            
            
        }  
              
        $ptr = $this->getInstallLevel() - 1;    
        $versions = $this->getVersions();    
        $stop = false;    
   
        // Process downgrade uninstalls
        if ($mode_downgrade) {
            $cur_ver_count = count($current_versions);
   
            if ($ptr < $cur_ver_count) {

                $stop = false;
                while (!$stop) {
                    $idx = $cur_ver_count - ($ptr + 1);        
                    if ($this->compareVersions($current_versions[$idx]->version(),$base_version) < 0) { 
                    	
                        $current_versions[$idx]->_install_path = $this->_install_path;
                        $current_versions[$idx]->_source_path = $this->_source_path;
                        $current_versions[$idx]->base_mode = $this->base_mode;
                        $current_versions[$idx]->setInstallLevel($ptr + 1);                
                        $current_versions[$idx]->version_uninstall();
        
                        if (!$current_versions[$idx]->complete) {
                            $stop = true;
                            $this->_stop = true;
                            $this->complete = false;
                            $success = false;         
                        } else if (!$current_versions[$idx]->success) {        
                            $success = false;
                            $this->success = false;
                            $stop = true;
                        } else {
                            $ptr++;
                            if ($ptr >= $cur_ver_count) {
                                $stop = true;
                                $success = true;
                            }        
                        }       
                    } else {
                    	
                        $ptr++;
                        if ($ptr >= $cur_ver_count) {
                            $stop = true;
                            $success = true;
                        }
                    }
                }
      
                if ($success) {
                    $stop = false;
                    $ptr = $ptr - $cur_ver_count;
                } else {
                    $this->_stop = true;
                    $stop = true;
                }
    
            } else {
                $ptr = $ptr - $cur_ver_count;
            }        
        }
            
        // skip installs if invalid level
        if (($ptr < 0) || $ptr >= count($versions)) {
            $this->_stop = true;
        } else {
            $this->_stop = false;
        }
   
        // skip installs if there was a stop from uninstall
        if ($stop) {
            $this->_stop = true;     
        }
        
        // Process Install(s)
    
        while(!$this->_stop) {
    
            $versions[$ptr]->_install_path = $this->_install_path;
            $versions[$ptr]->_source_path = $this->_source_path;
            $versions[$ptr]->base_mode = $this->base_mode;
            $versions[$ptr]->setInstallLevel($ptr + 1);
            $skip = false;
     
            if (
                ($mode_upgrade) && 
                ($current_version !== false) &&
                ($this->compareVersions($current_version,$versions[$ptr]->version()) <= 0)
               ) {     
                $skip = true;
            }
     
            if (
                ($mode_downgrade) &&          
                ($this->compareVersions($this->version(),$versions[$ptr]->version()) != 0)
               ) {     
                $skip = true;
            }
     
     
            if (!$skip) {          
                $vr = $versions[$ptr]->version_install();
                if (VWP::isWarning($vr)) {       
                    return $vr;
                }      
                if (!$versions[$ptr]->complete) {
                    $this->_stop = $versions[$ptr]->_stop;
                } else if (!$versions[$ptr]->success) {
                    $this->_stop = true;
                    $this->complete = true;
                    $this->success = false;
                }
            }
            if (!$this->_stop) {
                $ptr++;      
                if ($ptr >= count($versions)) {
                    $this->_stop = true;
                    $this->complete = true;
                    $this->success = true;
                }
            }    
        }        
    }
  
    /**
     * Start install process
     * 
     * Begin an install process.
     * 
     * This function will reset the install state to the begining
     * and call VInstaller::continue_install().
     *  
     * @access public
     */
              
    function install() 
    {
        $this->_mode = "install";
        $this->init_install();   
        $this->continue_install(); 
    }
  
    /**
     * Version Install handler
     * 
     * This function should be overriden by child classes
     * 
     * @access public   
     */
       
    function version_uninstall() 
    {
        VWP::raiseWarning('Missing version_uninstall()',get_class($this));
        $this->fail();
    }
  
    /**
     * Setup an uninstall process
     * 
     * Note: This function need not be called directly 
     * as it is called automatically by the VInstaller::uninstall(). 
     *  
     * @access public      
     */
   
    function init_uninstall() 
    {
        $this->setInstallLevel(count($this->getVersions()));
    }

    /**
     * Trigger end of uninstall process
     * 
     * @access public
     */
     
    function complete_uninstall() 
    {
        $this->complete = true;  
    }
  
    /**
     * Continue processing of an uninstall process
     * 
     * Note: This function is called automatically by the VInstaller:uninstall() function.
     * While modal installers should be avoided, only modal installers need to 
     * call this function directly as the uninstall() function also resets the installer
     * to the initial uninstall state.
     * 
     * @access public   
     */
     
    function continue_uninstall() 
    {
        $this->_mode = "uninstall";    
        $ptr = $this->getInstallLevel() - 1;    
        $versions = $this->getVersions();
        if (($ptr < 0) || $ptr >= count($versions)) {
            $stop = true;
        } else {
            $stop = false;
        }
        while(!$stop) {    

            $versions[$ptr]->_install_path = $this->_install_path;
            $versions[$ptr]->_source_path = $this->_source_path;
            $versions[$ptr]->base_mode = $this->base_mode;
            $versions[$ptr]->setInstallLevel($ptr + 1);    
            $versions[$ptr]->version_uninstall();     
         
            if (!$versions[$ptr]->complete) {
                $stop = true;
                $complete = false;
            } else if (!$versions[$ptr]->success) {
                //  $this->_stop = true;
                $complete = true;
                $success = false;
                $stop = true;
            } else {
                $ptr--;
                if ($ptr < 0) {
                    $stop = true;
                    $complete = true;
                    $success = true;
                }      
            }
        }
    
        $this->complete = $complete;
        if ($complete) {
            $this->success = $success;
        }
    
        if ($ptr < 0) { 
            $this->complete_uninstall();     
        }    
    }

     /**
      * Start uninstall process
      * 
      * @access public
      */

    function uninstall() 
    {
        $this->_mode = "uninstall";  
        $this->init_uninstall();   
        $this->continue_uninstall(); 
    }


    /**
     * Check for available upgrades
     * 
     * Reserved for future use
     *      
     * @return boolean True if upgrades available or false otherwise
     * @access public
     */           
  
    function check_updates() 
    {
        return false;
    }
 
    /**
     * Entry point function
     * 
     * This should be overridden by child classes
     * 
     * @param string $mode Processing mode            
     */
              
    function process($mode) 
    {
        return VWP::raiseError("Install processor is missing",get_class($this).":process",500,false);
    }
  
    /**
     * Check if version meets requirements
     *
     * Reserved for future use
     *
     * @todo Implement version requirements
     * @param array $min Minimum version
     * @param array $max (optional) Maximum version       
     * @return boolean True if meets requirements or false otherwise
     * @access public   
     */
              
    function require_version($min,$max = false) 
    {  
        return false;
    }
 
    /**
     * Run a group of install tasks
     * 
     * Note: Processing stops if one task fails      
     * 
     * @param array $tasks tasks
     * @return mixed True on success
     * @access public
     */                 
 
    function runAll($tasks) 
    {
        $result = true;     
        foreach($tasks as $task) {
            if ((!VWP::isWarning($result)) && ($result)) {  
                $result = $this->run($task);  
            }
        }
        return $result;
    }
  
    /**
     * Run a install task
     * 
     * @param string $task
     * @return mixed True on success
     * @access public
     */              
  
    function run($task) 
    {
        if (!method_exists($this,$task)) {
            return true;
        }
        return $this->$task();  
    }
  
    /**
     * Stop processing request
     *
     * @param true|false $result True if successful or false otherwise
     * @access public       
     */
           
    function finish($result = false) 
    {
        $this->complete = true;
        if (VWP::isWarning($result)) {
            $this->success = false;
        } else {
            $this->success = $result;
        }
        if ($this->success) {
            $this->addNotice("[" . implode(".",$this->version()) . "] Installed!",true);
        } else {      
            $this->addNotice("[" . implode(".",$this->version()) . "] Install Failed!",false);
        }      
    }
   
    /**
     * Delete install files and remove empty folders
     * 
     * Note: This function should not be called directly. Use VInstaller::deletefiles()
     * 
     * @param string $filename File to delete
     * @param string $type Subsystem type
     * @return true|object True on success, error or warning otherwise
     * @access public          
     */
  
    function idelete($filename,$type = "root") 
    {
   
        $result = $this->_vfile->delete($filename);
        if (VWP::isWarning($result)) {
            return $result;
        }
      
        $install_path = $this->getInstallPath($type);
   
        $path = dirname($filename);
   
        while (
          (strlen($path) > 0) && 
          ($path != $install_path) &&
          (dirname($path) != $install_path) &&
          ($this->_vfolder->is_empty($path))
         ) {             
            $this->_vfolder->delete($path);
            $path = dirname($path);
        }   
        return true;
    }
       
    /**
     * Copy install files
     * 
     * Note: This function should not be called directly. Use VInstaller::copyfiles()
     * 
     * @param string $src Source file
     * @param string $dest Destination file    
     * @return true|object True on success, error or warning on failure
     * @access public      
     */     
  
    function icopy($src,$dest) 
    {
        $src = v()->filesystem()->path()->clean($src);
        $dest = v()->filesystem()->path()->clean($dest);
        $parent = dirname($dest);
        if (!$this->_vfolder->exists($parent)) {
            $this->_vfolder->create($parent);
        }
        return $this->_vfile->copy($src,$dest,'',true);
    }
  
    /**
     * Wipe setup files
     * 
     * Note: This function will do nothing if any module paths still exist
     * 
     * @access public
     */
  
    function wipe_setup() 
    {
        $vfolder =& v()->filesystem()->folder();
        
        $app_id = $this->getAppId();
  
        if (empty($app_id)) {  	
            $prefix = $this->getThemeType().DS.$this->getThemeId();
            $setup_prefix = 'themes'.DS.$prefix;
            $paths = array("theme");
        } else {
            $prefix = $app_id;
            $setup_prefix = $app_id;
            $paths = array("application","library","theme");
        }
  
        $wipe = true;
        foreach($paths as $type) {
            $install_path = $this->getInstallPath($type);
            if ($vfolder->exists($install_path.DS.$prefix)) {
                $wipe = false;
            }   
        }

        $install_path = $this->getInstallPath("setup");
  
        if ($wipe) {
            $vfolder->delete($install_path.DS.$setup_prefix);    
        }
    }
        
    /**
     * Install files
     * 
     * @param array File list indexed by subsystem type
     * @return true|object True on success, error or warning otherwise
     * @access public        
     */
   
    function install_files($files) 
    {

        $vpath =& v()->filesystem()->path();
        
        $prefix = $this->app_id;
       
        if (empty($prefix)) {  	
  	        $prefix = $this->theme_type . DS . $this->theme_id;
  	        $setup_prefix = 'themes'.DS.$this->theme_type . DS . $this->theme_id;
        } else {
        	$setup_prefix = $prefix;
        }
  	
        foreach($files as $type=>$file_list) {
            $source_path = $vpath->clean($this->getSourcePath($type));
            
            if ($type == "setup") {
            	$dest_path = $vpath->clean($this->getInstallPath($type) .DS.$setup_prefix);
            } else {
                $dest_path = $vpath->clean($this->getInstallPath($type) .DS.$prefix);   
            }
            
            foreach($file_list as $file_info) {
                $folder_path = "";
                if ($file_info[0] !== false) {
                    $folder_path = $vpath->clean($file_info[0]);
                }
    
                if ($type == "setup") {
                    $folder_path .= DS.'setup';
                }
       
                $fname = $vpath->clean($file_info[1]);    
                while(substr($fname,0,strlen(DS)) == DS) {
                    $fname = substr($fname,strlen(DS));
                }
                $src = $source_path.DS.$fname;    
    
                $dst = $dest_path.$folder_path.DS.$fname;
    
                $result = self::icopy($src,$dst);    
                if (VWP::isWarning($result)) {
                    return $result;
                }     
            }
            if (!isset($result)) { 
                $result = true;
            }
        }
   
        if (!isset($result)) {
            return true;	
        }
        
        return $result;
    }     

    /**
     * UnInstall files
     * 
     * @param array $files File info list
     * @return boolean True on success        
     */
   
    function uninstall_files($files) 
    {
        $prefix = $this->app_id;
        if (empty($prefix)) {  	
  	        $prefix = $this->theme_type . DS . $this->theme_id;
  	        $setup_prefix = 'themes'.DS.$this->theme_type . DS . $this->theme_id;
        } else {
        	$setup_prefix = $prefix;
        }
   	  
   	  $vpath =& v()->filesystem()->path();
            
        foreach($files as $type=>$file_list) {
      
            $source_path = $vpath->clean($this->getSourcePath($type));
            
            if ($type == "setup") {
            	$dest_path = $vpath->clean($this->getInstallPath($type) .DS.$setup_prefix);
            } else {
                $dest_path = $vpath->clean($this->getInstallPath($type) .DS.$prefix);   
            }
            
            if ($type != "setup") {    
    
                $dest_path = $this->getInstallPath($type);
    
                foreach($file_list as $file_info) {
                    $folder_path = "";
                    if ($file_info[0] !== false) {
                        $folder_path = $file_info[0];
                    }
       
                    $fname = $vpath->clean($file_info[1]);
                    while(substr($fname,0,strlen(DS)) == DS) {
                        $fname = substr($fname,strlen(DS));
                    }
                  
                    $src = $source_path.DS.$fname;        
                    $dst = $dest_path.$folder_path.DS.$fname;
              
                    $result = self::idelete($dst);
                    if (VWP::isWarning($result)) {            
                        $this->addNotice("Unable to delete $dst",false);
                        $result = false;       
                    }      
                }
            } 
            if (!isset($result)) {
                $result = true;
            }
        }
   
        if (!isset($result)) {
        	return true;
        }
        return $result;
    }     
  
    /**
     * Register installer object
     * 
     * This function is performed automatically
     * by the class constructor.               
     */     
  
    function registerVersion() 
    {   
         
        $bname = implode(".",$this->base_version());
        $cname = implode(".",$this->version());
        $appid = $this->app_id;
        $themeType = $this->theme_type;
        $themeId = $this->theme_id;

        if (empty($appid)) {
  	
            //  Register Theme Installer
   
            if (!isset(self::$_installer_versions[1][$themeType])) {
                self::$_installer_versions[1][$themeType] = array();   
            }

            if (!isset(self::$_installer_versions[1][$themeType][$themeId])) {
                self::$_installer_versions[1][$themeType][$themeId] = array();   
            }
      
            if (!isset(self::$_installer_versions[1][$themeType][$themeId][$bname])) {
                self::$_installer_versions[1][$themeType][$themeId][$bname] = array();
            }

            self::$_installer_versions[1][$themeType][$themeId][$bname][$cname] = &$this;   
  
        } else {
 	
            // Register App Installer
  
            if (!isset(self::$_installer_versions[0][$appid])) {
                self::$_installer_versions[$appid] = array();   
            }
            if (!isset(self::$_installer_versions[0][$appid][$bname])) {
                self::$_installer_versions[$appid][$bname] = array();
            }
            self::$_installer_versions[0][$appid][$bname][$cname] =& $this;
        }    	
    }
    
    /**
     * Get current installed version
     * 
     * @return boolean|VInstaller Returns false if no version is installed, or the version installer otherwise
     * @access public
     */
              
    function &getCurrentVersion() 
    {  
    	$cver = false;
    	
    	$app_id = $this->app_id;
    	$install_path = $this->getInstallPath('application').DS.$app_id;
    	
    	$manifest =& VManifest::getInstance();
    	$result = $manifest->load($install_path.DS.'manifest.xml');
    	if (!VWP::isWarning($result)) {
    	    $ver = $manifest->version;
    	    $setup_path = $this->getInstallPath('setup').DS.$app_id.DS.'setup';
    	    $className = ucfirst($app_id.'_'. str_replace('.','_',$ver).'_Base');    	    
    	    if (!class_exists($className)) {
    	        require_once($setup_path.DS.'installer.php');
    	    }
    	    $className = ucfirst($app_id.'_'. str_replace('.','_',$ver).'_Installer');    	    
    	    $cver = new $className;    	    
    	}
    	
        return $cver;
    }
  
    /**
     * Class constructor
     * 
     * Note: If overriden must still be called by child classes with parent::__construct()
     *       
     * @access public   
     */
           
    function __construct() 
    {  
        $this->_vfolder =& v()->filesystem()->folder();
        $this->_vfile =& $this->_vfolder->getFileInstance();  
        $this->setInstallPath(VPATH_BASE);
        $this->setInstallPath(VPATH_BASE.DS.'Applications','application');
        $this->setInstallPath(VPATH_BASE.DS.'libraries','library');
        $this->setInstallPath(VPATH_BASE.DS.'themes','theme');
        $this->setInstallPath(VWP::getVarPath('packages'),'setup');  
        $this->registerVersion();  
    }
     
} // end class
