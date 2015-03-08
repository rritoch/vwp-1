<?php

class TinyMCE extends VObject {

 static $_driver = array("name"=>"tinymce_3_3_8","id"=>"tinymce_3_3_8");
    
 public static function &getInstance($driver = null) {
  static $drivers = array();
  
  if (!is_array($driver)) {
   $driver = self::$_driver;
  }  
  $className = self::$_driver["id"];  
  
  if (!class_exists($className)) {
   $vfile =& v()->filesystem()->file();
   $driverFile = dirname(__FILE__).DS.self::$_driver["name"].DS.'tinymce.php';
   if (!$vfile->exists($driverFile)) {
    $err = VWP::raiseError('TinyMCE Driver not found!','TinyMCE',null,false);
    return $err;
   }
   require_once($driverFile);
  }
  
  if (!class_exists($className)) {
   $err = VWP::raiseError('TinyMCE Driver Class not found!','TinyMCE',null,false);
   return $err;  
  }
  
  $idx = count($drivers);
  $drivers[] = new $className;  
  return $drivers[$idx];   
 }
 
 
} // end class