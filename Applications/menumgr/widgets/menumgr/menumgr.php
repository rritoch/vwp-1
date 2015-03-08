<?php

VWP::RequireLibrary('vwp.ui.widget');

class Menumgr_Widget_Menumgr extends VWidget 
{

 function save_item_order($tpl = null) {
  $shellob =& VWP::getShell();
  
  $menu = $this->getModel('menu');
  if (!VWP::isWarning($menu)) {
   $cat = $shellob->getVar('menu');
   $order = $shellob->getVar('order');            
   $menu->load($cat);
   $result = $menu->reorderItems($order);

   if (VWP::isWarning($result)) {
    $result->ethrow();
   } else {
    VWP::addNotice('Items Moved!');
   }   
  }
  
  $this->edit_menu($tpl);
 }
 
 function item_move_event($tpl = null) {
  $shellob =& VWP::getShell();
  
  $menu = $this->getModel('menu');
  if (!VWP::isWarning($menu)) {
   $item = $shellob->getVar('move_id');
   $dir = $shellob->getVar('move_dir');
   $cat = $shellob->getVar('menu');            
   $menu->load($cat);
   $result = $menu->moveItem($item,$dir);
   if (VWP::isWarning($result)) {
    $result->ethrow();
   } else {
    VWP::addNotice('Item Moved!');
   }   
  }
  $this->edit_menu($tpl);
 }
 
 function delete_menus($tpl = null) {

  $shellob =& VWP::getShell();
    
  // Set categories
  
  $is_new = false;
  $this->assignRef('is_new',$is_new);

  $tmp = $shellob->getVar('id');
  
  $selected = array();
  
  if (is_array($tmp)) {
   foreach($tmp as $key=>$val) {
    if (strtolower($val) == "on") {
     array_push($selected,$key);
    }
   }  
  }
  
  if (count($selected) < 1) {
   VWP::raiseWarning("No menu's selected",get_class($this));   
  } else {
   $menus = $this->getModel('menus');   
   if (VWP::isWarning($menus)) {
    $menus->ethrow();
   } else {    
    $menus->deleteMenus($selected);
    VWP::addNotice("Menus deleted!");
   }
  }
  $this->display($tpl);
 }

 function save_menu($tpl = null) {
  
  $shellob =& VWP::getShell();
  
  $menu = $this->getModel('menu');
  if (VWP::isWarning($menu)) {
   $menu->ethrow();
   $menu = array();
  } else {
   $is_new = $shellob->getVar('is_new',false);
      
   $cat = $shellob->getVar('menu');
      
   if ($is_new) {

    if (empty($cat["id"])) {
     VWP::addNotice("Menu ID required!");
     $menu->bind($cat);
     return $this->new_menu($tpl);    
    }    
    
    if (!VWP::isWarning($menu->load($cat["id"]))) {
     VWP::addNotice("Menu ID in use!");
     $menu->bind($cat);
     return $this->new_menu($tpl);
    }
   } else {           
    $menu->load($cat["id"]);
   }
         
   $menu->bind($cat);
      
   $result = $menu->save();
   if (VWP::isWarning($result)) {
    $result->ethrow();
   } else {
    VWP::addNotice("Menu saved!");
   }
  }
  
  $this->edit_menu($tpl);
 }

 function refresh($tpl = null) {
  $shellob =& VWP::getShell();
  
  $is_new = $shellob->getVar('is_new',1);
  if ($is_new) return $this->new_menu($tpl);
  $cat = $shellob->getVar('cat');
  $id = $cat["id"];
  $id = array($id=>"on");
  $shellob->setVar("id",$id);
  $this->edit_menu($tpl);
 }

