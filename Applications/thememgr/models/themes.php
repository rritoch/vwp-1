<?php

/**
 * Themes Model
 *  
 * @package    VWP.ThemeMgr
 * @subpackage Widgets
 * @author Ralph Ritoch <rritoch@gmail.com> 
 * @copyright (c) Ralph Ritoch - All rights reserved
 * @link http://www.vnetpublishing.com 
 */

VWP::RequireLibrary("vwp.model");
VWP::RequireLibrary('vwp.archive.manifest');

/**
 * Themes Model 
 *  
 * @package    VWP.ThemeMgr
 * @subpackage Widgets
 * @author Ralph Ritoch <rritoch@gmail.com> 
 * @copyright (c) Ralph Ritoch - All rights reserved
 * @link http://www.vnetpublishing.com 
 */
 
class ThemeMgr_Model_Themes extends VModel 
{
	var $_params = array();
	
    /**
     * Get Themes Base Folder
     */

    function getThemeBase() {
        return VPATH_ROOT.DS.'themes';
    }
    
	/**
	 * Get Theme Manifest Filename
	 * 	 
	 * @param string $themeType
	 * @param string $themeId
	 * @return string Theme Manifest Filename
	 * @access public
	 */
 
	function getManifestFilename($themeType,$themeId) 
	{
        $themeBaseDir = $this->getThemeBase();
        return $themeBaseDir.DS.$themeType.DS.$themeId.DS.'manifest.xml'; 
    }

    /**
	 * Get Theme Manifest
	 * 	 
	 * @param string $themeType
	 * @param string $themeId	 	 
	 * @return object VManifest Theme manifest on success, error or warning otherwise
	 * @access public	 
	 */
    
    function getManifest($themeType,$themeId) { 
        $manifest = VManifest::getInstance();  
        $result = $manifest->load($this->getManifestFilename($themeType,$themeId));
        if (VWP::isWarning($result)) {
            return $result;
        }    
        return $manifest;
    }
 
    /**
     * Get Theme Info
     *      
     * @param string $themeType Theme Type
     * @param string $themeId Theme ID
     */
    
    function getThemeInfo($themeType,$themeId) 
    {
        $info = array();  
          
        $file = $this->getManifestFilename($themeType,$themeId);
        if ($this->_vfile->exists($file)) {
            $manifest = $this->getManifest($themeType,$themeId);   
            if (VWP::isWarning($manifest)) {
                return $manifest;
            }
            $info = $manifest->getInfo();
        }
        
        if ((!isset($info["theme_name"])) || (empty($info["theme_name"]))) {
            $info["theme_name"] = $themeId;
        }
        $default = VThemeConfig::getDefaultTheme($themeType);
                
        $info["themeId"] = $themeId;
        $info["themeType"] = $themeType;
        $info["is_default"] = $themeId == $default ? true : false;
                
        return $info;        
    }
   
    /**
     * Get all Themes
     * 
     * @param boolean $public Unused
     * @return array Theme List
     * @access public
     */
    
    function getAll($public = true) {
  
        $theme_list = array();
        $theme_base = $this->getThemeBase();
        $themeTypes = $this->_vfolder->folders($theme_base);
        if (VWP::isWarning($themeTypes)) {
            $themeTypes->ethrow();
            $themeTypes = array();
        }
    
        foreach($themeTypes as $type) {
            $tlist = $this->_vfolder->folders($theme_base.DS.$type);
            if (VWP::isWarning($tlist)) {
                $tlist->ethrow();
                $tlist = array();
            }
            foreach($tlist as $tid) {
                $info = $this->getThemeInfo($type,$tid);
                if (VWP::isWarning($info)) {
                    $info = array("themeId"=>$tid,"themeType"=>$type);
                }
                array_push($theme_list,$info);    
            }  
        }
  
        return $theme_list;    
    }

    /**  
     * Install theme from package
     * 
     * @param string $package_file Package filename
     */
 
    function doInstallPackage($package_file) {
       VWP::RequireLibrary('vwp.archive.archive');
       $tempFolder = $this->_vfolder->mktemp("install");
       if (VWP::isWarning($tempFolder)) {
           return $tempFolder;
       }
       $arch = VArchive::getInstance();
       $result = $arch->extract($package_file,$tempFolder);
       if (VWP::isWarning($result)) {
       	   $this->_vfolder->delete($tempFolder);
           return $result;
       }
       $result = $this->doInstall($tempFolder);
       $this->_vfolder->delete($tempFolder);
       return $result;    	        	
    }

