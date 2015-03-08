<?php

VWP::RequireLibrary('vwp.ui.widget.params');

class Menu_WidgetParams_Vmenu extends VWidgetParams {
 
 static $_menus;
 
 var $title = "Vertical Menu";
 
 var $name = null;
 
 
 function getMenus() {
  if (!isset(self::$_menus)) {
   self::$_menus = array();
   $menu =& $this->getModel('menu');
   
   if (VWP::isWarning($menu)) {
    $menu->ethrow();
    return self::$_menus;
   }
   
   $alist = $menu->getAll();
   
   
   
   foreach($alist as $m) {
    self::$_menus[$m["id"]] = $m["title"] . ' [' . $m["id"] . ']';     
   }
  }
  return self::$_menus;
 }
 
 function getDefinitions() {
  $alist = $this->getMenus();
  
  $def = array();
  $def["menu"] = array(
   "label"=>"Menu",
   "type"=>"select",
   "values"=>$alist
  );

  return $def;
 }
 
 function __construct() {
  parent::__construct();
  $this->addPath('models',dirname(dirname(dirname(__FILE__))).DS.'models');
 }
} // end class