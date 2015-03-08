<?php

/**
 * New Widget
 * 
 * @package    SomePackage
 * @subpackage Widgets
 * @author AuthorName <authorEmail> 
 * @copyright CopyrightNotice
 * @link Url   
 */

VWP::RequireLibrary('vwp.ui.widget');

/**
 * New Widget
 * 
 * @package    SomePackage
 * @subpackage Widgets
 * @author AuthorName <authorEmail> 
 * @copyright CopyrightNotice
 * @link Url   
 */ 

class Thememgr_Widget_Framemgr extends VWidget {

 function save_item_order($tpl = null) {
  $shellob =& VWP::getShell();
  
  $frames = $this->getModel('frames');    
  $selected = $shellob->getChecked('ck');
  $frame_list = $shellob->getVar('frame_list');   
  if ((count($selected) < 1) || (!isset($frame_list[$selected[0]]))) {
   VWP::raiseWarning('No frame selected',get_class($this));
   return $this->display($tpl);    
  }
  $frameId = $frame_list[$selected[0]]["id"];
      
  $order = $shellob->getVar('order');            
  
  $result = $frames->reorderItems($frameId,$order);

  if (VWP::isWarning($result)) {
   $result->ethrow();
  } else {
   VWP::addNotice('Items Moved!');
  }
         
  $this->edit_frame($tpl);
 }
 
 function item_move_event($tpl = null) {
  $shellob =& VWP::getShell();
  
  $frames = $this->getModel('frames');
  $selected = $shellob->getChecked('ck');
  $frame_list = $shellob->getVar('frame_list');   
  if ((count($selected) < 1) || (!isset($frame_list[$selected[0]]))) {
   VWP::raiseWarning('No frame selected',get_class($this));
   return $this->display($tpl);    
  }
  $frameId = $frame_list[$selected[0]]["id"];
    
  $item = $shellob->getVar('move_id');
  $dir = $shellob->getVar('move_dir');
                
  $result = $frames->moveItem($frameId,$item,$dir);
  if (VWP::isWarning($result)) {
   $result->ethrow();
  } else {
   VWP::addNotice('Item Moved!');
  }   
      
  $this->edit_frame($tpl);
 }
 
 function delete_frames($tpl = null) {
  $shellob =& VWP::getShell();
  
  $frames = $this->getModel('frames');
  $selected = $shellob->getChecked('ck');
  $frame_list = $shellob->getVar('frame_list');   
      
  if (count($selected) < 1) {
   VWP::raiseWarning("No frame's selected",get_class($this));   
  } else {    
   $kframes = array();
   foreach($selected as $ptr) {
    array_push($kframes,$frame_list[$ptr]["id"]);
   }
   $result = $frames->deleteFrames($kframes);
   if (VWP::isWarning($result)) {
    $result->ethrow();
   } else {
    VWP::addNotice("Frames deleted!");
   }   
  }
  $this->display($tpl);
 }

 /**
  * Update frame settings
  * @param mixed $tpl optional
  */   function save_frame($tpl = null) {

$shellob =& VWP::getShell();

  $frames = $this->getModel('frames');

  $selected = $shellob->getChecked('ck');
  $frame_list = $shellob->getVar('frame_list');
  if ((count($selected) < 1) || (!isset($frame_list[$selected[0]]))) {
   VWP::raiseWarning('No frame selected',get_class($this));
   return $this->display($tpl);    
  }  
  $frameId = $frame_list[$selected[0]]["id"];  

  $frame_info = $shellob->getVar('frame_info');
        
  $result = $frames->saveFrame($frameId,$frame_info);
  if (VWP::isWarning($result)) {
   $result->ethrow();
  } else {
   VWP::addNotice("Frame saved!");
  }
    
  $this->edit_frame($tpl);
    }

 function refresh_frame($tpl = null) {
  return $this->edit_frame($tpl);
 }

 function delete_items($tpl = null) {
  $shellob =& VWP::getShell();
  
  $frames = $this->getModel('frames');
  $selected = $shellob->getChecked('ck');
  $frame_list = $shellob->getVar('frame_list');   
  if ((count($selected) < 1) || (!isset($frame_list[$selected[0]]))) {
   VWP::raiseWarning('No frame selected',get_class($this));
   return $this->display($tpl);    
  }  
  $frameId = $frame_list[$selected[0]]["id"]; 
  
  $selected = $shellob->getChecked('id'); 

  if (count($selected) < 1) {
   VWP::raiseWarning('No items selected!',get_class($this)."::delete_items");
  } else {
   $result = $frames->deleteItems($frameId,$selected);
   if (VWP::isWarning($result)) {
    $result->ethrow();
   } else {
    VWP::addNotice('Items deleted!');
   }  
  }
  $this->edit_frame($tpl);
 }