    function new_menu($tpl = null) 
    {

    	$shellob =& v()->shell();
    	
        // Set new category
  
        $is_new = true;
        $this->assignRef('is_new',$is_new);
        
        // Setup new Menu
  
        $menu = $this->getModel('menu');
        if (VWP::isWarning($menu)) {
            $menu->ethrow();
            $menu = array();
        } else {   
            $menu = $menu->getProperties();
        }
  
        $this->assignRef('menu',$menu);

        // Display layout
  
        $this->setLayout('edit');

        $screen =& $shellob->getScreen();
        $this->assignRef('screen',$screen);  
  
        parent::display($tpl);
    }

    function delete_items($tpl = null) 
    {
        $shellob =& VWP::getShell();
  
        $checked = $shellob->getVar('id');
        $selected = array();
        if (is_array($checked)) {
            foreach($checked as $key=>$val) {
                if (strtolower($val) == "on") {
                    array_push($selected,$key);
                }
            }
        }
        if (count($selected) < 1) {
            VWP::raiseWarning('No items selected!',get_class($this)."::delete_items");
        } else {
            $menu_id = $shellob->getVar('menu');
            $menu = $this->getModel('menu');
            if (VWP::isWarning($menu->load($menu_id))) {
                VWP::raiseWarning("Menu not found",get_class($this)."::delete_items");
                return $this->display();   
            }
            $result = $menu->deleteItems($selected);
            if (VWP::isWarning($result)) {
                $result->ethrow();
            } else {
                VWP::addNotice('Items deleted!');
            }  
        }
        $this->edit_menu($tpl);
    }

 function create_item($tpl = null) {
  $shellob =& VWP::getShell();
  
  $menu_id = $shellob->getVar('menu');
  $item = $shellob->getVar('item');
  $menu = $this->getModel('menu');
  if (VWP::isWarning($menu->load($menu_id))) {
   VWP::raiseWarning('Menu not found',get_class($this));
   return $this->display();   
  }
  
  $newItem = $menu->addItem($item);
  
  if (VWP::isWarning($newItem)) {
   $newItem->ethrow();
   return $this->new_item($tpl);
  }
  $shellob->setVar('id',array($newItem["id"] => "on")); 
  $this->edit_item($tpl);     

 }
 
 function save_item($tpl = null) {
  $shellob =& VWP::getShell();  
  $settings = null;
  
  $menu_id = $shellob->getVar('menu');
  if (!empty($menu_id)) {
   $menu = $this->getModel('menu');
   if (!VWP::isWarning($menu->load($menu_id))) {
    $settings = $shellob->getVar('item');
   }
  }
  if (is_array($settings)) {
   $checked = $shellob->getVar('id');
   $selected = array();
   if (is_array($checked)) {
    foreach($checked as $key=>$val) {
     if (strtolower($val) == "on") {
      array_push($selected,$key);
     }
    }
    if (count($selected) > 0) {
     $id = $selected[0];
     $params = $shellob->getVar('params');
     $result = $menu->updateItem($id,$settings,$params);
     if (VWP::isWarning($result)) {
      $result->ethrow();
     } else {
      VWP::addNotice('Item updated!');
     } 
    }
   }
  }
  $this->edit_item($tpl);
 }
 
