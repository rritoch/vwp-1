<?php

/**
 * VWP Install Model 
 *  
 * @package    VWP
 * @subpackage Models
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @link http://www.vnetpublishing.com VNetPublishing.Com 
 * @copyright (c) Ralph Ritoch - All rights reserved
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License
 * @todo Test theme install  
 */

/**
 * Require Model Support
 */

VWP::RequireLibrary('vwp.model');

/**
 * Require File Support
 */

VWP::RequireLibrary('vwp.filesystem.file');

/**
 * Require Path Support
 */

VWP::RequireLibrary('vwp.filesystem.path');

/**
 * Require Manifest Support
 */

VWP::RequireLibrary('vwp.archive.manifest');

/**
 * VWP Install Model
 *  
 * @package VWP
 * @subpackage Models
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @link http://www.vnetpublishing.com VNetPublishing.Com 
 * @copyright (c) Ralph Ritoch - All rights reserved
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License
 */

class VWP_Model_Install extends VModel 
{
    /**
     * Installer
     * 
     * @var object Installer
     */
	
	var $_installer;

    /**     
     * UnInstall application
     * 
     * @param string $appId
     * @return boolean|object True on success, error or warning otherwise
     * @access private
     */
	
    function _doUninstall($appId) 
    {
           
        $setup_folder = VWP::getVarPath('packages').DS.$appId;
        $app_folder = VPATH_BASE.DS.'Applications'.DS.$appId;
           
        // verify installer exists 
            
        $installerFilename = v()->filesystem()->path()->clean($setup_folder.DS.'setup'.DS.'installer.php');
        if (!$this->_vfile->exists($installerFilename)) {
            return VWP::raiseError("Installer $installerFilename not found!",get_class($this).":doInstall",500,false);
        }
  
        // load manifest to get installer class name
        $manifestFilename = $app_folder.DS.'manifest.xml';
        if (!$this->_vfile->exists($manifestFilename)) {
            return VWP::raiseError("Manifest not found!",get_class($this).":doInstall",500,false);
        }

        $manifest = VManifest::getInstance();  
        $result = $manifest->load($manifestFilename);
        if (VWP::isWarning($result)) {
            return $result;
        }  
  
        $verstr = str_replace(".","_",$manifest->version);
        $className = ucfirst(strtolower($manifest->_app)) . '_'. $verstr.'_Installer';
        require_once($installerFilename);
        if (!class_exists($className)) {
            return VWP::raiseError("Installer $className not found!",get_class($this).":doInstall",510,false);   
        }  
  
        $this->_installer = new $className; 
        $result = $this->_installer->process("uninstall");
           
        $notices = $this->_installer->getNotices();
           
        foreach($notices as $notice) {
            if ($notice[1]) {
                VWP::addNotice($notice[0]);
            } else {
                VWP::raiseWarning($notice[0],get_class($this->_installer));
            }
        }
           
        if (VWP::isWarning($result)) {
            return $result;
        }  
        
        $result = array(
               "complete"=>$this->_installer->is_complete(),
               "success"=>$this->_installer->is_success()
           );
           
        if ($result["complete"]) {
            if ($result["success"]) {
                return true;
            } else {
                return VWP::raiseWarning("Uninstall of '$appId' failed!",get_class($this),null,false);
            }
        }
           
        return false;                
    }
    
    /**     
     * UnInstall applications
     * 
     * @param array $apps Application list
     * @return boolean|object True on success, error or warning otherwise
     * @access public
     */
    
    function uninstall($apps) 
    {
 
        $ok = true;
        foreach($apps as $appId) {  
            $result = $this->_doUninstall($appId);
            if (VWP::isWarning($result)) {
                $result->ethrow();
                $ok = VWP::raiseWarning('Some packages failed to uninstall',get_class($this),null,false);
            } else {
                if ($result === true) {
                    VWP::addNotice('Uninstalled: ' . $appId);
                } else {
                    VWP::raiseWarning('Uninstall pause not supported!',get_class($this));
                }                
            }
        }     
        return $ok;  
   }
 
   /**    
    * Get application path
    * 
    * @return string Application path
    * @access public
    */
   
