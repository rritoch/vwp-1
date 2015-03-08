<?php

 
/**
 * Article Model 
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
 * Article Model
 *  
 * @package    VWP.Content
 * @subpackage Widgets
 * @author Ralph Ritoch <rritoch@gmail.com> 
 * @copyright (c) Ralph Ritoch - All rights reserved
 * @link http://www.vnetpublishing.com 
 */

class Content_Model_Article extends VModel {
 
 var $id = null;
 var $title = null;
 var $content = null;
 var $description = null;
 var $keywords = null;
 var $category = null;
 var $author_name = null;
 var $author_username = null;
 var $published = null;
 var $order = null;
 var $release_date = null;
 
 function getEditor($config = array()) {
  $cfg = $this->getConfig();
  if (VWP::isWarning($cfg)) {
   return '<p>Content manager not configured</p>';
  }
  
  $default_editor = $cfg['default_editor'];

  $key = "TOOLS\\Editors\\" . $default_editor;
  
  $localMachine = & Registry::LocalMachine();
  
  $result = Registry::RegOpenKeyEx($localMachine,
                        $key,
                        0,
                        0, //samDesired
                        $registryKey);
                         
  if (VWP::isWarning($result)) {
   Registry::RegCloseKey($localMachine);
   return '<p>Default editor not registered!</p>';
  }
  
       
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
    $editor[$key] = $val;
    $keylen = 255;
    $vallen = 255;  
   }
  }  
  Registry::RegCloseKey($registryKey);
  Registry::RegCloseKey($localMachine);
  
  $app = $editor['app'];
  $widget = $editor['widget'];
  $config["widget"] = $widget;
  $config["name"] = "article[content]";
  $config["value"] = $this->content;
  
  $appId = $app;
  
  if (!empty($widget)) {
   $appId .= '.' . $widget;
  }
  $args = $appId;
  $stdout = new VStdio;
  
  $shellob =& v()->shell();
  
  $env = array('any'=>$config);
  
  $result = $shellob->execute($args,$env,$stdout);
      
  return $stdout->getOutBuffer();
 }
 
 function load($catid = null) {
  $config = $this->getConfig();  
  $tbl_prefix = $config["table_prefix"];  
  $table_name = $tbl_prefix . 'content_articles'; 
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
  $table_name = $tbl_prefix . 'content_articles'; 
  $table = & $this->_dbi->getTable($table_name);
  
  if (VWP::isWarning($table)) {
   return $table;
  }
  
  $row = $table->getRow($this->id);

  if (VWP::isWarning($row)) {
   return $row;
  }
  
   
  $data = $this->getProperties();    
  
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
 
} // end class