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

class Content_Model_Category extends VModel {
 
 var $id = null;
 var $name = null;
 var $parent = null;
 
 function load($catid = null) {
  $config = $this->getConfig();  
  $tbl_prefix = $config["table_prefix"];  
  $table_name = $tbl_prefix . 'content_categories'; 
  $table = & $this->_dbi->getTable($table_name);
  
  if (VWP::isWarning($table)) {
   return $table;
  }
  
  $row = $table->getRow($catid);

  if (VWP::isWarning($row)) {
   return $row;
  }
    
  $data = $row->getProperties();
  $fields = array();
  foreach($data["fields"] as $key=>$val) {
   $fields[$key] = $val["value"];
  }
  $this->setProperties($fields);
  
  return true;
 }
 
 function save() {
  $config = $this->getConfig(); 
  
  if (VWP::isWarning($config)) {
   return $config;
  }
   
  $tbl_prefix = $config["table_prefix"];  
  $table_name = $tbl_prefix . 'content_categories'; 
  $table = & $this->_dbi->getTable($table_name);
  
  if (VWP::isWarning($table)) {
   return $table;
  }
  
  $row = $table->getRow($this->id);

  if (VWP::isWarning($row)) {
   return $row;
  }
  
   
  $data = $this->getProperties();  
  
  if (empty($data["parent"])) {
   $data["parent"] = null;
  }
  
  foreach($data as $key=>$val) {
   $row->set($key,$val);
  }
    
  $result = $row->save();
  if (VWP::isWarning($result)) {
   return $result;
  }
  
  $data = $row->getProperties();
  $fields = array();
  foreach($data["fields"] as $key=>$val) {
   $fields[$key] = $val["value"];
  }
  $this->setProperties($fields);  
  
  return true; 
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

 function getArticles($catid = null) {
  $config = $this->getConfig();
  
  $tbl_prefix = $config["table_prefix"];  
  $table_name = $tbl_prefix . 'content_articles';
  
  $db =& $this->_dbi->getDatabase();
  $table = & $db->getTable($table_name);
  if (VWP::isWarning($table)) {
   return array();
  }
    
  if (empty($catid)) {
   $catid = $this->id;
  }
  
  $table->loadData();
  $articles = array();

  
  foreach($table->rows as $row) {
   $u = $row->getProperties();
   $cat = array();
   foreach($u["fields"] as $id=>$i) {
    $cat[$id] = $i["value"];
   }
   if ($cat["category"] == $catid) {   
    array_push($articles,$cat);
   }
  }
    
  return $articles;  
 }
 
 function getSubCategories($catid = null) {
  static $categories;
  
  
  if (!isset($categories)) {
   $categories = array();
   
   $config = $this->getConfig();
  
   $tbl_prefix = $config["table_prefix"];  
   $table_name = $tbl_prefix . 'content_categories';
  
   $db =& $this->_dbi->getDatabase();
   $table = & $db->getTable($table_name);
  
   if (!VWP::isWarning($table)) {
    $table->loadData();
   
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
   }
  }
  
  $subs = array();
  
  if (empty($catid)) {
   $catid = $this->id;
  }
  
  foreach($categories as $cat) {
   if ($cat["parent"] == $catid) {
    array_push($subs,$cat);
   }
  }   
       
  return $subs;
 }
 
 function getChildArticles($catid = null) {
 
  static $articles;
  
  if (!isset($articles)) {
   $config = $this->getConfig();
  
   $tbl_prefix = $config["table_prefix"];  
   $table_name = $tbl_prefix . 'content_articles';
  
   $db =& $this->_dbi->getDatabase();
   $articles = array();
   $table = & $db->getTable($table_name);
   if (!VWP::isWarning($table)) {         
    $table->loadData();  
    foreach($table->rows as $row) {
     $u = $row->getProperties();
     $cat = array();
     foreach($u["fields"] as $id=>$i) {
      $cat[$id] = $i["value"];
     }      
     array_push($articles,$cat);   
    }
   }
  }
  
  $children = array();
  
  $subs = $this->getSubCategories($catid);
  foreach($subs as $cat) {
   $a = $this->getArticles($cat["id"]);
   $b = $this->getChildArticles($cat["id"]);
   $children = array_merge($children,$a,$b);
  }
  
  return $children;
 }
 
} // end class