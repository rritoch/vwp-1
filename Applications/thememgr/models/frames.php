<?php

/**
 * Frames model
 * 
 * @package    VWP.ThemeMgr
 * @subpackage Models
 * @author Ralph Ritoch <rritoch@gmail.com> 
 * @copyright Ralph Ritoch - ALL RIGHTS RESERVED
 * @link http://www.vnetpublishing.com
 */

VWP::RequireLibrary('vwp.model');
VWP::RequireLibrary('vwp.ui.frame');

/**
 * Frames model
 * 
 * @package    VWP.ThemeMgr
 * @subpackage Models
 * @author Ralph Ritoch <rritoch@gmail.com> 
 * @copyright Ralph Ritoch - ALL RIGHTS RESERVED
 * @link http://www.vnetpublishing.com
 */

class Thememgr_Model_Frames extends VModel {
 
 var $_params = array();

 function getBasePath() {
  $filename = VWP::getVarPath('vwp').DS.'frames';
  return $filename;
 }

 function createFrame($frameId) {
  $old = VUIFrame::getInstance($frameId);
  if (!VWP::isWarning($old)) {
   return VWP::raiseWarning('Frame id in use!',get_class($this),null,false);
  }
  $frameFilename = $this->getBasePath() .DS.$frameId.'.xml';
  $newframe = VUIFrame::getInstance();
  $newframe->id = $frameId;
  return $newframe->save($frameFilename); 
 }

 function getAll($public = true) {
  $frames = array();

  $frame_list = VUIFrame::getFrameList();

  foreach($frame_list as $frameId) {
    $f =& VUIFrame::getInstance($frameId);
    $info = $f->getProperties();
    $info["id"] = $frameId;
    array_push($frames,$info);
  }
  return $frames;
 }

 function reorderItems($frameId,$order) {
  
  if (VWP::isWarning($f =& VUIFrame::getInstance($frameId))) return $f;
  
  $item_list = array();
 
  $info = array();
  $idx = 0;
  
  $info["item"] =& $f->getItem($idx++);
  
  while(!VWP::isWarning($info["item"])) {
   $info["_order"] = $order[$idx];   
   array_push($item_list,$info);
   $info = array();
   $info["item"] =& $f->getItem($idx++);
  }
  
  $item_list = $this->_applyOrder($item_list);
 
  // Delete All Items
  
  $dr = $f->deleteItem(0);  
  while(!VWP::isWarning($dr)) {
   $dr = $f->deleteItem(0);
  }
  
  // ReInsert All Items
  foreach($item_list as $info) {
   $f->insertItem($info["item"]);
  }
    
  $f->save();
  
  return true;    
 }

 function _compareOrder($a,$b) {
  if ($a["_order"] < $b["_order"]) {
   return -1;
  }
  if ($a["_order"] > $b["_order"]) {
   return 1;
  }  
  return 0;
 }
 

 function _applyOrder($item_list) {  
  $ordered_item_list = $item_list; 
  usort($ordered_item_list, array($this,'_compareOrder')); 
  return $ordered_item_list;
 }
 
 function moveItem($frameId,$itemId,$dir) {
  
  if (VWP::isWarning($f =& VUIFrame::getInstance($frameId))) return $f;
  
  if (($dir < 0) || ($dir > 0)) {
   if ($dir < 0) {
    // Move Up   
    if ($itemId < 2) {
     return true;
    }
    
    // get me
    $item = $f->getItem($itemId - 1);
    
    if (VWP::isWarning($item)) {
     return $item;
    }
    
    // delete me
    $f->deleteItem($itemId - 1);
    
    // Re-Insert Me
    $f->insertItem($item,$itemId - 2);
                
   } else {
    // get me
    
    $item = $f->getItem($itemId - 1);
    
    if (VWP::isWarning($item)) {
     return $item;
    }
    
    // delete me
    $f->deleteItem($itemId - 1);
    
    // Re-Insert Me
    $f->insertItem($item,$itemId);
    
   }
  }
  
  $f->save();
  return true;    
 }
 
 function deleteFrames($frame_list) {
    
  foreach($frame_list as $frameID) {

   $f =& VUIFrame::getInstance($frameID);
         
   $f->delete();
  }
  return true; 
 }

 function saveFrame($frameId,$frameInfo) {

  if (!is_array($frameInfo)) return VWP::raiseWarning('Save failed due to invalid data format',get_class($this),null,false);  

  if (VWP::isWarning($f =& VUIFrame::getInstance($frameId))) return $f;  

  foreach($frameInfo as $k=>$v) {
   $f->set($k,$v);
  }

  return $f->save();
 }

 function addItem($frameId,$item) {
  if (VWP::isWarning($f =& VUIFrame::getInstance($frameId))) return $f;
  $result = $f->insertItem($item);
  
  if (VWP::isWarning($result)) {
   return $result;
  }
  
  $s = $f->save();
  if (VWP::isWarning($s)) {
   return $s;
  }
  return $result;    
 }
 
 function deleteItems($frameId,$item_list) {
  if (VWP::isWarning($f =& VUIFrame::getInstance($frameId))) return $f;

  arsort($item_list);
  foreach($item_list as $id) {
   $f->deleteItem($id - 1);   
  }   
  return $f->save(); 
 }
 