    function getAppBaseDir() 
    {
        return VPATH_BASE.DS.'Applications';
    }
 
    /**     
     * Get manifest filename
     * @param string $appID Application ID
     */
    
    function getManifestFilename($appID) 
    {
        $appBaseDir = $this->getAppBaseDir();
        return $appBaseDir.DS.$appID.DS.'manifest.xml'; 
    }
 
    /**     
     * Get manifest
     * @param unknown_type $appID
     * @return object Manifest on success, error or warning otherwise
     */
    
    function &getManifest($appID) 
    { 
        $manifest =& VManifest::getInstance();  
        $result = $manifest->load($this->getManifestFilename($appID));
        if (VWP::isWarning($result)) {
            return $result;
        }  
  
        return $manifest;
    }
 
    /**     
     * Get application info
     * 
     * @param string $appID
     * @return array Application info
     * @access public
     */
    
    function getApplicationInfo($appID) 
    {
        $info = array();
  
        $vfile =& v()->filesystem()->file();
  
        $file = $this->getManifestFilename($appID);
        if ($vfile->exists($file)) {
            $manifest =& $this->getManifest($appID);   
            if (!VWP::isWarning($manifest)) {
                $info = $manifest->getInfo();
            }
        }
        if ((!isset($info["name"])) || (empty($info["name"]))) {
            $info["name"] = $appID;
        }
        $info["app_id"] = $appID;
        return $info;
    }
 
    function getApplications() 
    {
        $appBaseDir = $this->getAppBaseDir();
        $vfolder =& v()->filesystem()->folder();
  
        $apps = $vfolder->folders($appBaseDir);
        $app_list = array();
        foreach($apps as $name) {
            $app = array(
             "id"=>$name,
             "name"=>$name,    
             "author"=>'',
             "author_email"=>'',
             "author_link"=>'',
             "version"=>'',
             "version_release_date"=>'',
            );
     
            $vfile =& v()->filesystem()->file();
       
            $manifestFilename = $this->getManifestFilename($name);
            if ($vfile->exists($manifestFilename)) {
                $manifest = $this->getManifest($name);
                if (VWP::isWarning($manifest)) {
                    $app["manifest"] = "Invalid";     
                } else {     
                    $labels = $manifest->getInfo();
                    $app = array_merge($app,$labels);
                    $app["manifest"] = "Valid";
                    $app["id"] = $name;     
                }
            } else {
                $app["manifest"] = "Missing";
                $app["name"] = $name;
            }
            array_push($app_list,$app);
        }
        return $app_list;  
    }

    /**     
     * Install module from folder
     * 
     * @param string $source_folder
     * @return boolean|object True on success, error or warning otherwise
     * @access public
     */
 
    function doInstall($source_folder) 
    {
  
        // verify installer exists  
        $installerFilename = v()->filesystem()->path()->clean($source_folder.DS.'setup'.DS.'installer.php');
        if (!$this->_vfile->exists($installerFilename)) {
            return VWP::raiseError("Installer $installerFilename not found!",get_class($this).":doInstall",500,false);
        }
  
        // load manifest to get installer class name
        $manifestFilename = $source_folder.DS.'base'.DS.'manifest.xml';
        if (!$this->_vfile->exists($manifestFilename)) {
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

    /**     
     * Install module from pacakge
     * 
     * @param string $package_file Package file
     * @return boolean|object True on success, error or warning otherwise
     * @access public
     */
    
    function doInstallPackage($package_file) 
    {
        VWP::RequireLibrary('vwp.archive.archive');
        $tempFolder = $this->_vfolder->mktemp("install");
        if (VWP::isWarning($tempFolder)) {
            return $tempFolder;
        }
        $arch = VArchive::getInstance();
        $result = $arch->extract($package_file,$tempFolder);
        if (VWP::isWarning($result)) {
            return $result;
        }
        $result = $this->doInstall($tempFolder);
        $this->_vfolder->delete($tempFolder);
        return $result;
    }

    /**     
     * Class constructor
     * 
     * @access public
     */
    
    function __construct() {
        parent::__construct();
    }
    
    // End class VWP_Model_Install
}