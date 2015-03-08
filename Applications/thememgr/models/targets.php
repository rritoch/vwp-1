<?php

/**
 * Targets Model
 * 
 * @package    VWP.ThemeMgr
 * @subpackage Models
 * @author Ralph Ritoch <rritoch@gmail.com> 
 * @copyright CopyrightNotice
 * @link Url   
 */

VWP::RequireLibrary('vwp.model');
VWP::RequireLibrary('vwp.themes.targets');

/**
 * New Model
 * 
 * @package    SomePackage
 * @subpackage Models
 * @author AuthorName <authorEmail> 
 * @copyright CopyrightNotice
 * @link Url   
 */ 

class Thememgr_Model_Targets extends VModel {

 /**
  * Get All Targets
  */

 function getAllTargets($themeType,$themeId) {
  $result = VThemeTarget::_listTargets($themeType,$themeId);
  if (VWP::isWarning($result)) {
   return $result;
  }
  
  $settings = VThemeTarget::_getThemeSettings($themeType,$themeId);

  $targets = array();
  foreach($result as $id) {
   $info = array();
   if (isset($settings["targets"][$id])) {
    $info = $settings["targets"][$id];
   }
   $info["id"] = $id;
   array_push($targets,$info);
  }
  return $targets;
 }

 function assignFrames($themeType,$themeId,$target_list) {
  return VThemeTarget::_assignFrames($themeType,$themeId,$target_list);
 }

} // end class

