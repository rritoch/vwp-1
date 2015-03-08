<?php

VWP::RequireLibrary("vwp.model");
VWP::RequireLibrary('vwp.ui.route');

class Menu_Model_Menu extends VModel {

 function getMenuInfo($menuID) {
 
  $menuInfo = array("id"=>$menuID,
                    "title"=>"Error",
                    "disabled"=>1,
                    "visible"=>0);
  
  $menupath = VWP::getVarPath('vwp').DS.'menus';
  $doc = new DomDocument;
  $data = $this->_vfile->read($menupath.DS.$menuID.'.xml');
  if ($doc->loadXML($data)) {
   $root = $doc->documentElement;
   
   for($idx = 0; $idx < $root->childNodes->length; $idx++) {
    $key = $root->childNodes->item($idx)->nodeName;
    if (isset($menuInfo[$key])) {
     $menuInfo[$key] = $root->childNodes->item($idx)->nodeValue;     
    }   
   }  
  }
  return $menuInfo;
 }

 function getAll($public = true) {
  $menupath = VWP::getVarPath('vwp').DS.'menus';
  $menu_list = array();  
  $mlist = $this->_vfolder->files($menupath);  
  if (VWP::isWarning($mlist)) {
   return $menu_list;
  }
  
  foreach($mlist as $fname) {
   $ext = $this->_vfile->getExt($fname);
   if ($ext == 'xml') {
    $menuID = $this->_vfile->stripExt($fname);
    $menu = $this->getMenuInfo($menuID);
    array_push($menu_list,$menu);
   }
  }
  return $menu_list;
 }

 function getMenu($menuID) { 
  $menu = VMenu::getInstance($menuID);
  if (VWP::isWarning($menu)) {
   return $menu;
  }
  $route = VRoute::getInstance();
  
  $data = $menu->getProperties();
  $len = count($data["_items"]);
  for($ptr = 0; $ptr < $len; $ptr++) {
   if (isset($data["_items"][$ptr]["url"])) {
    $data["_items"][$ptr]["url"] = $route->encode($data["_items"][$ptr]["url"]);
   }
  }
  $data["id"] = $menuID;
  return $data;
 }

} // end class