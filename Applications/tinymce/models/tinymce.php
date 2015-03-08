<?php

VWP::RequireLibrary('vwp.model');
VWP::RequireLibrary('tinymce.tinymce');

class Tinymce_Model_Tinymce extends VModel {

 function &open($driver = null) {
  static $drivers = array();
  $idx = count($drivers);
  $drivers[] = Tinymce::getInstance($driver);
  return $drivers[$idx];
 }

 function getConfig(&$tinymce) {
  return $tinymce->getProperties();
 }
 

 
}