 function create_item($tpl = null) {
  $shellob =& VWP::getShell();
  
  $frames = $this->getModel('frames');
  $selected = $shellob->getChecked('ck');
  $frame_list = $shellob->getVar('frame_list');   
  if ((count($selected) < 1) || (!isset($frame_list[$selected[0]]))) {
   VWP::raiseWarning('No frame selected',get_class($this));
   return $this->display($tpl);    
  }  
  $frameId = $frame_list[$selected[0]]["id"]; 
     
  $item = $shellob->getVar('item');
  
  $newItem = $frames->addItem($frameId,$item);
  
  $newItem++;
  
  if (VWP::isWarning($newItem)) {
   $newItem->ethrow();
   return $this->new_item();
  }
  VWP::addNotice('Item Created!');
  $val = array($newItem => "ON");
  
  
  $shellob->setVar('id',$val);
  $id = $shellob->getVar('id');
  
   
  $this->edit_item($tpl);
 }
 
 function save_item($tpl = null) {
  $shellob =& VWP::getShell();
  
  $frames = $this->getModel('frames');
  $selected = $shellob->getChecked('ck');
  $frame_list = $shellob->getVar('frame_list');   
  if ((count($selected) < 1) || (!isset($frame_list[$selected[0]]))) {
   VWP::raiseWarning('No frame selected',get_class($this));
   return $this->display($tpl);    
  }  
  $frameId = $frame_list[$selected[0]]["id"]; 
    
  $settings = $shellob->getVar('item');
  
  if (is_array($settings)) {
   $selected = $shellob->getChecked('id');
   
   if (count($selected) > 0) {
    $id = $selected[0];
    $params = $shellob->getVar('params');
    $result = $frames->updateItem($frameId,$id,$settings,$params);
    if (VWP::isWarning($result)) {
     $result->ethrow();
    } else {
     VWP::addNotice('Item updated!');
    } 
   }   
  }
  $this->edit_item($tpl);
 }
 
 function edit_item($tpl = null) {
  $shellob =& VWP::getShell();
  
  
  // setup tabs
  
  $current_widget = 'framemgr';
  $shellob->setVar('current_widget',$current_widget);
    
  $tabs = $this->getWidget("tabs");
    
  if (!VWP::isWarning($tabs)) {
   $tabs = $tabs->build();   
  }
   
  $tabs_foot = $this->getWidget("tabs");
    
  if (!VWP::isWarning($tabs_foot)) {
   $tabs_foot = $tabs_foot->build('foot');   
  }
  
  if (VWP::isWarning($tabs)) {
   $tabs->ethrow();
   $tabs = null;
  }

  if (VWP::isWarning($tabs_foot)) {
   $tabs_foot->ethrow();
   $tabs_foot = null;
  }  

  $this->assignRef('tabs',$tabs);
  $this->assignRef('tabs_foot',$tabs_foot);
 
  // Setup Widget

  $frames = $this->getModel('frames');
  $selected = $shellob->getChecked('ck');
  $frame_list = $shellob->getVar('frame_list');   
  if ((count($selected) < 1) || (!isset($frame_list[$selected[0]]))) {
   VWP::raiseWarning('No frame selected',get_class($this));
   return $this->display($tpl);    
  }  
  $frameId = $frame_list[$selected[0]]["id"]; 
   
  $selected = $shellob->getChecked('id');
  
  
  if (count($selected) < 1) {
   VWP::raiseWarning('Frame item not found!',get_class($this));
   return $this->edit_frame($tpl);   
  }
  
  $item_id = $selected[0];    
  $item = $frames->getItem($frameId,$item_id);

  if (VWP::isWarning($item)) {
   $item->ethrow();
   //VWP::raiseWarning('Frame item not found!',get_class($this));
   
   return $this->edit_frame($tpl);   
  }

  if (isset($item->widget)) {
   if (empty($item->ref)) {      
    $params = $frames->getParams($item->widget);
   } else {
    $params = $frames->getParams($item->widget,$item->ref);   
   }     
  } else {   
   $params = array();
  }
    
  $this->assignRef('params',$params);
  $widget_list = $frames->getWidgets();
  
  foreach($widget_list as $key=>$val) {
   $parts = explode(".",$key);
   if (count($parts) > 1) {
    array_pop($parts);
    $parent = implode(".",$parts);
    if (isset($widet_list[$parent])) {
     $parent = $widget_list[$parent];
     $widget_list[$key] =  $parent . " > " . $val;
    }
   }
  }
  
  $this->assignRef('item_id',$item_id);
    
  $this->assignRef('item',$item);       
  $this->assignRef('frameId',$frameId);  
  $this->assignRef('widget_list',$widget_list);
  $this->setLayout('edit_item');
  parent::display($tpl);
 }


