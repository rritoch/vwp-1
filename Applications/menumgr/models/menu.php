<?php

/**
 * Menu Model
 *  
 * This is the model to access and modify menus  
 *  
 * @package    VWP.MenuMgr
 * @subpackage Models
 * @author Ralph Ritoch <rritoch@gmail.com> 
 * @copyright (c) Ralph Ritoch - All rights reserved
 * @link http://www.vnetpublishing.com 
 */
 

VWP::RequireLibrary('vwp.model');
VWP::RequireLibrary('vwp.ui.menu');


/**
 * Menu Model
 *  
 * This is the model to access and modify menus  
 *  
 * @package    VWP.MenuMgr
 * @subpackage Models
 * @author Ralph Ritoch <rritoch@gmail.com> 
 * @copyright (c) Ralph Ritoch - All rights reserved
 * @link http://www.vnetpublishing.com 
 */ 

class Menumgr_Model_Menu extends VModel {
 var $id = null;
 var $_menu = null;
 var $_params = array();
 
 function getTypes() {
  return array(
   "link"=>"Link",
   "spacer"=>"Spacer",
   "category"=>"Category",
   "applink"=>"Application"
  );
 }
 
 function getParams($widget,$ref = null) {
 
  $ob =& $this->_getWidgetParams($widget,$ref);
  if (VWP::isWarning($ob)) {
  	$ob->ethrow();
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
 
 function getApplications() {
  $basePath = VPATH_BASE.DS.'Applications';
  $apps = $this->_findApps($basePath);
  foreach($apps as $key=>$val) {
   $params =& $this->_getWidgetParams($key);
   if (!VWP::isWarning($params)) {
    if (!empty($params->title)) {
     $apps[$key] = $params->title;
    }     
   }
  }
  return $apps;
 }
 
 
 function _findApps($path) {
  $app_list = array();
  if (!$this->_vfolder->exists($path)) {
   return $app_list;
  }
  
  $nlist = $this->_vfolder->folders($path);
  
  foreach($nlist as $app_id) {
   // Define App
   $app_list[$app_id] = $app_id;
   
   // Get sub apps
   $slist = $this->_findApps($path.DS.$app_id.DS.'widgets');
   foreach($slist as $key=>$val) {
    $app_list[$app_id . "." . $key] = $val;
   }
  }
  ksort($app_list);
  return $app_list;
 }
 
 function bind($data) {
  return $this->_menu->bind($data);
 }
 
 function save() {
  $menupath = VWP::getVarPath('vwp').DS.'menus';
  $menuID = $this->id; 
  $this->_menu->_id = $menuID;   
  $this->_menu->save(); 
 }
 
 function updateItem($itemID,$settings,$params = null,$parent = null) {
  if (empty($parent)) {
   $parent =& $this->_menu;
  } 
 
  $parts = explode(":",$itemID);
  if (count($parts) > 1) {
   $id = array_shift($parts);
   $child = implode(":",$parts);
   $item =& $parent->getItem($id - 1);
   if (VWP::isWarning($item)) {
    return $item;
   }
   $ret = $this->updateItem($child,$settings,$params,$item);
   return $ret;
  }  
 
  $item =& $parent->getItem($itemID - 1);
  if (VWP::isWarning($item)) {
   return $item;    
  }
  foreach($settings as $key=>$val) {
   $item->set($key,$val);
  }
  $menupath = VWP::getVarPath('vwp').DS.'menus';
  $menuID = $this->id; 
  $filename = $menupath.DS.$menuID.'.xml';
  $ret = $this->_menu->save($filename);
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
 
 function addItem($item) {  
  $id = $this->_menu->insertItem($item);
  if (VWP::isWarning($id)) {
   return $id;
  }
  
  $menupath = VWP::getVarPath('vwp').DS.'menus';
  $menuID = $this->id; 
  $filename = $menupath.DS.$menuID.'.xml';  
  $result = $this->_menu->save($filename);
  if (VWP::isWarning($result)) {
   return $result;
  }
  return $this->getItem($id + 1);  
 }
 
 function deleteItems($items) {  
  arsort($items);
  foreach($items as $id) {
   $this->_menu->deleteItem($id - 1);   
  }
  $menupath = VWP::getVarPath('vwp').DS.'menus';
  $menuID = $this->id; 
  $filename = $menupath.DS.$menuID.'.xml';   
  return $this->_menu->save($filename);
 }
 
 function getItem($itemID,$parent = null) {
  if (empty($parent)) {
   $parent =& $this->_menu;
  } 
 
  $parts = explode(":",$itemID);
  if (count($parts) > 1) {
   $id = array_shift($parts);
   $child = implode(":",$parts);
   $item =& $parent->getItem($id - 1);
   if (VWP::isWarning($item)) {
    return $item;
   }
   $ret = $this->getItem($child,$item);
   if (!VWP::isWarning($ret)) {
    $ret["id"] = $itemID;
   }
   return $ret;
  }
  
  $item =& $parent->getItem($itemID - 1);
  if (VWP::isWarning($item)) {
   return $item;    
  }
  $p = $item->getProperties();  
  $p["id"] = $itemID;
  return $p;
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
 
 function _renumberOrder($item_list) {
  $renumbered_item_list = $item_list;
 
  $sz = count($renumbered_item_list);
  for($idx = 0; $idx < $sz; $idx++) {
   $renumbered_item_list[$idx]["_order"] = 2 * $idx;
  }  
  return $renumbered_item_list;
 }
 
 function reorderItems($order) {
  $item_list = array();
 
  $info = array();
  $idx = 0;
  
  $info["item"] =& $this->_menu->getItem($idx++);
  
  while(!VWP::isWarning($info["item"])) {
   $info["_order"] = $order[$idx];   
   array_push($item_list,$info);
   $info = array();
   $info["item"] =& $this->_menu->getItem($idx++);
  }
  
  $item_list = $this->_applyOrder($item_list);
 
  // Delete All Items
  
  $dr = $this->_menu->deleteItem(0);  
  while(!VWP::isWarning($dr)) {
   $dr = $this->_menu->deleteItem(0);
  }
  
  // ReInsert All Items
  foreach($item_list as $info) {
   $this->_menu->insertItem($info["item"]);
  }
    
  $this->save();
  return true;   
 }
 
 function moveItem($itemId,$dir) {
  if (($dir < 0) || ($dir > 0)) {
   if ($dir < 0) {
    // Move Up   
    if ($itemId < 2) {
     return true;
    }
    
    // get me
    $item = $this->_menu->getItem($itemId - 1);
    
    if (VWP::isWarning($item)) {
     return $item;
    }
    
    // delete me
    $this->_menu->deleteItem($itemId - 1);
    
    // Re-Insert Me
    $this->_menu->insertItem($item,$itemId - 2);
                
   } else {
    // get me
    $item = $this->_menu->getItem($itemId - 1);
    
    if (VWP::isWarning($item)) {
     return $item;
    }
    
    // delete me
    $this->_menu->deleteItem($itemId - 1);
    
    // Re-Insert Me
    $this->_menu->insertItem($item,$itemId);
    
   }
  }
  $this->save();
  return true;  
 }
  
 function getAllItems($parent = null) {
  if (empty($parent)) {
   $parent =& $this->_menu;
  }
  
  $idx = 0;  
  $item_list = array();
  $item =& $parent->getItem($idx++);
  
  while(!VWP::isWarning($item)) {   
   $data = $item->getProperties();
   $data["type"] = $item->_type;
   $data["id"] = $idx;
   $data["parent_name"] = $parent->get('title');
   array_push($item_list,$data);
   $item =& $parent->getItem($idx++);
  }
    
  $item_list = $this->_renumberOrder($item_list);
  $item_list = $this->_applyOrder($item_list);
  
  return $item_list;
 }
 
 function load($menuID) {  
  //$menupath = VWP::getVarPath('vwp').DS.'menus';
  $this->id = $menuID;   
  return $this->_menu->load($menuID);
 }

 function getProperties($public = true) {
  $p = $this->_menu->getProperties($public);
  $p["id"] = $this->id;
  return $p;
 }
 
 function __construct() {
  parent::__construct();
  $this->_menu = new VMenu;
 }
}