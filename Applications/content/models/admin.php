<?php

 
/**
 * Categories Model 
 *  
 * @package    VWP.Content
 * @subpackage Widgets
 * @author Ralph Ritoch <rritoch@gmail.com> 
 * @copyright (c) Ralph Ritoch - All rights reserved
 * @link http://www.vnetpublishing.com 
 */

VWP::RequireLibrary("vwp.model");
VWP::RequireLibrary('vwp.sys.registry');

/**
 * Categories Model
 *  
 * @package    VWP.Content
 * @subpackage Widgets
 * @author Ralph Ritoch <rritoch@gmail.com> 
 * @copyright (c) Ralph Ritoch - All rights reserved
 * @link http://www.vnetpublishing.com 
 */

class Content_Model_Admin extends VModel {

 function getEditors() {
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
 
 function getConfigKey() {
  return "SOFTWARE\\VNetPublishing\\Content\\Config";
 }
 
 function getRequiredSettings() {
  return array("table_prefix","default_editor","home_page_type","home_article","home_category","home_category_layout","home_title","edit_mode_theme_type");
 }
 /**
  * Get configuration settings
  * 
  * @return array|object Configuration settings on success, error or warning otherwise
  */       
 
 function getConfig() {
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
  */
     
 function checkConfig() {
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
  
  $table_name = $tbl_prefix . 'content_categories';
  $table = & $db->getTable($table_name);
  
  if (VWP::isError($table)) {
  
   $type = strtolower($db->getDBType());
   
   switch($type) {
   
    case "mysql":
    
     $query = 'CREATE TABLE IF NOT EXISTS '
              .$db->nameQuote($table_name)
              .' (
               `id` int(11) NOT NULL AUTO_INCREMENT,
                `name` text NOT NULL,
                `parent` int(11),
                `_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
               ) ENGINE=MyISAM DEFAULT CHARSET=latin1';
      $db->setQuery($query);
      $result = $db->query();
      $db->unloadTables();                  
     break;
    default:
     return VWP::raiseWarning("Unable to create tables due to unsupported database type ($type)!",get_class($this),null,false);   
   }
  } else {
   $result = true;
  }


  $table_name = $tbl_prefix . 'content_articles';
  $table = & $db->getTable($table_name);
  
  if (VWP::isError($table)) {
  
   $type = strtolower($db->getDBType());
   
   switch($type) {
   
    case "mysql":
    
     $query = 'CREATE TABLE IF NOT EXISTS '
              .$db->nameQuote($table_name)
              .' (
               `id` int(11) NOT NULL AUTO_INCREMENT,
                `title` text NOT NULL,
                `content` text NOT NULL,
                `description` text,
                `keywords` text,
                `category` int(11),
                `author_name` text,
                `author_username` text,
                `published` tinyint(1) NOT NULL DEFAULT 0,
                `order` int(11),
                `release_date` datetime,                
                `_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
               ) ENGINE=MyISAM DEFAULT CHARSET=latin1';
      $db->setQuery($query);
      $result = $db->query();
      $db->unloadTables();                  
     break;
    default:
     return VWP::raiseWarning("Unable to create tables due to unsupported database type ($type)!",get_class($this),null,false);   
   }
  } else {
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
  
 function saveConfig($settings) {
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
 
 
} // end class