 function new_item($tpl = null) {

  $shellob =& VWP::getShell();

  // setup tabs
  
  $current_widget = 'framemgr';
  $shellob->setVar('current_widget',$current_widget);
    
  $tabs = $this->getWidget("tabs");
    
  if (!VWP::isWarning($tabs)) {
   $tabs = $tabs->build();   
  }
   
  $tabs_foot = $this->getWidget("tabs");
    
  if (!VWP::isWarning($tabs_foot)) {
   $tabs_foot = $tabs_foot->build('foot');   
  }
  
  if (VWP::isWarning($tabs)) {
   $tabs->ethrow();
   $tabs = null;
  }

  if (VWP::isWarning($tabs_foot)) {
   $tabs_foot->ethrow();
   $tabs_foot = null;
  }  

  $this->assignRef('tabs',$tabs);
  $this->assignRef('tabs_foot',$tabs_foot);
 
  // Setup Widget
   
  $frames = $this->getModel('frames');
  $selected = $shellob->getChecked('ck');
  $frame_list = $shellob->getVar('frame_list');   
  if ((count($selected) < 1) || (!isset($frame_list[$selected[0]]))) {
   VWP::raiseWarning('No frame selected',get_class($this));
   return $this->display($tpl);    
  }  
  $frameId = $frame_list[$selected[0]]["id"]; 
  $widget_list = $frames->getWidgets();

  $default_item = array('widget'=>'','ref'=>'','visible'=>'1','disabled'=>'0');

  $item = $shellob->getVar('item',$default_item);
  $this->assignRef('item',$item);         
  $this->assignRef('widget_list',$widget_list);
  $this->assignRef('frameId',$frameId);  
  $this->setLayout('new_item');
  parent::display($tpl);
          }

 /**
  * Display Edit Frame Form
  */ 
  
  function edit_frame($tpl = null) {

  $shellob =& VWP::getShell();
  
  // setup tabs
  
  $current_widget = 'framemgr';
  $shellob->setVar('current_widget',$current_widget);
    
  $tabs = $this->getWidget("tabs");
    
  if (!VWP::isWarning($tabs)) {
   $tabs = $tabs->build();   
  }
   
  $tabs_foot = $this->getWidget("tabs");
    
  if (!VWP::isWarning($tabs_foot)) {
   $tabs_foot = $tabs_foot->build('foot');   
  }
  
  if (VWP::isWarning($tabs)) {
   $tabs->ethrow();
   $tabs = null;
  }

  if (VWP::isWarning($tabs_foot)) {
   $tabs_foot->ethrow();
   $tabs_foot = null;
  }  

  $this->assignRef('tabs',$tabs);
  $this->assignRef('tabs_foot',$tabs_foot);
 
  // Setup Widget

  $frames = $this->getModel('frames');
  $selected = $shellob->getChecked('ck');    
  $frame_list = $shellob->getVar('frame_list');   
  if ((count($selected) < 1) || (!isset($frame_list[$selected[0]]))) {
   VWP::raiseWarning('No frame selected',get_class($this));
   return $this->display($tpl);    
  }  
  $frameId = $frame_list[$selected[0]]["id"]; 
    
  $frame_item_list = $frames->getAllItems($frameId);
  
  $frame_info = $frames->getFrameProperties($frameId);
  if (VWP::isWarning($frame_info)) {
   $frame_info->ethrow();
   $frame_info = array();
  }
  
  $this->assignRef('frame_item_list',$frame_item_list);
  $this->assignRef('frame_info',$frame_info);
  $this->assignRef('frameId',$frameId);

  // Display layout
  
  $this->setLayout('edit_frame');      
  parent::display($tpl);
  }



 /**
  * Display Manage Frames Form
  *
  * @param mixed $tpl Optional
  * @access public
  */