    /**  
     * Install theme from folder
     * 
     * @param string $source_folder Folder path
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
        	$className = ucfirst(strtolower($manifest->_theme_type)) . '_'. ucfirst(strtolower($manifest->_theme_id)) . '_'. $verstr.'_Installer';        
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

        if (!$this->_installer->is_complete()) {
        	return VWP::raiseWarning('Modal installs are not supported!',get_class($this),null,false);        	
        }
        
        if (!$this->_installer->is_success()) {
        	return VWP::raiseWarning('Install failed!',get_class($this),null,false);        	
        }        
        
        return true;                  
    }

    function _doUninstall($themeType,$themeId) {
           
    	   $default = VThemeConfig::getDefaultTheme($themeType);
    	   if ($default == $themeId) {
    	       return VWP::raiseWarning('Uninstall failed! The requested theme is in use!');
    	   }
    	   
    	   
           $setup_folder = VWP::getVarPath('packages').DS.'themes'.DS.$themeType.DS.$themeId;
           $theme_folder = VPATH_BASE.DS.'themes'.DS.$themeType.DS.$themeId;
           
           // verify installer exists 
            
           $installerFilename = v()->filesystem()->path()->clean($setup_folder.DS.'setup'.DS.'installer.php');
           if (!$this->_vfile->exists($installerFilename)) {
               return VWP::raiseError("Installer $installerFilename not found!",get_class($this).":doUninstall",500,false);
           }
  
           // load manifest to get installer class name
           $manifestFilename = $theme_folder.DS.'manifest.xml';
           if (!$this->_vfile->exists($manifestFilename)) {
               return VWP::raiseError("Manifest not found!",get_class($this).":doUninstall",500,false);
           }

           $manifest = VManifest::getInstance();  
           $result = $manifest->load($manifestFilename);
           if (VWP::isWarning($result)) {
               return $result;
           }  
  
           $verstr = str_replace(".","_",$manifest->version);
           $className = ucfirst(strtolower($manifest->_theme_type)) . '_' . ucfirst(strtolower($manifest->_theme_id)) . '_'. $verstr.'_Installer';
           require_once($installerFilename);
           if (!class_exists($className)) {
               return VWP::raiseError("Installer $className not found!",get_class($this).":doUninstall",510,false);   
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
    
    function updateDefaults($defaults) {
    	$ret = true;
    	foreach($defaults as $themeType=>$themeId) {
    		$result = VThemeConfig::setDefaultTheme($themeType,$themeId);
    		if (VWP::isWarning($result)) {
    			$result->ethrow;
    			$ret = false;
    		}
    	}
    	
    	if (!$ret) {
    		return VWP::raiseWarning('Update failed!',get_class($this),null,false);
    	}
    	return $ret;
    }
    
    function uninstall($themes) {
 
        $ok = true;
        foreach($themes as $themeInfo) {  
            $result = $this->_doUninstall($themeInfo['themeType'],$themeInfo['themeId']);
            if (VWP::isWarning($result)) {
                $result->ethrow();
                $ok = VWP::raiseWarning('Some themes failed to uninstall',get_class($this),null,false);
            } else {
                if ($result === true) {
                    VWP::addNotice('Uninstalled: ' . $themeInfo['themeType'] .':'. $themeInfo['themeId']);
                } else {
                    VWP::raiseWarning('Uninstall pause not supported!',get_class($this));
                }                
            }
        }     
        return $ok;  
   }    

    /**    
     * Get Theme Parameters
     * 
     * @param string $themeType Theme Type
     * @param string $themeId Theme ID
     */
   
    function &_getThemeParams($themeType,$themeId) 
    {
   
        if (!isset($this->_params[$themeType])) {
            $this->_params[$themeType] = array();
        }
  
        if (!isset($this->_params[$themeType][$themeId])) {       
            $basePath = VPATH_BASE.DS.'themes';
   
            // Setup path
   
            $basePath .= DS.$themeType.DS.$themeId;
   
            if ($this->_vfile->exists($basePath.DS.'params.php')) {
                // Setup className
      
                $classPrefix = ucfirst($themeType);
                $classSuffix = ucfirst($themeId);
          
                $className = $classPrefix.'_ThemeParams_'.$classSuffix;

                require_once($basePath.DS.'params.php');
    
                if (class_exists($className)) {    
                    $this->_params[$themeType][$themeId] = new $className;                              
                    $this->_params[$themeType][$themeId]->loadData();                         
                } else {
                    $this->_params[$themeType][$themeId] = VWP::raiseError("Parameters object $className not found!",get_class($this),null,false);
                }
    
            } else {
                $this->_params[$themeType][$themeId] = VWP::raiseWarning('Parameters not found!',get_class($this),null,false);
            }
        }
        
        return $this->_params[$themeType][$themeId]; 
    }
   

   /**    
    * Get theme parameters
    * 
    * @param string $themeType
    * @param string $themeId
    */ 
    
   function getParams($themeType,$themeId) 
    {
 
        $ob =& $this->_getThemeParams($themeType,$themeId);
  
        if (VWP::isWarning($ob)) {
            return array();
        }
    
        $data = $ob->getDefinitions();
        $p = $ob->getProperties();
  
        foreach($data as $key=>$val) {
            if (isset($p[$key])) {
                $data[$key]["data"] = $p[$key];
            } else {
                $data[$key]["data"] = null;
            }
        }
  
        foreach($p as $key=>$val) {
            if (!isset($data[$key])) {
                $data[$key] = array("data"=>$val);
            }
        }
        return $data;
    }   

   /**    
    * Get theme parameters
    * 
    * @param string $themeType
    * @param string $themeId
    */ 
    
    function updateParams($themeType,$themeId,$params) 
    {
 
        $ob =& $this->_getThemeParams($themeType,$themeId);
  
        if (VWP::isWarning($ob)) {
            return array();
        }
    
        $data = $ob->getDefinitions();
          
        foreach($data as $key=>$val) {
            if (isset($params[$key])) {
                $ob->set($key,$params[$key]);
            }
        }
  
        return $ob->saveData();
    }    
    
    // End ThemeMgr_Model_Themes class
}
 
 