<?php

VWP::RequireLibrary('vwp.uri');

class Tinymce_3_3_8 extends TinyMCE {
 var $name = null;
 
 var $theme_advanced_buttons = array(
   array( /*"save",*/ "newdocument","|","bold","italic","underline","strikethrough","|","justifyleft","justifycenter","justifyright","justifyfull","styleselect","formatselect","fontselect","fontsizeselect"),
   array("cut","copy","paste","pastetext","pasteword","|","search","replace","|","bullist","numlist","|","outdent","indent","blockquote","|","undo","redo","|","link","unlink","anchor","image","cleanup","help","code","|","insertdate","inserttime","preview","|","forecolor","backcolor"),
   array("tablecontrols","|","hr","removeformat","visualaid","|","sub","sup","|","charmap","emotions","iespell","media","advhr","|","print","|","ltr","rtl","|","fullscreen"),
   array("insertlayer","moveforward","movebackward","absolute","|","styleprops","|","cite","abbr","acronym","del","ins","attribs","|","visualchars","nonbreaking","template","pagebreak","restoredraft"),
 );   
 var $theme_advanced_toolbar_location = "top";
 var $theme_advanced_toolbar_align = "left";
 var $theme_advanced_statusbar_location = "bottom"; 
 var $template_external_list_url = '';
 var $texternal_link_list_url = '';
 var $texternal_image_list_url = '';
 var $media_external_list_url = '';
 var $theme_advanced_resizing = true;
 
 function getBaseURL() {
  static $base;
  if (!isset($base)) {
   $base = VURI::base();
   $offset = substr(dirname(__FILE__),strlen(VPATH_BASE));  
   $base = $base . v()->filesystem()->path()->clean($offset,'/');
  }
  return $base; 
 }
 
 function getExampleBaseURL() {
  return $this->getBaseURL() . '/tinymce/examples/';
 }
 
 function getScriptURL() {
  $base = $this->getBaseURL();
  $url = $base .'/tinymce/jscripts/tiny_mce/tiny_mce.js';
  return $url;
 }
 
 function setName($name) {
  $this->name = $name;
 }
 
 function __construct() {
  parent::__construct();
  
  $lists = array(
   "template_external_list_url"=>"lists/template_list.js",
   "external_link_list_url"=>"lists/link_list.js",
   "external_image_list_url"=>"lists/image_list.js",
   "media_external_list_url"=>"lists/media_list.js"
  );		
  $listbase = $this->getBaseURL() . '/tinymce/examples/';
  foreach($lists as $key=>$offset) {
   $this->$key = $listbase.$offset;
  }
 }

}