 function display($tpl = null) {
  // setup tabs

  $shellob =& VWP::getShell();
  
  $current_widget = 'framemgr';
  $shellob->setVar('current_widget',$current_widget);
    
  $tabs = $this->getWidget("tabs");
    
  if (!VWP::isWarning($tabs)) {
   $tabs = $tabs->build();   
  }
   
  $tabs_foot = $this->getWidget("tabs");
    
  if (!VWP::isWarning($tabs_foot)) {
   $tabs_foot = $tabs_foot->build('foot');   
  }
  
  if (VWP::isWarning($tabs)) {
   $tabs->ethrow();
   $tabs = null;
  }

  if (VWP::isWarning($tabs_foot)) {
   $tabs_foot->ethrow();
   $tabs_foot = null;
  }  

  $this->assignRef('tabs',$tabs);
  $this->assignRef('tabs_foot',$tabs_foot);
 
  // Setup Widget
    
  $frames = $this->getModel('frames');
  $frame_list = $frames->getAll();
  if (VWP::isWarning($frame_list)) {
   $frame_list->ethrow();
   $frame_list = array();
  }

  $this->assignRef('frame_list',$frame_list);

  parent::display($tpl); }


/**
 * New Frame Form
 */

  function new_frame($tpl = null) {
   $shellob =& VWP::getShell();
  
  // setup tabs
  
  $current_widget = 'framemgr';
  $shellob->setVar('current_widget',$current_widget);
    
  $tabs = $this->getWidget("tabs");
    
  if (!VWP::isWarning($tabs)) {
   $tabs = $tabs->build();   
  }
   
  $tabs_foot = $this->getWidget("tabs");
    
  if (!VWP::isWarning($tabs_foot)) {
   $tabs_foot = $tabs_foot->build('foot');   
  }
  
  if (VWP::isWarning($tabs)) {
   $tabs->ethrow();
   $tabs = null;
  }

  if (VWP::isWarning($tabs_foot)) {
   $tabs_foot->ethrow();
   $tabs_foot = null;
  }  

  $this->assignRef('tabs',$tabs);
  $this->assignRef('tabs_foot',$tabs_foot);

  // Setup widget
  
  $new_frame_info = $shellob->getVar('new_frame_info',array('id'=>''));
  $this->setLayout('new_frame'); 
  $this->assignRef('new_frame_info',$new_frame_info);
  parent::display($tpl);  
  
  }

 /**
  * Refresh new frame form
  */
 function refresh_new_frame($tpl = null) {

  $shellob =& VWP::getShell();
  
  // setup tabs
  
  $current_widget = 'framemgr';
  $shellob->setVar('current_widget',$current_widget);
    
  $tabs = $this->getWidget("tabs");
    
  if (!VWP::isWarning($tabs)) {
   $tabs = $tabs->build();   
  }
   
  $tabs_foot = $this->getWidget("tabs");
    
  if (!VWP::isWarning($tabs_foot)) {
   $tabs_foot = $tabs_foot->build('foot');   
  }
  
  if (VWP::isWarning($tabs)) {
   $tabs->ethrow();
   $tabs = null;
  }

  if (VWP::isWarning($tabs_foot)) {
   $tabs_foot->ethrow();
   $tabs_foot = null;
  }  

  $this->assignRef('tabs',$tabs);
  $this->assignRef('tabs_foot',$tabs_foot);


  $new_frame_info = array('id'=>'');
  $this->setLayout('new_frame'); 
  $this->assignRef('new_frame_info',$new_frame_info);
  parent::display($tpl);   

 }

/**
 * Create a frame
 */

  function create_frame($tpl = null) {

   $shellob =& VWP::getShell();
   
   $new_frame_info = $shellob->getVar('new_frame_info');
   $frames = $this->getModel('frames');
   $result = $frames->createFrame($new_frame_info["id"]);
   if (VWP::isWarning($result)) {
    $result->ethrow();
    return $this->new_frame($tpl);  
   }
             
   $this->display($tpl); 
  }

 // end class

 function refresh_new_item($tpl = null) {
  $shellob =& VWP::getShell();
  
  $frames = $this->getModel('frames');
  $selected = $shellob->getChecked('ck');
  $frame_list = $shellob->getVar('frame_list');   
  if ((count($selected) < 1) || (!isset($frame_list[$selected[0]]))) {
   VWP::raiseWarning('No frame selected',get_class($this));
   return $this->display($tpl);    
  }  
  $frameId = $frame_list[$selected[0]]["id"]; 
  $app_list = $frames->getApplications();

  $item = array('widget'=>'','ref'=>'','visible'=>'1','disabled'=>'0');
  
  $this->assignRef('item',$item);         
  $this->assignRef('app_list',$app_list);
  $this->assignRef('frameId',$frameId);  
  $this->setLayout('new_item');
  parent::display($tpl);
           }} 
