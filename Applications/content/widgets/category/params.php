<?php

VWP::RequireLibrary('vwp.ui.widget.params');

class Content_WidgetParams_Category extends VWidgetParams {
 
 static $_categories;
 
 var $title = "Content Category";
 
 var $category = null;
 var $fmt = null;
 
 function getCategories() {
  if (!isset(self::$_categories)) {
   $categories =& $this->getModel('categories');
   self::$_categories = array();
   if (VWP::isWarning($categories)) {
    $categories->ethrow();
    return self::$_categories;
   }
   $catlist = $categories->getAll();
   
   
   foreach($catlist as $cat) {
    self::$_categories[$cat["id"]] = $cat["name"];
   }
  }
  return self::$_categories;
 }
 
 function getDefinitions() {
  $cats = $this->getCategories();
  
  $def = array();
  $def["category"] = array(
   "label"=>"Category",
   "type"=>"select",
   "values"=>$cats
  );
  $def["fmt"] = array(
   "label"=>"Format",
   "type"=>"select",
   "values"=>array("default"=>"Default","blog"=>"Blog")  
  );
  return $def;
 }
 
 function __construct() {
  parent::__construct();
  $this->addPath('models',dirname(dirname(dirname(__FILE__))).DS.'models');
 }

} // end class