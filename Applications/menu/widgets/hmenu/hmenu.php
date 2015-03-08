<?php

VWP::RequireLibrary("vwp.ui.widget");
VWP::RequireLibrary("vwp.ui.menu");

class Menu_Widget_Hmenu extends VWidget {

 function display($tpl = null) {

  $menu = $this->getModel("menu");
    
  if (VWP::isWarning($menu)) {
   $menu->ethrow();
   return $menu;
  }

  $user =& VUser::getCurrent();
  $shellob =& $user->getShell();
  
  $menu_name = $shellob->getVar("name",false);
    
  $params =& $this->getParams();        
  
  if (VWP::isWarning($params)) {
   $params->ethrow();
  }
  
  $ref = $shellob->getVar('ref');
  
  if (!empty($ref)) {   
   $params->loadRef($ref);
  }
  if (empty($menu_name)) {
   if (isset($this->_params->menu)) {
    $menu_name = $this->_params->menu;
   }
  }
  
  $cur_menu = array('_items'=>array(),'title'=>'');
  
  if (empty($menu_name)) {      
   VWP::raiseWarning("Invalid menu requested!",get_class($this),null);   
  } else {   
   $c_menu = $menu->getMenu($menu_name);
   if (VWP::isWarning($c_menu)) {
    $c_menu->ethrow();
    $c_menu = array('_items'=>array(),'title'=>'');
   } else {

    $S_Id = array('menu'=>$menu_name);   
    $user =& VUser::getCurrent();
    if ($c_menu["default_security_policy"] == 'allow') {
     $granted = !$user->deny('Access menu',$this->getResourceID(),$S_Id);
    } else {
     $granted = $user->allow('Access menu',$this->getResourceID(),$S_Id);
    }
    if ($granted) {
     $cur_menu = $c_menu;
    }
   }
  }
  
  $this->assignRef('cur_menu',$cur_menu);
  $this->assignRef('menu_name',$menu_name);
  
  parent::display();
 }

}