 function updateItem($frameId,$itemId,$settings,$params) {
  if (VWP::isWarning($f =& VUIFrame::getInstance($frameId))) return $f;
      
  $item = $f->getItem($itemId - 1);
    
  if (VWP::isWarning($item)) {
   return $item;    
  }
  foreach($settings as $key=>$val) {
   $item->set($key,$val);
  }

  $ret = $f->save();
  
  if (VWP::isWarning($ret)) {
   return $ret;
  }
  
  if (!isset($settings["widget"])) {
   return $ret;
  }
  
  if (empty($params)) {
   return $ret;
  }
  
  if (!isset($settings["ref"])) {
   return $ret;
  }
  
  $ref = $settings["ref"];
  
  if (empty($ref)) {
   return VWP::raiseWarning("Alias refrence is missing! Unable to save parameters!",get_class($this),null,false);
  }
  
  $widget = $settings["widget"];
  $ob =& $this->_getWidgetParams($widget,$ref);
  if (VWP::isWarning($ob)) {
   return $ret;
  }
  
  $ob->bind($params);  
  return $ob->saveRef($ref);     
  
 }
 
 function getItem($frameId,$itemId) {
  if (VWP::isWarning($f =& VUIFrame::getInstance($frameId))) return $f;
  
  $item = $f->getItem($itemId - 1);
  return $item;
 }


 function getParams($widgetId,$ref = null) {
  $ob =& $this->_getWidgetParams($widgetId,$ref);
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
 
 function &_getWidgetParams($widget,$ref = null) {
 
  if (empty($ref)) {
   $id = "_";
  } else {
   $id = $ref;
  }
  
  if (!isset($this->_params[$id])) {
   $this->_params[$id] = array();
  }
  
  if (!isset($this->_params[$id][$widget])) {       
   $basePath = VPATH_BASE.DS.'Applications';
   $parts = explode(".",$widget);
   
   // Setup path
   
   $appName = array_shift($parts);   
   $basePath .= DS.$appName;
   
   foreach($parts as $w) {
    $basePath .= DS.'widgets'.DS.$w;
   }
   
   if ($this->_vfile->exists($basePath.DS.'params.php')) {
    // Setup className
   
    $classPrefix = $appName;
    $classSuffix = '';
      
    if (count($parts) > 0) {
     $classSuffix = array_pop($parts);
    }
   
    while(count($parts) > 0) {
     $classPrefix .= ucfirst(array_shift($parts));
    }
    
    $className = $classPrefix.'_WidgetParams_'.$classSuffix;
    require_once($basePath.DS.'params.php');
    
    if (class_exists($className)) {    
     $this->_params[$id][$widget] = new $className;
          
     if (!empty($ref)) {
      $this->_params[$id][$widget]->loadRef($ref);
     }     
    } else {
     $this->_params[$id][$widget] = VWP::raiseError("Parameters object $className not found!",get_class($this),null,false);
    }
    
   } else {
    $this->_params[$id][$widget] = VWP::raiseWarning('Parameters not found!',get_class($this),null,false);
   }
  }
  return $this->_params[$id][$widget]; 
 }


 function _findWidgets($path, $skipApps = true) {
  $app_list = array();
  if (!$this->_vfolder->exists($path)) {
   return $app_list;
  }
  
  $nlist = $this->_vfolder->folders($path);
  
  foreach($nlist as $app_id) {
   // Define App
   if (!$skipApps) {
    $app_list[$app_id] = $app_id;
   }
   // Get sub apps
   $slist = $this->_findWidgets($path.DS.$app_id.DS.'widgets',false);
   foreach($slist as $key=>$val) {
    $app_list[$app_id . "." . $key] = $val;
   }
  }
  ksort($app_list);
  return $app_list;
 } 


 function getWidgets() {
  $basePath = VPATH_BASE.DS.'Applications';
  $apps = $this->_findWidgets($basePath);
  foreach($apps as $key=>$val) {
   $params =& $this->_getWidgetParams($key);
   if (!VWP::isWarning($params)) {
    if (!empty($params->title)) {
     $apps[$key] = $params->title;
    }     
   }
  }

  foreach($apps as $key=>$val) {
   $parts = explode(".",$key);
   if (count($parts) > 1) {
    array_pop($parts);
    $parent = implode(".",$parts);
    if (count($parts < 2)) {
     $apps[$key] =  $parent . " > " . $val;
    } elseif (isset($apps[$parent])) {
     $parent = $apps[$parent];
     $apps[$key] =  $parent . " > " . $val;
    }
   }
  }  
  
  return $apps;
 }
 
 function getAllItems($frameId) {
  if (VWP::isWarning($f =& VUIFrame::getInstance($frameId))) return $f; 
  
  $item_list = array();

  $idx = 0;
  
  $item_list[$idx] =& $f->getItem($idx);  
  while (!VWP::isWarning($item_list[$idx++])) {
   $item_list[$idx] =& $f->getItem($idx);
  }  
  $result = array_pop($item_list);
  
  if ($result->errno != ERROR_NO_MORE_ITEMS) {
   return $result;
  } 
  return $item_list;
 }
 
 function getFrameProperties($frameId) {
  if (VWP::isWarning($f =& VUIFrame::getInstance($frameId))) return $f;
  return $f->getProperties();
 }
 
 // end class
}
