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

class Content_Model_Categories extends VModel {
 
 function deleteCategories($categories) {
  $dbok = $this->checkConfig();
  if (VWP::isWarning($dbok)) {
   $dbok->ethrow();
   return $dbok;
  } 
 
  $config = $this->getConfig();
  
  $tbl_prefix = $config["table_prefix"];  
  $table_name = $tbl_prefix . 'content_categories';
  
  $db =& $this->_dbi->getDatabase();
  $table = & $db->getTable($table_name);

  foreach($categories as $catid) {
   $row =& $table->getRow($catid);
   if (!VWP::isWarning($row)) {
    $row->delete();
   }
  }
  return true;   
 }
  
 function getAll($public = true) {
  $dbok = $this->checkConfig();
  if (VWP::isWarning($dbok)) {
   $dbok->ethrow();
   return array();
  }

  $config = $this->getConfig();
  
  $tbl_prefix = $config["table_prefix"];  
  $table_name = $tbl_prefix . 'content_categories';
  
  $db =& $this->_dbi->getDatabase();
  $table = & $db->getTable($table_name);
  
  
  $table->loadData();
  $categories = array();
  $parents = array();
  foreach($table->rows as $row) {
   $u = $row->getProperties();
   $pid = $u["fields"]["id"]["value"];
   $pname = $u["fields"]["name"]["value"];   
   $parents[$pid] = $pname; 
  }    
  
  foreach($table->rows as $row) {
   $u = $row->getProperties();
   $cat = array();
   foreach($u["fields"] as $id=>$i) {
    $cat[$id] = $i["value"];
   }
   if (empty($cat["parent"])) {
    $cat["_parent_name"] = null;
   } else {
    $cat["_parent_name"] = $parents[$cat["parent"]];
   }   
   array_push($categories,$cat);
  }
    
  return $categories;     
 }

 function getConfigKey() {
  return "SOFTWARE\\VNetPublishing\\Content\\Config";
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
   return VWP::raiseWarning("Content categories table is missing!",get_class($this),null,false);
  }
      
  return true;
 }
 

 
 
} // end class