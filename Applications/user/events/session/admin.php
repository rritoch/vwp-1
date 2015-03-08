<?php

/**
 * Administrator session
 *  
 * This file provides administrator sessions         
 * 
 * @package VWP.User
 * @subpackage Events.Session  
 * @author Ralph Ritoch 
 * @copyright Ralph Ritoch <rritoch@gmail.com>
 * @link http://www.vnetpublishing.com    
 */

VWP::RequireLibrary('vwp.sys.events');



/**
 * Administrator authentication
 *  
 * This class provides administrator sessions         
 * 
 * @package VWP.User
 * @subpackage Events.Session  
 * @author Ralph Ritoch 
 * @copyright Ralph Ritoch <rritoch@gmail.com>
 * @link http://www.vnetpublishing.com    
 */

class AdminEventSession extends VEvent {
 
 var $sessions = array();
 var $stack = array();
 
 function onOpen($options,&$result) {
  $name = $options["session_name"];
  $path = $options["save_path"];
  array_push($this->stack,$name);
  $this->sessions[$name] = $path; 
  $result = true;
  return $result;   
 }
 
 function onClose($options,&$result) {
  if (count($this->stack)) {
   array_pop($this->stack);
  }
  return true;
 }
 
 function onRead($options,&$result) {
  $ptr = count($this->stack) - 1;
  $name = $this->stack[$ptr];
  $vfile =& v()->filesystem()->file();
  $id = $options["id"];
  $filename = $this->sessions[$name].DS."vwp_" . $id . ".sess";
    
  if ($vfile->exists($filename)) {
   $result = $vfile->read($filename);
  } else {
   $result = null;
  }    
  return $result;
 }
 
 function onWrite($options,&$result) {    
  $ptr = count($this->stack) - 1;
  $name = $this->stack[$ptr];
  $vfile =& v()->filesystem()->file();
  $id = $options["id"];
  $data = $options["data"];
  $filename = $this->sessions[$name].DS."vwp_" . $id . ".sess";
  $result = $vfile->write($filename,$data);  
  return $result;
 }
 
 function onDestroy($options,&$result) {
  $ptr = count($this->stack) - 1;
  $name = $this->stack[$ptr];
  $vfile =& v()->filesystem()->file();
  $id = $options["id"];  
  $filename = $this->sessions[$name].DS."vwp_" . $id . ".sess";
  $result = $vfile->delete($filename);
  return $result;  
 }
 
 function onGc($options,&$result) {
  $maxlifetime = $options["maxlifetime"];
  
  if ($maxlifetime) {
   $paths = array();
   if (count($this->stack)) {
    $ptr = count($this->stack) - 1;
    $name = $this->stack[$ptr];
    array_push($paths,$this->sessions[$name]);
   } else {
    foreach($this->sessions as $n=>$path) {
     array_push($paths,$path);
    }
   }   
   $timeout = time() - $maxlifetime;
   $vfolder =& v()->filesystem()->folder();
   $vfile =& v()->filesystem()->file();
   $prefix = "vwp_";
   $suffix = ".sess";
   
   foreach($paths as $path) {
    $files = $vfolder->files($path);
    foreach($files as $bname) {
     if (
         (substr($bname,0,strlen($prefix)) == $prefix) &&
         (substr($bname,strlen($bname) - strlen($suffix)) == $suffix)
        ) {
      $mtime = $vfile->getMTime($path.DS.$bname);
      if (!VWP::isWarning($mtime)) {
       if ($mtime < $timeout) {
        $vfile->delete($path.DS.$bname);
       }
      }   
     } 
    } 
   }
  }
  $result = true;
  return $result;
 }

} // end class