    function edit_item($tpl = null) 
    {
        $shellob =& VWP::getShell();
 
        $menu = $this->getModel('menu');
        $menu_id = $shellob->getVar('menu');
        if (VWP::isWarning($menu->load($menu_id))) {
            VWP::raiseWarning('Menu not found!',get_class($this));
            return $this->display();   
        }
  
        $checked = $shellob->getVar('id');
        $selected = array();
        if (is_array($checked)) {
            foreach($checked as $key=>$val) {
                if (strtolower($val) == "on") {
                    array_push($selected,$key);
                }
            }      
        }
  
        if (count($selected) < 1) {
            VWP::raiseWarning('Menu item not found!',get_class($this));
            return $this->edit_menu($tpl);   
        }
  
        $menu->load($menu_id);  
        $item = $menu->getItem($selected[0]);

        if (VWP::isWarning($item)) {
            VWP::raiseWarning('Menu item not found!',get_class($this));
            return $this->edit_menu($tpl);   
        }
    
        $item_types = $menu->getTypes();
  
        if (!isset($item_types[$item["type"]])) {
            VWP::raiseWarning('Unsupported menu type!',get_class($this));
            return $this->edit_menu($tpl);   
        }
    
        if (isset($item["widget"])) {
            if (empty($item["ref"])) {      
                $params = $menu->getParams($item["widget"]);
            } else {
                $params = $menu->getParams($item["widget"],$item["ref"]);   
            }        
        } else {   
            $params = array();
        }
        
        $this->assignRef('params',$params);
        $app_list = $menu->getApplications();
  
        foreach($app_list as $key=>$val) {
            $parts = explode(".",$key);
            if (count($parts) > 1) {
                array_pop($parts);
                $parent = implode(".",$parts);
                if (isset($app_list[$parent])) {
                    $parent = $app_list[$parent];
                    $app_list[$key] =  $parent . " > " . $val;
                }
            }
        }
  
        $menu = $menu_id;
    
        $this->assignRef('item',$item);       
        $this->assignRef('menu',$menu);
        $this->assignRef('item_types',$item_types);
        $this->assignRef('app_list',$app_list);
        $this->setLayout('edit'.strtolower($item["type"]).'item');
  
        $screen = $shellob->getScreen();
        $this->assignRef('screen',$screen);
  
        parent::display($tpl);
    }

    function new_item($tpl = null) 
    {  
        $shellob =& VWP::getShell();
  
        $menu = $this->getModel('menu');
        $menu_id = $shellob->getVar('menu');
        if (VWP::isWarning($menu->load($menu_id))) {
            VWP::raiseWarning('Menu not found!',get_class($this));
            return $this->display();   
        }
        $item_types = $menu->getTypes();
  
        $menu = $menu_id;
         
        $this->assignRef('menu',$menu);
        $this->assignRef('item_types',$item_types);
        $this->setLayout('newitem');
        
        $screen = $shellob->getScreen();
        $this->assignRef('screen',$screen);
        parent::display($tpl);
    }

    function edit_menu($tpl = null) 
    {
        $shellob =& VWP::getShell();
        
        // Set category
  
        $is_new = false;
        $this->assignRef('is_new',$is_new);
  
        $tmp = $shellob->getVar('menu');
  
        if (!empty($tmp)) {
            if (is_array($tmp)) {
                $tmp = $tmp["id"];
            }
            $selected = array($tmp);   
        } else {
            $selected = $shellob->getChecked('id');   
        }
    
        if (count($selected) < 1) {
            VWP::raiseWarning("No menu selected",get_class($this));
            return $this->display($tpl);
        }
  
        $id = $selected[0];

        // Setup menu
  
        $menu = $this->getModel('menu');
        if (VWP::isWarning($menu)) {
            $menu->ethrow();
            $menu = array();
            $menu_item_list = array();
        } else {
            $result = $menu->load($id);
            if (VWP::isWarning($result)) {
                $result->ethrow();
                return $this->display($tpl);
            }
            $menu_item_list = $menu->getAllItems();
            $menu = $menu->getProperties();
        }
  
  
        $this->assignRef('menu_item_list',$menu_item_list);
        $this->assignRef('menu',$menu);

        // Display layout
  
        $this->setLayout('edit');

        $screen = $shellob->getScreen();
        $this->assignRef('screen',$screen);
        parent::display($tpl);
    }

 
    function display($tpl = null) 
    {

        $shellob =& v()->shell(); 
        // Get Categories
  
        $menus = $this->getModel('menus');
        $menu_list = array();    
        if (VWP::isWarning($menus)) {
            $menus->ethrow();
        } else {
            $menu_list = $menus->getAll();
        }  
        $this->assignRef('menu_list',$menu_list);
  
        $screen = $shellob->getScreen();
        $this->assignRef('screen',$screen);
        
        parent::display($tpl);
    } 
 
}