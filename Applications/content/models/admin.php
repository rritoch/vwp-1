<?php
 
/**
 * Admin Model 
 *  
 * @package    VWP.Content
 * @subpackage Models
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch 2011 - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License 
 */

/**
 * Require Model Support
 */

VWP::RequireLibrary("vwp.model");

/**
 * Require Registry Support
 */

VWP::RequireLibrary('vwp.sys.registry');

/**
 * Admin Model
 *  
 * @package VWP.Content
 * @subpackage Models
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch 2011 - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License 
 */

class Content_Model_Admin extends VModel 
{

	/**
	 * Get Editors
	 * 
	 * @return array Editors
	 * @access public
	 */
	
    function getEditors() 
    {
        $key = "TOOLS\\Editors";
        $localMachine = & Registry::LocalMachine();
    
        $result = Registry::RegOpenKeyEx($localMachine,
                        $key,
                        0,
                        0, //samDesired
                        $registryKey);
                         
        if (VWP::isWarning($result)) {
            Registry::RegCloseKey($localMachine);
            return array(""=>"== No registered editors ==");
        }     

        $editors = array();
        $keys = array();
        $idx = 0;
        $keylen = 255;     
        $lpReserved = null;
        $lpClass = null;
        $lpcClass = 0;
        $lpftLastWriteTime = null;
  
        while (!VWP::isWarning($result = Registry::RegEnumKeyEx(
            $registryKey,
            $idx++,
            $keyName,
            $keylen,
            $lpReserved,
            $lpClass,
            $lpcClass,
            $lpftLastWriteTime))) {
            array_push($keys,$keyName);
            $keylen = 255;    
        } 
        Registry::RegCloseKey($registryKey);
  
        foreach($keys as $keyName) {
            $keyPath = "TOOLS\\Editors\\" . $keyName;
            $result = Registry::RegOpenKeyEx($localMachine,
                        $keyPath,
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
                if (isset($data["name"])) {   
                    $editors[$keyName] = $data["name"];
                }   
            }     
        }
        Registry::RegCloseKey($localMachine);
        return $editors; 
    }
 
    /**
     * Get Configuration Key
     * 
     * @return string Configuration key
     * @access public
     */
    
    function getConfigKey() 
    {
        return "SOFTWARE\\VNetPublishing\\Content\\Config";
    }
 
    /**
     * Get Required Settings
     *
     * @return array Required settings
     * @access public
     */
    
    function getRequiredSettings() 
    {
        return array("table_prefix",
                     "default_editor",
                     "home_page_type",
                     "home_article",
                     "home_category",
                     "home_category_layout",
                     "home_title",
                     "edit_mode_theme_type",
                     "images_path",
                     "images_url");
    }
    
    /**
     * Get configuration settings
     * 
     * @return array|object Configuration settings on success, error or warning otherwise
     * @access public
     */       
 
    function getConfig() 
    {
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

    /**
     * Check configuration settings
     * 
     * @return boolean|object True on success, error or warning otherwise
     * @access public
     */
     
    function checkConfig() 
    {
        $config = $this->getConfig();
        if (VWP::isWarning($config)) {
            return VWP::raiseWarning("Content Manager is not configured!",get_class($this),null,false);
        }

        if (!isset($config["table_prefix"])) {
            return VWP::raiseWarning("Content Manager Configuration is incomplete",get_class($this),null,false);  
        }
  
        // Check Database
  
        $db =& $this->_dbi->getDatabase();
        if (VWP::isWarning($db)) {
            return $db;
        }
  
        // Check Table
  
        $tbl_prefix = $config["table_prefix"];
  
        $table_id = 'content_categories';
        $table_name = $tbl_prefix . $table_id;
        $table = & $db->getTable($table_name);
  
        if (VWP::isError($table)) {
  
  	        $schemaSource = dirname(__FILE__).DS.'tables'.DS.$table_id.'.xsd';

  	        $schema = $this->_vfile->read($schemaSource);
  	        if (VWP::isWarning($schema)) {
  	  	        return $schema;
  	        } else {  	  
                $table =& $db->createTable($table_id,$table_name,$schema,$schemaSource);
                if (VWP::isWarning($table)) {
                    return $table;
                }
                $result = true;
  	        }
        } else {

            if (!$table->hasColumn('description')) {	
                $result = $table->insertColumn('description','character_varying',array('size'=>65536),'parent');
                if (VWP::isWarning($result)) {
          	        return $result;
                }
      
        	
                if (!$table->hasColumn('keywords')) {	
                    $result = $table->insertColumn('keywords','character_varying',array('size'=>1024),'description');
                    if (VWP::isWarning($result)) {
          	            return $result;
                    }
                }   	
  	
                if (!$table->hasColumn('filename')) {	
                    $result = $table->insertColumn('filename','character_varying',array('size'=>1024),'keywords');
                    if (VWP::isWarning($result)) {
          	            return $result;
                    }
                }
                
                if (!$table->hasColumn('author_name')) {	
                    $result = $table->insertColumn('author_name','character_varying',array('size'=>1024),'author_username');
                    if (VWP::isWarning($result)) {
          	            return $result;
                    }
                }                
            }   	
            $result = true;
        }

        $table_id = 'content_articles';
        $table_name = $tbl_prefix . $table_id;
        $table = & $db->getTable($table_name);
  
        if (VWP::isError($table)) {
  
  	        $schemaSource = dirname(__FILE__).DS.'tables'.DS.$table_id.'.xsd';

  	        $schema = $this->_vfile->read($schemaSource);
  	        if (VWP::isWarning($schema)) {
  	  	        return $schema;
  	        } else {  	  
                $table =& $db->createTable($table_id,$table_name,$schema,$schemaSource);
                if (VWP::isWarning($table)) {
                    return $table;
                }
                $result = true;          
  	       }
  	
        } else {
            if (!$table->hasColumn('filename')) {	
                $result = $table->insertColumn('filename','character_varying',array('size'=>1024),'description');
                if (VWP::isWarning($result)) {
          	        return $result;
                }
            }         
            $result = true;
        }
      
        return $result;
    }

    /**
     * Save configuration settings
     * 
     * @param array $settings Configuration settings
     * @return true|object True on success, error or warning otherwise
     * @access public  
     */    
  
    function saveConfig($settings) 
    {
        $localMachine = & Registry::LocalMachine();  
  
        $result = Registry::RegCreateKeyEx($localMachine,
                              self::getConfigKey(),
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
            if ($result === true) {
                $result = $this->checkConfig();
            }
            return $result;
        }
  
        Registry::RegCloseKey($localMachine);
        return $result; 
    }
 
    // end class Content_Model_Admin
}
 